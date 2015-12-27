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

class RaidAPIController extends APIController
{
    public function get($f3)
    {
        switch ($f3->get('PARAMS.action')) {
            case 'getraids':
                $from = date("Y-m-d H:i:s", $f3->get('GET.from')/1000);
                $to = date("Y-m-d H:i:s", $f3->get('GET.to')/1000);
                
                $raidsDB = $this->getDB('raids');
                $raids = $raidsDB->find(array('datetime>? AND datetime<?',
                                        $from, 
                                        $to)
                                       );
                
                $output= array();
                $output['success'] = 1;                
                
                foreach($raids as $raid) 
                {
                    $raidinfo = $f3->get('game.raids.'.$raid->raidtypeid);
                    $title = date("[H:i] ", strtotime($raid->datetime)).$raidinfo['name']." ".$raidinfo['players']." (".$raidinfo['difficulty'].")";
                    
                    $output['result'][] = array(
                        'id' => $raid->id,
                        'title' => $title,
                        'url' => '/raid/show/'.$raid->id,
                        'class' => "event-important",
                        'start' => strtotime($raid->datetime) . '000',
                        'end' => strtotime($raid->datetime)+3600 . '000',
                    );
                }
                
                $f3->set('json_output', $output);
                break;              
        }
    }
}
