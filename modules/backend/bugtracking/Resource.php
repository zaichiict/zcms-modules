<?php
$resource = [
    'author' => 'ZCMS Team',
    'authorUri' => 'http://www.zcms.com',
    'version' => '0.0.1',
    'uri' => 'http://www.zcms.com',
    'location' => 'backend',
    'class_name' => 'ZCMS\\Backend\\Bugtracking\\Module',
    'path' => '/backend/bugtracking/Module.php',
    'acl' => [
        [
            'controller' => 'index',
            'controller_name' => 'Bug Tracking',
            'rules' => [
                [
                    'action' => 'index',
                    'action_name' => 'Over view',
                    'sub_action' => 'myBugAndRequest,bugAndRequestNotifyToMe,view,viewDetail,changeStatusTracking',
                ],
                [
                    'action' => 'viewAllNotify',
                    'action_name' => 'View all bug /request tracking of System (All User Role)',
                    'sub_action' => '',
                ],
                [
                    'action' => 'list',
                    'action_name' => 'View all all bug /request tracking of Current User Role',
                    'sub_action' => '',
                ],
                [
                    'action' => 'new',
                    'action_name' => 'Add new bug/ request tracking',
                    'sub_action' => 'edit',
                ],
                [
                    'action' => 'process',
                    'action_name' => 'Update status bug / request tracking',
                    'sub_action' => '',
                ]
            ]
        ]
    ]
];