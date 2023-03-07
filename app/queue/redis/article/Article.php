<?php

namespace app\queue\redis\article;

use app\kernel\help\ChatgptHttp;
use App\model\Article as ModelArticle;
use App\model\Word as ModelWord;
use Webman\RedisQueue\Consumer;

class Article implements Consumer
{
    // 要消费的队列名
    public $queue = ModelArticle::ARTICLE_QUEUE;

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data)
    {   
        $title = ltrim($data['title']);
        $resultContent = ChatgptHttp::getMessage('article', '请根据 '.$title. ' 这个标题,生成一篇文章', $data['index']);
        $content = $resultContent['content'];
        // $index = $resultContent['index'];
        // $parent_message_id = $resultContent['parent_message_id'];
        // $resultNext = ChatgptHttp::getMessage(
        //     'article',
        //     '继续',
        //     $index,
        //     $parent_message_id
        // );
        // $content = $content. $resultNext['content'];

        // $index = $resultNext['index'];
        // $parent_message_id = $resultNext['parent_message_id'];
        
        // $resultTags = ChatgptHttp::getMessage(
        //     'article',
        //     '提取生成的文章中的keywords、description、tags', 
        //     $index,
        //     $parent_message_id
        // );
        // $tdk = explode(PHP_EOL, $resultTags['content']);
        // $keyword = $description = $tags = "";
        // foreach($tdk as $value) {
        //     if($value){
        //         $tdkArr = explode(':', $value);
        //         if($tdkArr[0] === 'Keywords' || $tdkArr[0] === '关键词'){
        //             $keyword = $tdkArr[1];
        //         }
        //         if($tdkArr[0] === 'Description' || $tdkArr[0] === '描述'){
        //             $description = $tdkArr[1];
        //         }
        //         if($tdkArr[0] === 'Tags' || $tdkArr[0] === '标签'){
        //             $tags = str_replace('、',',',$tdkArr[1]);
        //             $tags = str_replace('。','',$tags);
        //         }

        //     }
        // }

        $data = [
            'title' => $title,
            // 'content' => str_replace($title, '', $content),
            // 'keywords' => $keyword,
            // 'description' => $description,
            // 'tags' => $tags,
            'content' => $content,
            'keywords' => '',
            'description' => '',
            'tags' => '',
            'word' => $data['word'],
            'word_id' => $data['word_id'],
        ];

        ModelArticle::store($data);
        ModelWord::progress($data['word_id']);
    }
}