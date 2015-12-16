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

use Form\PlayerRegistrationForm;

class PlayerRegistrationController extends Controller
{
    /**
     * @var PlayerRegistrationForm
     */
    protected $playerRegistrationForm;
    
    /**
     * @var MailController
     */
    protected $mail;    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->playerRegistrationForm = new PlayerRegistrationForm;
        
        $this->mail = new MailController;
    }
    
    public function get($f3)
    {
      
    }
    
    public function post($f3)
    {
        $newPlayer = $this->getDB('players');        
        
        if ($this->playerRegistrationForm->isValid($f3->get('POST'))) {      
            $newPlayer->copyfrom('POST');
            $newPlayer->uniqueid = $this->generateUniqueID();
            $newPlayer->save();         
            
            // Send Mail
            $this->mail->setSubject("RP Player Registration")
                       ->sendMessage("registration", $newPlayer->email);
        } else {            
            $f3->set('SESSION.failedFields', array_keys($this->authForm->getFailedFields()));
            $f3->set('SESSION.errormsg', implode("<br>", $this->authForm->getFailedFields()));
            $f3->reroute('/playerregistration');
        }        
    }
    
    protected function generateUniqueID()
    {
        return md5(uniqid('player', true));
    }
}
