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

use DB\SQL\Mapper; 

class Controller
{    
    /**
     * @var DB\SQL
     */
    public $db;    
       
    public function __construct()
    {
        $this->db = \Base::instance()->get('DB');
    }
    
    public function afterroute() 
    {        
        $classPath = explode('\\', get_called_class());

        $calledClass = substr(end($classPath), 0, strpos(end($classPath), "Controller"));
        
        $action = \Base::instance()->get('VERB');
        
        \Base::instance()->set('content',$calledClass.'\\'.strtolower($action).'.phtml');
        
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
