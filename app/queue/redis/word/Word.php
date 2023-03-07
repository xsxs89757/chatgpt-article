<?php

namespace app\queue\redis\word;

use app\kernel\help\ChatgptHttp;
use App\model\Article as ModelArticle;
use App\model\Word as ModelWord;
use Webman\RedisQueue\Redis;
use Webman\RedisQueue\Consumer;

class Word implements Consumer
{
    // 要消费的队列名
    public $queue = ModelWord::WORD_QUEUE;

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data)
    {   
        $result = ChatgptHttp::getMessage('title','请根据 '.$data['word']. ' 这个词,生成'. ModelWord::ONE_WORD_TO_ARTICLE_COUNT . '个适合SEO的标题', $data['index']);
        $content = explode(PHP_EOL, $result['content']);
        $api = config('app.chatgpt_cluster_article');
        $index = 0;
        foreach($content as $value){
            if($value){
                $index = ($index + 1) % count($api);
                $title = explode('.', $value);
                Redis::send(ModelArticle::ARTICLE_QUEUE, [
                    'index' => $index, 
                    'title' => $title[1],
                    'word' => $data['word'],
                    'word_id' => $data['task_id']
                ]);
            }
        }
    }
}