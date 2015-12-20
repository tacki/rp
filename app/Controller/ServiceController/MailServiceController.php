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
namespace Controller\ServiceController;

use SMTP;

class MailServiceController
{
    protected $smtp;
    
    public function __construct()
    {
        $this->smtp = $this->initSMTP();
    }
    
    /**
     * @param string $subject
     * @return \Controller\MailController
     */
    public function setSubject($subject)
    {
        $this->smtp->set('Subject', $subject);
        return $this;
    }
    
    /**
     * @param string $template Message Template
     * @param string $to Receiver Mailaddress
     */
    public function sendMessage($template, $to) 
    {
        $f3 = \Base::instance();
        
        $this->smtp->set('From', $f3->get('mail.FROM'));
        $this->smtp->set('To', $to);
        
        $message =  \View::instance()->render('MailView\\'.$template.".phtml");
        
        echo "sending message $message from {$f3->get('mail.FROM')} to $to<br>";
        //$this->smtp->send($message);
    }
    
    /**
     * @return SMTP
     */
    protected function initSMTP()
    {
        $f3 = \Base::instance();
        
        $host = $f3->get('mail.HOST')?$f3->get('mail.HOST'):"localhost";
        $port = $f3->get('mail.PORT')?$f3->get('mail.PORT'):"25";
        $encryption = $f3->get('mail.ENCRYPTION')?$f3->get('mail.ENCRYPTION'):NULL;
        $user = $f3->get('mail.USER')?$f3->get('mail.USER'):NULL;
        $pass = $f3->get('mail.PASS')?$f3->get('mail.PASS'):NULL;
        
        return new SMTP($host, $port, $encryption, $user, $pass);
    }
}
