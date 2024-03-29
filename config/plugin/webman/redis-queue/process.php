<?php
return [
    'article'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 5, // 可以设置多进程同时消费
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis/article/'
        ]
    ],
    'word'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 2, // 可以设置多进程同时消费
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis/word/'
        ]
    ],
    'prompt' => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 30, // 可以设置多进程同时消费
        'constructor' => [
            // 消费者类目录
            'consumer_dir' => app_path() . '/queue/redis/prompt/'
        ]
    ]
];