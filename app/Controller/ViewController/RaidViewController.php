<?php
/*
 * Copyright (C) 2015 Markus Schlegel
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
namespace Controller\ViewController;

use Controller\ServiceController\MailServiceController;
use Form\RaidCreateForm;
use Form\RaidEditForm;
use Form\RaidRegistrationForm;



class RaidViewController extends ViewController
{
    /**
     * @var RaidCreateForm
     */
    protected $raidCreateForm;
    
    /**
     * @var RaidEditForm
     */
    protected $raidEditForm;    
    /**
     * @var RaidRegistrationForm
     */
    protected $raidRegistrationForm;        
    
    /**
     * @var MailServiceController 
     */
    protected $mail;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->raidCreateForm = new RaidCreateForm;
        $this->raidEditForm = new RaidEditForm;
        $this->raidRegistrationForm = new RaidRegistrationForm;
        $this->mail = new MailServiceController;
    }
    
    public function get($f3)
    {
        switch ($f3->get('PARAMS.action')) {
            default:   
            case 'list':
                $f3->set('headTitle', 'Planned Raids');

                // get Raidinfo
                $raidsView = $this->getDB('v_raids');
                $raidlist = $raidsView->find('datetime > now()');

                $f3->set('raidlist', $raidlist);                   
                break;
            
            case 'show':
                // Header
                $raidsView = $this->getDB('v_raids');
                $raidinfo = $raidsView->findone('id='.$f3->get('PARAMS.raidid'));

                $f3->set('headTitle', $raidinfo->name);
                $f3->set('headSubTitle', $raidinfo->players." Spieler (".$raidinfo->difficulty.")");        

                // get registration info
                $registrationsView = $this->getDB('v_registrations');
                $registrations = $registrationsView->find('raidid='.$f3->get('PARAMS.raidid'), array('order' => 'participation DESC'));        

                $f3->set('registrations', $registrations); 
                break;
            
            case 'registration':
                $raidsView = $this->getDB('v_raids');
                $raidinfo = $raidsView->findone('id='.$f3->get('PARAMS.raidid'));
                
                $f3->set('headTitle', $raidinfo->name);
                $f3->set('headSubTitle', $raidinfo->players." Spieler (".$raidinfo->difficulty.")");                 
                $f3->set('raidinfo', $raidinfo);
                
                $characterView = $this->getDB('v_characters');
                $characterlist = $characterView->find('userid='.$f3->get('SESSION.user.id'), array('order' => 'armorclass DESC'));                
                
                $f3->set('characterlist', $characterlist);
                break;
            
            case 'create':
                $raidtypes = $this->getDB('raidtypes');
                $result = $raidtypes->find("enabled=1");

                $f3->set('raidtypes', $result);    
                break;
            
            case 'edit':
                $raidsView = $this->getDB('v_raids');
                $raid = $raidsView->findone('id = '.$f3->get('PARAMS.raidid'));
                
                $f3->set('raid', $raid);
                break;
            
            case 'delete':
                $raidsView = $this->getDB('v_raids');
                $raid = $raidsView->findone('id = '.$f3->get('PARAMS.raidid'));
                
                $f3->set('raid', $raid);                
                break;
        }
    }
    
    public function post($f3)
    {
        switch ($f3->get('PARAMS.action')) {
            case 'registration':
                $registrationDB = $this->getDB('registrations'); 
                $registration = $registrationDB->findone('raidid = '.$f3->get('POST.raidid').
                                                         ' AND characterid = '.$f3->get('POST.characterid'));

                if ($this->raidRegistrationForm->isValid($f3->get('POST')) && !$registration->dry()) {      
                    $registration->participation = $f3->get('POST.participation');
                    $registration->text = $f3->get('POST.text');
                    $registration->save();     
                    
                    // Remove remaining Characters from the Registration-List
                    $characterView = $this->getDB('v_characters');
                    $remainingCharacters = $characterView->find('userid = '.$f3->get('SESSION.user.id').
                                                                ' AND id != '.$f3->get('POST.characterid'));   
                    
                    foreach ($remainingCharacters as $character) {
                        $registrationDB->erase('raidid = '.$f3->get('POST.raidid').
                                               ' AND characterid = '.$character->id);
                    }
                                                          
                    

                } elseif ($registration->dry()) {
                    $f3->set('SESSION.errormsg', 'Raid not found!');
                    $f3->reroute('/raid/registration/'.$f3->get('PARAMS.raidid'));
                } else {            
                    $f3->set('SESSION.failedFields', array_keys($this->raidRegistrationForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->raidRegistrationForm->getFailedFields()));
                    $f3->reroute('/raid/registration/'.$f3->get('PARAMS.raidid'));
                } 
                break;
            
            case 'create':
                if ($this->raidCreateForm->isValid($f3->get('POST'))) {
                    $raid = $this->getDB('raids');
                    
                    $datetime = date("Y-m-d H:i:s", strtotime($f3->get('POST.date').' '.$f3->get('POST.time')));                    

                    $raid->copyFrom('POST');        
                    $raid->datetime = $datetime;
                    $raid->creationdate = date("Y-m-d H:i:s");
                    $raid->save();    
                    
                    // Add available Characters to Raid
                    $raidView = $this->getDB('v_raids');
                    $newRaid = $raidView->findone('id = '. $raid->id);
                    
                    // Find Characters suitable for this Raid
                    $charactersDB = $this->getDB('characters');
                    $characters = $charactersDB->find('armorclass >= '. $newRaid->armorclass);
                    
                    $useridlist = array();
                    $registration= $this->getDB('registrations');
                    foreach ($characters as $character) {
                        $registration->raidid = $newRaid->id;
                        $registration->characterid = $character->id;
                        $registration->save();
                        $registration->reset();
                        
                        $useridlist[] = $character->userid;
                    }

                    // Send Mail to user
                    $f3->set('newRaid', $newRaid);
                    $usersDB = $this->getDB('users');
                    
                    foreach (array_unique($useridlist) as $userid) {
                        $user = $usersDB->findone('id = '. $userid);

                        $this->mail->sendMessage('raidnotification', $user->email);
                    }
                    
                    die;
                    
                    $this->mail->sendMessage($template, $to);
                    
                    
                    $f3->reroute('/raid/list');
                } else {
                    $f3->set('SESSION.failedFields', array_keys($this->raidCreateForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->raidCreateForm->getFailedFields()));
                    $f3->reroute('/raid/create');
                }
                break;
            
            case 'edit':
                if ($this->raidEditForm->isValid($f3->get('POST'))) {
                    $raidDB = $this->getDB('raids');
                    $raid = $raidDB->findone('id = '.$f3->get('POST.id'));
                    
                    $datetime = date("Y-m-d H:i:s", strtotime($f3->get('POST.date').' '.$f3->get('POST.time')));

                    $raid->datetime = $datetime;
                    $raid->save();    
                    
                    $f3->reroute('/raid/list');
                } else {
                    $f3->set('SESSION.failedFields', array_keys($this->raidEditForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->raidEditForm->getFailedFields()));
                    $f3->reroute('/raid/create');
                }
                break;     
                
            case 'delete':
                $raidsDB = $this->getDB('raids');
                $raid = $raidsDB->findone('id = '.$f3->get('PARAMS.raidid'));

                $raid->erase();

                $f3->reroute('/raid/list');                
                break;
        }        
    }   
}
