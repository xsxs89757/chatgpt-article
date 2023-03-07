<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

 return [
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => env('DB_HOST'),
            'port'        => env('DB_PORT'),
            'database'    => env('DB_DATABASE'),
            'username'    => env('DB_USERNAME'),
            'password'    => env('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => env('DB_CHARSET','utf8mb4'),
            'collation'   => env('DB_COLLATION','utf8mb4_0900_ai_ci'),
            'prefix'      => env('DB_PREFIX',''),
            'strict'      => true,
            'engine'      => null,
        ]
    ]
];
