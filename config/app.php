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

use support\Request;

return [
    'debug' => (bool)env('APP_DEBUG', false),
    'error_reporting' => E_ALL,
    'default_timezone' => 'Asia/Shanghai',
    'request_class' => Request::class,
    'public_path' => base_path() . DIRECTORY_SEPARATOR . 'public',
    'runtime_path' => base_path(false) . DIRECTORY_SEPARATOR . 'runtime',
    'controller_suffix' => 'Controller',
    'controller_reuse' => false,

    'chatgpt_cluster_title' =>[
        'http://43.153.18.225:8792/chatgpt'
    ],
    'chatgpt_cluster_article' =>[
        'http://43.153.18.225:8792/chatgpt'
    ],
    'chatgpt_cluster' => 'http://43.153.18.225:8792/chatgpt'
    // 'chatgpt_cluster_title' => [
    //     'http://43.153.90.245:8788/chatgpt',
    //     'http://43.153.23.119:8788/chatgpt',
    // ],
    // 'chatgpt_cluster_article' => [
    //     'http://43.153.40.79:8788/chatgpt',
    //     'http://43.153.31.5:8788/chatgpt',
    //     'http://43.135.160.97:8788/chatgpt',
    //     'http://43.153.8.112:8788/chatgpt',
    //     'http://43.153.83.180:8788/chatgpt',
    //     'http://43.153.81.192:8788/chatgpt',
    //     'http://43.135.153.30:8788/chatgpt',
    //     'http://43.153.50.81:8788/chatgpt',
    // ]
];
