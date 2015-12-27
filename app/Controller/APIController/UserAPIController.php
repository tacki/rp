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

class UserAPIController extends APIController
{
    public function get($f3)
    {
        switch ($f3->get('PARAMS.action')) {
            case 'getcharacters':
                $characterlist = array();
                $characterDB = $this->getDB('characters');
                $characters = $characterDB->select('name',
                                                   array('userid=?',$f3->get('PARAMS.userid')));
                
                foreach ($characters as $character) {
                    $characterlist[] = $character->name;
                }
                
                $f3->set('json_output', $characterlist);
                break;              
        }
    }
}
