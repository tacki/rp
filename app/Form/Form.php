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

abstract class Form
{
    /**
     * @var array
     */
    protected $failedFields = array();
    
    /**
     * @returns array
     */
    abstract public function getFormFields();
    
    /**
     * @param array $post
     * @return boolean
     */
    public function isValid($post)
    {
        if (!is_array($post)) {
            return false;
        }
        
        foreach ($post as $name=>$value) {
            $this->check($name, $value);
        }
        
        if (empty($this->failedFields)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @return array
     */
    public function getFailedFields()
    {
        return $this->failedFields;
    }
    
    /**
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function check($name, $value)
    {
        $formFields = $this->getFormFields();
        
        if (!isset($formFields[$name])) {
            $this->failedFields[$name] = "undefined";
            return false;
        }
               
        if ($formFields[$name]['required'] && !(bool)$value) {
            $this->failedFields[$name] = "invalid data (empty field)";
            return false;
        }        
        
        switch ($formFields[$name]['type']) {
            case 'submit':
            case 'string':
                if (!is_string($value)) {
                    $this->failedFields[$name] = "invalid data (not a string)";
                    return false;
                }
                break;
            case 'bool':
                if (!filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== NULL) {
                    $this->failedFields[$name] = "invalid data (not a bool)";
                    return false;                    
                }
                break;                
            case 'numeric':
                if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->failedFields[$name] = "invalid data (not a number)";
                    return false;                    
                }
                break;
            case 'date':
                if (!$this->validateDate($value, 'd.m.Y')) {
                    $this->failedFields[$name] = "invalid data (not a date)";
                    return false;
                }
                break;
            case 'time':
                if (!$this->validateDate($value, 'H:i')) {
                    $this->failedFields[$name] = "invalid data (not a time)";
                    return false;
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->failedFields[$name] = "invalid data (not a email)";
                    return false;                    
                }
                break;
        }
        
        return true;
    }
    
    /**
     * @param string $date
     * @param strng $format
     * @return bool
     */
    protected function validateDate($date, $format)
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
