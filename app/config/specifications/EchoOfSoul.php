<?php
return array (
    'name' => 'Echo of Soul',
    'version' => '1.0',
    'lang' => 'german',
    'armorclasses' => array(
        1 => 790,
        2 => 862,
        3 => 934,
        4 => 1006,
        5 => 1078,
        6 => 1135,
    ),
    'subclasschangeable' => true,
    'classes' => array(
        1 => array (
            'name' => 'Krieger',
            'subclasses' => array (
                1 => array(
                    'name' => 'Berserker',
                    'role' => 1,
                ),
                2 => array(
                    'name' => 'Beschützer',
                    'role' => 2,                    
                ),
            ),
        ),
        2 => array (
            'name' => 'Schurke',
            'subclasses' => array (
                1 => array(
                    'name' => 'Duellant',
                    'role' => 1,                   
                ),
                2 => array(
                    'name' => 'Asssasine',
                    'role' => 1,       
                ),                
            ),  
        ),
        3 => array(
            'name' => 'Hüter',
            'subclasses' => array (
                1 => array(
                    'name' => 'Sturmrufer',
                    'role' => 1,                    
                ),
                2 => array(
                    'name' => 'Erdrufer',
                    'role' => 2,
                ),                
            ),
        ),
        4 => array(
            'name' => 'Magier',
            'subclasses' => array (
                1 => array(
                    'name' => 'Pyromant',
                    'role' => 1,
                ),
                2 => array(
                    'name' => 'Kryomant',
                    'role' => 1, 
                ),                
            ), 
        ),
        5 => array(
            'name' => 'Waldläufer',
            'subclasses' => array (
                1 => array(
                    'name' => 'Schütze',
                    'role' => 1,                    
                ),
                2 => array(
                    'name' => 'Barde',
                    'role' => 1,                                        
                ),                
            ),  
        ),
        6 => array(
            'name' => 'Okkultist',
            'subclasses' => array (
                1 => array(
                    'name' => 'Peiniger',
                    'role' => 1,
                ),
                2 => array(
                    'name' => 'Manipulator',
                    'role' => 1,                                        
                ),                   
            ),   
        ),
    ),
    'raids' => array(
        1 => array(
            'name' => 'Dryade',
            'players' => 10,
            'difficulty' => 'Normal',            
            'armorclass' => 1,        
        ),
        2 => array(
            'name' => 'Dryade',
            'players' => 20,
            'difficulty' => 'Normal',            
            'armorclass' => 1,        
        ),   
        3 => array(
            'name' => 'Dryade',
            'players' => 10,
            'difficulty' => 'Schwer',            
            'armorclass' => 2,        
        ),    
        4 => array(
            'name' => 'Dryade',
            'players' => 20,
            'difficulty' => 'Schwer',            
            'armorclass' => 2,        
        ),   
        5 => array(
            'name' => 'Kranheim',
            'players' => 10,
            'difficulty' => 'Normal',            
            'armorclass' => 3,        
        ),  
        6 => array(
            'name' => 'Kranheim',
            'players' => 20,
            'difficulty' => 'Normal',            
            'armorclass' => 3,        
        ), 
        7 => array(
            'name' => 'Kranheim',
            'players' => 10,
            'difficulty' => 'Schwer',            
            'armorclass' => 4,        
        ),     
        8 => array(
            'name' => 'Kranheim',
            'players' => 20,
            'difficulty' => 'Schwer',            
            'armorclass' => 4,        
        ),  
        9 => array(
            'name' => 'Dunkle Festung',
            'players' => 10,
            'difficulty' => 'Normal',            
            'armorclass' => 5,        
        ),   
        10 => array(
            'name' => 'Dunkle Festung',
            'players' => 20,
            'difficulty' => 'Normal',            
            'armorclass' => 5,        
        ), 
        11 => array(
            'name' => 'Dunkle Festung',
            'players' => 10,
            'difficulty' => 'Schwer',            
            'armorclass' => 6,        
        ),   
        12 => array(
            'name' => 'Dunkle Festung',
            'players' => 20,
            'difficulty' => 'Schwer',            
            'armorclass' => 6,        
        ),         
    ),
    'roles' => array(
        1 => 'DD',
        2 => 'Tank',
    ),
);

