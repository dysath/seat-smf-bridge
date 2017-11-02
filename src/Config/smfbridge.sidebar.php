<?php
/**
 * User: Denngarr B'tarn <ed.stafford@gmail.com>
 * Date: 10/27/2017
 */

return [
    'forum' => [
	'name'          => 'Forum',
        'icon'          => 'fa-clipboard',
        'route'         => 'smfbridge.login',
        'route_segment' => 'smfbridge',
        'permission'    => 'forum.view'
    ],
    'smfbridge' => [
        'name'          => 'SMF Bridge',
        'icon'          => 'fa-comments',
        'route_segment' => 'smfbridge',
        'entries' => [
            [
                'name'  => 'SMF Bridge Settings',
                'icon'  => 'fa-cogs',
                'route' => 'smfbridge.configuration',
                'permission' => 'smfbridge.setup'
            ],

            [
                'name'  => 'SMF Bridge Sync User',
                'icon'  => 'fa-cogs',
                'route' => 'smfbridge.syncusers',
                'permission' => 'smfbridge.setup'
            ]
        ],
        'permission' => 'smfbridge.view'
    ]
];


