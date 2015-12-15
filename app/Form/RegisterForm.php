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
namespace Form;

class RegisterForm extends Form
{
    public function getFormFields() 
    {
        return array(
            'raidid' => array(
                'type' => 'numeric',
                'required' => true,
            ),
            'playerid' => array(
                'type' => 'numeric',
                'required' => true,
            ),
            'participation' => array(
                'type' => 'numeric',
                'required' => true,
            ),
            'text' => array(
                'type' => 'string',
                'required' => false,
            ),            
            'submit' => array(
                'type' => 'submit',
                'required' => true,
            ),            
        );
    }
}
