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

use Form\AuthForm;

class AuthController extends Controller
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
        $f3->set('headTitle', 'Login');     
    }    
    
    public function post($f3)                       
    {        
        if ($this->authForm->isValid($f3->get('POST'))) {
            $auth = new \Auth($this->getDB('users'), array('id'=>'username', 'pw'=>'password'));
            $crypt = \Bcrypt::instance();

            if ($auth->login($f3->get('POST.username'), $crypt->hash($f3->get('POST.password', $f3->get('crypt.SALT'))))) {
                //echo "ok!";
            } else {
                $f3->set('SESSION.errormsg', "Wrong Username or Password");
                $f3->reroute('/auth');
            }
 
        } else {
            $f3->set('SESSION.failedFields', array_flip($this->createForm->getFailedFields()));
            $f3->set('SESSION.errormsg', implode("<br>", $this->createForm->getFailedFields()));
            $f3->reroute('/auth');
        }
    } 
}
