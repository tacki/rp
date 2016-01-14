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

use Form\CharacterRegistrationForm;
use Form\CharacterEditForm;

class CharacterViewController extends ViewController
{
    /**
     * @var CharacterRegistrationForm
     */
    protected $characterRegistrationForm;
    
    /**
     * @var CharacterEditForm
     */
    protected $characterEditForm;    
    
    public function __construct()
    {
        parent::__construct();
        
        $this->characterRegistrationForm = new CharacterRegistrationForm;
        $this->characterEditForm = new CharacterEditForm;
    }
    
    public function get($f3)
    {
        if($f3->get('PARAMS.characterid') && !$this->editOrViewAllowed($f3->get('PARAMS.characterid'))) {
            $f3->set('SESSION.errormsg', 'Nicht erlaubt!');
            $f3->reroute('/character/list');
        }
        
        switch ($f3->get('PARAMS.action')) {
            case 'list':
                $f3->set('headTitle', 'Charakter Liste');
                
                $characterDB = $this->getDB('characters');
                $characters = $characterDB->find(array('userid=?',$f3->get('SESSION.user.id')));
                
                $f3->set('characters', $characters);
                break;

            case 'create':
                break;  

            case 'edit':
                $characterDB = $this->getDB('characters');
                $character = $characterDB->findone(array('id=?',$f3->get('PARAMS.characterid')));
                
                $f3->set('character', $character);                               
                break;   
            
            case 'delete':
                $characterDB = $this->getDB('characters');
                $character = $characterDB->findone(array('id=?',$f3->get('PARAMS.characterid')));
                
                $f3->set('character', $character);
                break;   
        }
    }
    
    public function post($f3)
    {
        if($f3->get('PARAMS.characterid') && !$this->editOrViewAllowed($f3->get('PARAMS.characterid'))) {
            $f3->set('SESSION.errormsg', 'Nicht erlaubt!');
            $f3->reroute('/character/list');
        }        
        
        switch ($f3->get('PARAMS.action')) {
            case 'create':        
                $newCharacter = $this->getDB('characters');        

                if ($this->characterRegistrationForm->isValid($f3->get('POST'))) {    
                    $characterDB = $this->getDB('characters');
                    if ($characterDB->findone(array('name=?',$f3->get('POST.name')))) {
                        $f3->set('SESSION.errormsg', 'Charaktername ungültig oder bereits registriert!');
                        $f3->reroute('/character/create');
                    }                    
                    
                    $newCharacter->copyfrom('POST');
                    $newCharacter->userid = $f3->get('SESSION.user.id');
                    $newCharacter->save();   
                    
                    $f3->reroute('/character/list');
                } else {            
                    $f3->set('SESSION.failedFields', array_keys($this->characterRegistrationForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->characterRegistrationForm->getFailedFields()));
                    $f3->reroute('/character/create');
                }       
                break;
                
            case 'edit':
                $characterid = $f3->get('PARAMS.characterid');
                    
                $characterDB = $this->getDB('characters');
                $character = $characterDB->findone(array('id=?',$characterid));
                
                if ($this->characterEditForm->isValid($f3->get('POST'))) { 
                    if ($f3->get('POST.role')) {
                        // Role is optional
                        $character->role = $f3->get('POST.role');
                    }
                    $character->armorclass = $f3->get('POST.armorclass');
                    $character->save();       
                    
                    $f3->reroute('/character/list');
                } else {            
                    $f3->set('SESSION.failedFields', array_keys($this->characterEditForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->characterEditForm->getFailedFields()));
                    $f3->reroute('/character/edit/'.$characterid);
                }       
                break;   
                
            case 'delete':
                $characterDB = $this->getDB('characters');
                $character = $characterDB->findone(array('id=?',$f3->get('PARAMS.characterid')));

                $character->erase();

                $f3->reroute('/character/list');
                break;
        }
    }
    
    public function editOrViewAllowed($characterid)
    {
        $f3 = \Base::instance();
        
        $characterDB = $this->getDB('characters');
        
        return (bool)($characterDB->findone(array('userid=? AND id=?',$f3->get('SESSION.user.id'),$characterid)));
    }
}
