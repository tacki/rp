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
namespace Controller;

use Form\RegisterForm;

class RegisterController extends Controller
{
    /**
     * @var CreateForm
     */
    protected $registerForm;
    
    /**
     * @var MailController
     */
    protected $mail;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->registerForm = new RegisterForm;
        $this->mail = new MailController;
    }    
    
    public function get($f3) 
    {
        $f3->set('headTitle', 'Für Raid anmelden');
        
        // Header
        $raidsView = $this->getDB('v_raids');
        $raidinfo = $raidsView->findone('id='.$f3->get('PARAMS.raidid'));
        
        $f3->set('headTitle', $raidinfo->name);
        $f3->set('headSubTitle', $raidinfo->players." Spieler (".$raidinfo->difficulty.")");            
        
        // get Playerinfo
        $players = $this->getDB('players');
        $player = $players->findone('uniqueid=\''.$f3->get('PARAMS.uniqueid').'\'');
       
        $f3->set('raidid', $raidinfo->id);
        $f3->set('playerid', $player->id);
        $f3->set('datetime', $raidinfo->datetime);
    }
    
    public function post($f3)
    {
        $registration = $this->getDB('registrations');        
        
        if ($this->registerForm->isValid($f3->get('POST'))) {      
            $registration->copyfrom('POST');
            $registration->save();         
        } else {            
            var_dump ($this->registerForm->getFailedFields());
            $f3->set('SESSION.failedFields', array_flip($this->registerForm->getFailedFields()));            
            $f3->set('SESSION.errormsg', implode("<br>", $this->registerForm->getFailedFields()));
        }
    }
}
