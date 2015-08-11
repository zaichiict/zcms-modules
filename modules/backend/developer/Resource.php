<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
    'class_name' => 'ZCMS\\Backend\\Developer\\Module',
    'path' => '/backend/developer/Module.php',
    'acl' => [
        [
            'controller' => 'index',
            'rules' =>
                [
                    [
                        'action' => 'index',
                        'sub_action' => 'new'
                    ]
                ]
        ]
    ]
];