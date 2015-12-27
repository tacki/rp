<?php
/*
 * Copyright (C) 2015 Markus Schlegel <g42@gmx.net>
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
use Form\RaidInviteForm;



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
     * @var RaidInviteForm
     */
    protected $raidInviteForm;       
    
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
        $this->raidInviteForm = new RaidInviteForm;
        $this->mail = new MailServiceController;
    }
    
    public function get($f3)
    {
        switch ($f3->get('PARAMS.action')) {
            case 'list':
                $f3->set('headTitle', 'Geplante Raids');

                // get Raidinfo
                $raidsDB = $this->getDB('raids');
                $raidlist = $raidsDB->find(array(),
                                           array('order' => 'datetime ASC'));

                $f3->set('raidlist', $raidlist);                   
                break;
            
            case 'show':                
                $raidsDB = $this->getDB('raids');
                $raid = $raidsDB->findone(array('id=?',$f3->get('PARAMS.raidid')));
                
                // Header
                $raidinfo = $f3->get('game.raids.'.$raid->raidtypeid);
                
                $f3->set('headTitle', $raidinfo['name']);
                $f3->set('headSubTitle', $raidinfo['players']." Spieler (".$raidinfo['difficulty'].")");        

                // get registration info
                $registrationsView = $this->getDB('v_registrations');
                $registrations = $registrationsView->find(array('raidid=?',
                                                                $f3->get('PARAMS.raidid')),
                                                          array('order' => 'participation ASC'));        

                $f3->set('registrations', $registrations); 
                break;
            
            case 'registration':
                $raidsDB = $this->getDB('raids');
                $raid = $raidsDB->findone(array('id=?',$f3->get('PARAMS.raidid')));
                
                $f3->set('raid', $raid);
                
                // Header
                $raidinfo = $f3->get('game.raids.'.$raid->raidtypeid);
                
                $f3->set('headTitle', $raidinfo['name']);
                $f3->set('headSubTitle', $raidinfo['players']." Spieler (".$raidinfo['difficulty'].")");                                 
                
                $charactersDB = $this->getDB('characters');
                $characterlist = $charactersDB->find(array('userid=?',
                                                           $f3->get('SESSION.user.id')), 
                                                     array('order' => 'armorclass DESC'));                
                
                $f3->set('characterlist', $characterlist);
                break;
            
            case 'create':  
                break;
            
            case 'edit':
            case 'delete':
                $raidsDB = $this->getDB('raids');
                $raid = $raidsDB->findone(array('id=?',$f3->get('PARAMS.raidid')));
                
                $f3->set('raid', $raid);
                break;
            
            case 'invite':
                $raidsDB = $this->getDB('raids');
                $raid = $raidsDB->findone(array('id=?',$f3->get('PARAMS.raidid')));
                
                $f3->set('raid', $raid);
                
                $usersDB = $this->getDB('users');
                $userlist = $usersDB->find();
                
                $f3->set('userlist', $userlist);
                break;            
        }
    }
    
    public function post($f3)
    {
        switch ($f3->get('PARAMS.action')) {
            case 'registration':
                $registrationDB = $this->getDB('registrations'); 
                $registration = $registrationDB->findone(array('raidid=? AND characterid=?',
                                                                $f3->get('POST.raidid'),
                                                                $f3->get('POST.characterid')));
                
                if ($this->raidRegistrationForm->isValid($f3->get('POST')) && $registration) {      

                    $registration->participation = $f3->get('POST.participation');
                    $registration->comment = $f3->get('POST.comment');
                    $registration->role = $f3->get('POST.role');
                    $registration->save();     
                    
                    // Remove remaining Characters from the Registration-List
                    //$characterDB = $this->getDB('characters');
                    //$remainingCharacters = $characterDB->find(array('userid=? AND id!=?',
                    //                                                 $f3->get('SESSION.user.id'),
                    //                                                 $f3->get('POST.characterid')));   
                    
                    //foreach ($remainingCharacters as $character) {
                    //    $registrationDB->erase(array('raidid=? AND characterid=?',
                    //                                 $f3->get('POST.raidid'),
                    //                                 $character->id));
                    //}
                    $f3->reroute('/raid/show/'.$f3->get('POST.raidid'));
                } elseif (!$registration) {
                    $f3->set('SESSION.errormsg', 'Raideinladung nicht gefunden!');
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
                    
                    // Find Characters suitable for this Raid and add them
                    $raidinfo = $f3->get('game.raids.'.$raid->raidtypeid);
                    
                    $charactersDB = $this->getDB('characters');
                    $characters = $charactersDB->find(array('armorclass>=?',$raidinfo['armorclass']));
                    
                    $useridlist = array();
                    $registration= $this->getDB('registrations');
                    foreach ($characters as $character) {
                        $registration->raidid = $raid->id;
                        $registration->characterid = $character->id;
                        $registration->save();
                        $registration->reset();
                        
                        $useridlist[] = $character->userid;
                    }
                    
                    // Send Mail to user
                    $f3->set('raid', $raid);
                    $raidinfo = $f3->get('game.raids.'.$f3->get('raid')->raidtypeid);
                    $raidname = $raidinfo['name']." ".$raidinfo['players']." (".$raidinfo['difficulty'].")";       
                    
                    $usersDB = $this->getDB('users');
                    
                    $receiverlist = array();
                    foreach (array_unique($useridlist) as $userid) {
                        $user = $usersDB->findone(array('id=?',$userid));

                        $receiverlist[] = $user->email;
                    }

                    $this->mail->setSubject('Raideinladung '.$raidname)->sendMessage('raidnotification', $receiverlist);
                    
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
                    $raid = $raidDB->findone(array('id=?',$f3->get('POST.id')));
                    
                    $datetime = date("Y-m-d H:i:s", strtotime($f3->get('POST.date').' '.$f3->get('POST.time')));

                    $raid->datetime = $datetime;
                    $raid->save();    
                    
                    $f3->reroute('/raid/list');
                } else {
                    $f3->set('SESSION.failedFields', array_keys($this->raidEditForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->raidEditForm->getFailedFields()));
                    $f3->reroute('/raid/list');
                }
                break;     
                
            case 'delete':
                $raidsDB = $this->getDB('raids');
                $raidsDB->erase(array('id=?',$f3->get('PARAMS.raidid')));
                
                $registrationsDB = $this->getDB('registrations');
                $registrationsDB->erase(array('id=?',$f3->get('PARAMS.raidid')));
                
                $f3->reroute('/raid/list');                
                break;
            
            case 'invite':
                if ($this->raidInviteForm->isValid($f3->get('POST'))) {
                    $usersDB = $this->getDB('users');
                    $user = $usersDB->findone(array('id=?',$f3->get('POST.userid')));
                    
                    $raidsDB = $this->getDB('raids');
                    $raid = $raidsDB->findone(array('id=?',$f3->get('PARAMS.raidid')));
                    
                    $f3->set('raid', $raid);
                    $raidinfo = $f3->get('game.raids.'.$f3->get('raid')->raidtypeid);
                    $raidname = $raidinfo['name']." ".$raidinfo['players']." (".$raidinfo['difficulty'].")";                    

                    // Send Mail
                    $this->mail->setSubject('Raideinladung '.$raidname)->sendMessage('raidnotification', array($user->email));   
                    
                    $f3->reroute('/raid/list');
                } else {
                    $f3->set('SESSION.failedFields', array_keys($this->raidInviteForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->raidInviteForm->getFailedFields()));
                    $f3->reroute('/raid/invite/'.$f3->get('PARAMS.raidid'));
                }                
                break;
        }        
    }   
}
