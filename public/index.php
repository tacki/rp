<?php
    // load f3
    $f3 = require('../vendor/f3/base.php');

    // autoloader
    $f3->set('AUTOLOAD','../app/');
    
    // load configs
    $f3->config('../app/config/config.ini', false);
    $f3->config('../app/config/routing.ini', true);
    
    // load Game specification
    $spec = include '../app/config/specifications/'.$f3->get('SPECIFICATION').'.php';
    $f3->set('game', $spec);
    
    // Database init
    $connstr = $f3->get('database.TYPE').":".
               "host=".$f3->get('database.HOST').";".
               "port=".$f3->get('database.PORT').";".
               "dbname=".$f3->get('database.DBNAME');
    $f3->set('DB', new DB\SQL(
                $connstr,
                $f3->get('database.DBUSER'),
                $f3->get('database.DBPASS')
            ));

    // run
    $f3->run();

