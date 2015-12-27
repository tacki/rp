<?php
/*
 * Copyright (C) 2015 Markus Schlegel <g42@gmx.net>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Controller\APIController;

use DB\SQL\Mapper; 
use Controller\ServiceController\SecurityServiceController;

class APIController 
{
    /**
     * @var DB\SQL
     */
    protected $db; 
    
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
        if (!$this->security->checkParams()) {
            die('Bad Parameter!');
        }
        
        if (!$this->security->isPublicRoute() && !$f3->get('SESSION.user')) {
            // Not a public page and user is not logged in
            die('Nicht authentifiziert!');
        }
        
        if ($this->security->isRaidleaderRoute() && !$f3->get('SESSION.user.raidleader')) {
            // A raidlead-page and user is not a raidleader
            die('Nicht erlaubt!');
        }        
    }

    public function afterroute() 
    {        
        $f3 = \Base::instance();
        
        // Render Output
        echo json_encode($f3->get('json_output'));
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
