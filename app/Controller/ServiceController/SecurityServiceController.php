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
namespace Controller\ServiceController;

class SecurityServiceController 
{
    /**
     * Check if the F3 Params are correct
     * @return boolean
     */
    public function checkParams()
    {
        $f3 = \Base::instance();
        
        // params checking
        foreach($f3->get('PARAMS') as $param => $value) {
            if ($f3->get('params.'.$param) && !preg_match("/^".$f3->get('params.'.$param)."$/", $value)) {
                // Bad Parameter - possible hacking attempt
                return false;                
            }
        }        
        
        return true;
    }
    
    /**
     * Check if the current Route is a public one (accessable without a login)
     * @return boolean
     */
    public function isPublicRoute()
    {
        $f3 = \Base::instance();
        
        // public page checking
        foreach($f3->get('access.ALLOWPUBLIC') as $publicroute) {
            if (preg_match("/^".$publicroute."$/", $f3->get('PATTERN')) ||
                preg_match("/^".$publicroute."$/", $f3->get('PATH'))) {
                return true;
            }
        }  
        
        return false;
    }
    
    /**
     * Check if the current Route is a Raidleader-restricted
     * @return boolean
     */
    public function isRaidleaderRoute()
    {
        $f3 = \Base::instance();
        
        // raidleader route checking
        foreach($f3->get('access.ALLOWRAIDLEAD') as $raidleadroute) {
            if (preg_match("/^".$raidleadroute."$/", $f3->get('PATTERN')) ||
                preg_match("/^".$raidleadroute."$/", $f3->get('PATH'))) {
                return true;
            }
        }   
        
        return false;
    }
}
