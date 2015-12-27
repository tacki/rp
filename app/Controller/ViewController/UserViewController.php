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
use Form\UserRegistrationForm;
use Form\UserEditForm;

class UserViewController extends ViewController
{
    /**
     * @var UserRegistrationForm
     */
    protected $userRegistrationForm;
    
    /**
     * @var UserEditorm
     */
    protected $userEditForm;  
    
    /**
     * @var MailServiceController
     */
    protected $mail; 
    
    public function __construct()
    {
        parent::__construct();
        
        $this->userRegistrationForm = new UserRegistrationForm;
        $this->userEditForm = new UserEditForm;
        $this->mail = new MailServiceController;
    }
    
    public function get($f3)
    {
        if($f3->get('PARAMS.userid') && !$this->editOrViewAllowed($f3->get('PARAMS.userid'))) {
            $f3->set('SESSION.errormsg', 'Not Allowed!');
            $f3->reroute('/user/edit/'.$f3->get('SESSION.user.id'));
        }
        
        switch ($f3->get('PARAMS.action')) {
            default:
            case 'list':
                $usersDB = $this->getDB('users');
                $users = $usersDB->find();
                
                $f3->set('users', $users);
                
                $charactersDB = $this->getDB('characters');
                $characters = $charactersDB->find();
                
                $f3->set('characters', $characters);                
                break;

            case 'create':

                break;  
            
            case 'mailvalidation':
                $userdb = $this->getDB('users');  
                $user = $userdb->findone(array('id=?',$f3->get('PARAMS.userid')));


                if ($user->dry() || $user->mailvalidation !== $f3->get('PARAMS.validationkey')) {
                    $f3->set('SESSION.errormsg', 'Mailvalidierung ungültig! Account bereits aktiviert?');
                    $f3->reroute('/auth');                
                } else {
                    $user->mailvalidation = '';
                    $user->save();
                    $f3->set('SESSION.successmsg', 'Account aktiviert! Bitte einloggen.');
                    $f3->reroute('/auth');
                }             
                break;

            case 'edit':
                $usersDB = $this->getDB('users');
                $user = $usersDB->findone(array('id=?',$f3->get('PARAMS.userid')));
                
                $f3->set('user', $user);            
                break;   
            
            case 'delete':
                $usersDB = $this->getDB('users');
                $user = $usersDB->findone(array('id=?',$f3->get('PARAMS.userid')));
                
                $f3->set('user', $user);
                
                $charactersDB = $this->getDB('characters');
                $characters = $charactersDB->find(array('userid=?',$f3->get('PARAMS.userid')));
                
                $f3->set('characters', $characters);
                break;                
        }
    }
    
    public function post($f3)
    {
        if($f3->get('PARAMS.userid') && !$this->editOrViewAllowed($f3->get('PARAMS.userid'))) {
            $f3->set('SESSION.errormsg', 'Nicht erlaubt!');
            $f3->reroute('/user/edit/'.$f3->get('SESSION.user.id'));
        }        
        
        switch ($f3->get('PARAMS.action')) {
            case 'create':        
                $newUser = $this->getDB('users');  
                $crypt = \Bcrypt::instance();

                if ($this->userRegistrationForm->isValid($f3->get('POST'))) {   

                    $userDB = $this->getDB('users');
                    if ($userDB->findone(array('email=?',$f3->get('POST.email')))) {
                        $f3->set('SESSION.errormsg', 'EMail ungültig oder bereits registriert!');
                        $f3->reroute('/user/create');
                    }
                    if ($f3->get('POST.password') !== $f3->get('POST.password2')) {
                        $f3->set('SESSION.failedFields', array('password', 'password2'));
                        $f3->set('SESSION.errormsg', 'Passwörter stimmen nicht überein');
                        $f3->reroute('/user/create');                        
                    }                    

                    $newUser->copyfrom('POST');
                    $newUser->password = $crypt->hash($f3->get('POST.password',$f3->get('crypt.SALT')));
                    $newUser->mailvalidation = $this->generateMailValidationKey();
                    $newUser->save();         

                    // Send Mail
                    $f3->set('newuser', $newUser);
                    $this->mail->setSubject("RaidPlaner Registration")
                               ->sendMessage("registration", array($newUser->email));
                    
                    $f3->set('SESSION.successmsg', 'Benutzer erstellt! Prüfe deine EMails um die EMail-Adresse zu bestätigen.');
                    //$f3->reroute('/auth');
                } else {            
                    $f3->set('SESSION.failedFields', array_keys($this->userRegistrationForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->userRegistrationForm->getFailedFields()));
                    $f3->reroute('/user/create');
                }
                break;
                
            case 'edit':
                $userDB = $this->getDB('users');
                $user = $userDB->findone(array('id=',$f3->get('PARAMS.userid')));
                
                $crypt = \Bcrypt::instance();
                
                if ($this->userEditForm->isValid($f3->get('POST'))) {  
                    if ($f3->get('POST.password') !== $f3->get('POST.password2')) {
                        $f3->set('SESSION.failedFields', array('password', 'password2'));
                        $f3->set('SESSION.errormsg', 'Passwörter stimmen nicht überein');
                        $f3->reroute('/user/edit/'.$f3->get('PARAMS.userid'));                        
                    }
                    
                    $user->password = $crypt->hash($f3->get('POST.password',$f3->get('crypt.SALT')));
                    $user->save();       
                    $f3->set('SESSION.successmsg', 'Daten geändert!');
                    $f3->reroute('/user/edit/'.$f3->get('PARAMS.userid'));
                } else {            
                    $f3->set('SESSION.failedFields', array_keys($this->userEditForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->userEditForm->getFailedFields()));
                    $f3->reroute('/user/edit/'.$f3->get('PARAMS.userid'));
                }       
                break;   
                
            case 'delete':
                $charactersDB = $this->getDB('characters');
                $charactersDB->erase(array('userid=?',$f3->get('PARAMS.userid')));
                
                $usersDB = $this->getDB('users');
                $usersDB->erase(array('id=?',$f3->get('PARAMS.userid')));

                $f3->reroute('/user/list');
                break;
        }
    }
    
    public function editOrViewAllowed($userid)
    {
        $f3 = \Base::instance();
        
        if ($f3->get('SESSION.user.admin')) {
            return true;
        } elseif ($userid == $f3->get('SESSION.user.id') && $f3->get('PARAMS.action') == "edit") {
            return true;
        } elseif ($f3->get('PARAMS.action') == 'mailvalidation') {
            return true;
        }
        
        return false;
    }
    
    /**
     * @return string 32-char long random String
     */
    protected function generateMailValidationKey()
    {
        return md5(uniqid('mailvalidation', true));
    }    
}
