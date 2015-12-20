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

use DB\SQL\Mapper; 

class ViewController
{    
    /**
     * @var DB\SQL
     */
    public $db;    
       
    public function __construct()
    {
        $this->db = \Base::instance()->get('DB');
    }
    
    public function beforeroute()
    {
        $f3 = \Base::instance();
        $isPublicRoute = false;
        $isRaidleadRoute = false;
        
        // access control
        // public pages
        foreach($f3->get('access.ALLOWPUBLIC') as $publicroute) {
            if (preg_match("/^".$publicroute."$/", $f3->get('PATTERN')) ||
                preg_match("/^".$publicroute."$/", $f3->get('PATH'))) {
                $isPublicRoute = true;
                break;
            }
        }
        
        if (!$isPublicRoute && !$f3->get('SESSION.user')) {
            // Not a public page and user is not logged in
            $f3->set('SESSION.errormsg', 'Not Authenticated!');
            $f3->reroute('/auth/reroute?url='.urlencode($f3->get('PATH')));
        }

        // raidlead pages
        foreach($f3->get('access.ALLOWRAIDLEAD') as $raidleadroute) {
            if (preg_match("/^".$raidleadroute."$/", $f3->get('PATTERN')) ||
                preg_match("/^".$raidleadroute."$/", $f3->get('PATH'))) {
                $isRaidleadRoute = true;
                break;
            }
        }        
        
        if ($isRaidleadRoute && !$f3->get('SESSION.user.raidleader')) {
            // A raidlead-page and user is not a raidleader
            $f3->set('SESSION.errormsg', 'Not Allowed!');
            $f3->reroute('/raid/list');
        }
    }
    
    public function afterroute() 
    {        
        $f3 = \Base::instance();
        
        $classPath = explode('\\', get_called_class());

        $calledClass = substr(end($classPath), 0, strpos(end($classPath), "Controller"));
        
        $action = $f3->get('VERB');
        
        $f3->set('content',$calledClass.'\\'.strtolower($action).'.phtml');
        
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
