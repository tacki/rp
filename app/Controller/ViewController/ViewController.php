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

use DB\SQL\Mapper; 
use Controller\ServiceController\SecurityServiceController;

class ViewController
{    
    /**
     * @var DB\SQL
     */
    public $db;    
    
    /**
     * @var SecurityServiceController 
     */
    protected $security;
    
    public function __construct()
    {
        $this->db = \Base::instance()->get('DB');
        $this->security = new SecurityServiceController;
    }
    
    public function beforeroute()
    {
        $f3 = \Base::instance();
        
        // params checking
        if(!$this->security->checkParams()) {
            // Bad Parameter - possible hacking attempt
            $f3->set('SESSION.errormsg', 'Bad Parameter!');
            $f3->reroute('/');                
        }
        
        // access control
        // public routes
        if (!$this->security->isPublicRoute() && !$f3->get('SESSION.user')) {
            // Not a public route and user is not logged in
            $f3->set('SESSION.errormsg', 'Nicht authentifiziert!');
            $f3->reroute('/auth/reroute?url='.urlencode($f3->get('PATH')));
        }
        
        // raidleader routes
        if ($this->security->isRaidleaderRoute() && !$f3->get('SESSION.user.raidleader')) {
            // A raidlead-page and user is not a raidleader
            $f3->set('SESSION.errormsg', 'Nicht erlaubt!');
            $f3->reroute('/raid/list');
        }
    }
    
    public function afterroute() 
    {        
        $f3 = \Base::instance();
        
        $classPath = explode('\\', get_called_class());

        $calledClass = substr(end($classPath), 0, strpos(end($classPath), "Controller"));
        
        $f3->set('content',$calledClass.'\\'.$calledClass.'.phtml');
        
        // Render HTML layout
        echo \View::instance()->render('layout.phtml');
    }   
    
    /**
     * @param string $database
     * @return Mapper
     */
    public function getDB($database)
    {
        return new Mapper($this->db, $database);
    }
}
