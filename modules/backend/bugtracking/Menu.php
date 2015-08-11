<?php
$menu = [
    'menu_name' => 'm_bug_tracking_menu_index',
    'link' => '/admin/bugtracking/index/',
    'rule' => 'bugtracking|index|index',
    'icon_class' => 'fa fa-bug',
    'items' => [
        [
            'menu_name' => 'Overview',
            'link' => '/admin/bugtracking/index/',
            'rule' => 'bugtracking|index|index',
            'icon_class' => 'fa fa-newspaper-o'
        ],
        [
            'menu_name' => 'View All',
            'link' => '/admin/bugtracking/index/list/',
            'rule' => 'bugtracking|index|list',
            'icon_class' => 'fa fa-list'
        ],
        [
            'menu_name' => 'My Bug / Request',
            'link' => '/admin/bugtracking/index/mybugandrequest/',
            'rule' => 'bugtracking|index|index',
            'icon_class' => 'fa fa-align-left'
        ],
        [
            'menu_name' => 'Notify To Me',
            'link' => '/admin/bugtracking/index/bugandrequestnotifytome/',
            'rule' => 'bugtracking|index|index',
            'icon_class' => 'fa fa-rotate-right'
        ],
        [
            'menu_name' => 'New Bug / Request',
            'link' => '/admin/bugtracking/index/new/',
            'rule' => 'bugtracking|index|new',
            'icon_class' => 'fa fa-plus-square'
        ],
    ],
];
