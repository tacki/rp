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

use Form\AuthForm;

class AuthViewController extends ViewController
{
    /**
     * @var AuthForm
     */
    protected $authForm;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->authForm = new AuthForm;
    }
    
    public function get($f3)
    {
        switch ($f3->get('PARAMS.action')) {
            case 'logout':
                $f3->clear('SESSION.user');
                $f3->reroute('/');                
                break;
            
        }
    }    
    
    public function post($f3)                       
    {        
        if ($this->authForm->isValid($f3->get('POST'))) {
            $users = $this->getDB('users');
            $user = $users->findOne(array('email=?',$f3->get('POST.email')));
            $crypt = \Bcrypt::instance();
            
            if ($user->mailvalidation) {
                $f3->set('SESSION.errormsg', 'Account nicht aktiviert!');
                $f3->reroute('/auth');                
            } elseif ($crypt->verify($f3->get('POST.password'), $user->password)) {     
                $f3->set('SESSION.user', array('id' => $user->id,
                                               'email' => $user->email,
                                               'raidleader' => $user->raidleader,
                                               'admin' => $user->admin));
                if($f3->get('GET.url')) {
                    $f3->reroute($f3->get('GET.url'));
                } else {
                    $f3->reroute('/');
                }
            } else {
                $f3->set('SESSION.errormsg', 'EMail oder Passwort falsch');
                $f3->reroute('/auth');
            }
 
        } else {
            $f3->set('SESSION.failedFields', array_keys($this->authForm->getFailedFields()));
            $f3->set('SESSION.errormsg', implode("<br>", $this->authForm->getFailedFields()));
            $f3->reroute('/auth');
        }
    } 
}
