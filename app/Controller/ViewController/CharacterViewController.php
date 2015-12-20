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
            $f3->set('SESSION.errormsg', 'Not Allowed!');
            $f3->reroute('/character/list');
        }
        
        switch ($f3->get('PARAMS.action')) {
            default:
            case 'list':
                $characterView = $this->getDB('v_characters');
                $characters = $characterView->find('userid = '.$f3->get('SESSION.user.id'));
                
                $f3->set('characters', $characters);
                break;

            case 'create':
                $characterTypesDB = $this->getDB('charactertypes');
                $characterTypes = $characterTypesDB->find();

                $f3->set('characterTypes', $characterTypes);
                
                $raidtypeDB = $this->getDB('raidtypes');
                $armorClasses = $raidtypeDB->select("DISTINCT armorclass", null, array('order'=>'armorclass ASC'));
                
                $f3->set('armorClasses', $armorClasses);
                break;  

            case 'edit':
                $characterView = $this->getDB('v_characters');
                $character = $characterView->findone('id = '.$f3->get('PARAMS.characterid'));
                
                $f3->set('character', $character);
                
                $raidtypeDB = $this->getDB('raidtypes');
                $armorClasses = $raidtypeDB->select("DISTINCT armorclass", null, array('order'=>'armorclass ASC'));
                
                $f3->set('armorClasses', $armorClasses);                
                break;   
            
            case 'delete':
                $characterView = $this->getDB('v_characters');
                $character = $characterView->findone('id = '.$f3->get('PARAMS.characterid'));
                
                $f3->set('character', $character);
                break;                
        }
    }
    
    public function post($f3)
    {
        if($f3->get('PARAMS.characterid') && !$this->editOrViewAllowed($f3->get('PARAMS.characterid'))) {
            $f3->set('SESSION.errormsg', 'Not Allowed!');
            $f3->reroute('/character/list');
        }        
        
        switch ($f3->get('PARAMS.action')) {
            case 'create':        
                $newCharacter = $this->getDB('characters');        

                if ($this->characterRegistrationForm->isValid($f3->get('POST'))) {      
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
                $character = $characterDB->findone('id = '.$characterid);
                
                if ($this->characterEditForm->isValid($f3->get('POST'))) {      
                    $character->armorclass = $f3->get('POST.armorclass');
                    $character->save();       
                    
                    $f3->reroute('/character/list');
                } else {            
                    $f3->set('SESSION.failedFields', array_keys($this->characterEditForm->getFailedFields()));
                    $f3->set('SESSION.errormsg', implode("<br>", $this->characterEditForm->getFailedFields()));
                    $f3->reroute('/character/list');
                }       
                break;   
                
            case 'delete':
                $characterDB = $this->getDB('characters');
                $character = $characterDB->findone('id = '.$f3->get('PARAMS.characterid'));

                $character->erase();

                $f3->reroute('/character/list');
                break;
        }
    }
    
    public function editOrViewAllowed($characterid)
    {
        $f3 = \Base::instance();
        
        $characterDB = $this->getDB('characters');
        
        return (bool)($characterDB->findone('userid = '.$f3->get('SESSION.user.id').' AND id = '.$characterid));
    }
}
