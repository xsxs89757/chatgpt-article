<?php

namespace app\queue\redis\prompt;

use app\kernel\help\CgptHttp;
use App\model\Article as ModelArticle;
use App\model\Word as ModelWord;
use support\Log;
use Webman\RedisQueue\Consumer;

class Prompt implements Consumer
{
    // 要消费的队列名
    public $queue = ModelWord::WORD_PROMPT_QUEUE;

    // 连接名，对应 plugin/webman/redis-queue/redis.php 里的连接`
    public $connection = 'default';

    // 消费
    public function consume($data)
    {   
        try{
            $resultContent = CgptHttp::getMessage($data['word']);
            if(!isset($resultContent['content'])){
                Log::debug('RESULT_ERROR:', $resultContent);
            }
            $content = $resultContent['content'];
            $parent_message_id = $resultContent['parent_message_id'];
            $resultTags = CgptHttp::getMessage(
                '根据上面生成的文章生成title并提取keywords、description、tags', 
                $parent_message_id
            );
            if(!isset($resultTags['content'])){
                Log::debug('RESULT_TAGS_ERROR:', $resultTags);
            }
            $tdk = explode(PHP_EOL, $resultTags['content']);
            $title = $keyword = $description = $tags = "";
            foreach($tdk as $value) {
                if($value){
                    $tdkArr = explode(':', $value);
                    if($tdkArr[0] === 'Title' || $tdkArr[0] === '标题'){
                        $title = $tdkArr[1];
                    }
                    if($tdkArr[0] === 'Keywords' || $tdkArr[0] === '关键词'){
                        $keyword = $tdkArr[1];
                    }
                    if($tdkArr[0] === 'Description' || $tdkArr[0] === '描述'){
                        $description = $tdkArr[1];
                    }
                    if($tdkArr[0] === 'Tags' || $tdkArr[0] === '标签'){
                        $tags = str_replace('、',',',$tdkArr[1]);
                        $tags = str_replace('。','',$tags);
                    }

                }
            }
            $data = [
                'title' => $title,
                'keywords' => $keyword,
                'description' => $description,
                'tags' => $tags,
                'content' => $content,
                'word' => $data['y_word'],
                'word_id' => $data['task_id'],
            ];

            ModelArticle::store($data);
            ModelWord::progress($data['word_id']);
        }catch(\Throwable $e){
            Log::debug('ERROR: ' . $e->getMessage());
        }

        
    }
}