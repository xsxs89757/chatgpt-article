<?php
namespace process;

use Error;
use Carbon\Carbon;
use app\model\Word;
use Workerman\Timer;
use App\model\Timing;
use App\model\Article;
use GuzzleHttp\Client;
use Webman\RedisQueue\Redis;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use support\Log;

class Task
{

    public function onWorkerStart()
    {
        // 每隔10秒检查一次
        Timer::add(10, function(){
            $this->patrolTask();
        });

        // 每隔1分钟更新查看进度
        Timer::add(60, function(){
            try{
                $result = Word::where('status', Word::START)->firstOrFail();
                if($result){
                    if($result->count_article === $result->progress){
                        $result->status = Word::OVER;
                        $result->save();
                    }
                    if ($result->progress > 0 && 
                        !Redis::exists('{redis-queue}-waiting'.Word::WORD_PROMPT_QUEUE) && 
                        !Redis::exists('{redis-queue}-delayed')
                    ){
                        $result->status = Word::OVER;
                        $result->save();
                    }
                }
            }catch(ModelNotFoundException){

            }catch(\Throwable $e){
                var_dump('Pross:'.$e->getMessage());
            }
        });

        
        // 每10秒执行一次
        Timer::add(1, function(){
            try{
                $this->sendArticle();
            }catch(\Throwable $e){
                var_dump($e->getMessage());
            }
        });
    }

    /**
     * 巡查任务
     *
     * @return void
     */
    // private function patrolTask(): void
    // {
    //     $result = Word::where('status', Word::STARTING)->first();
    //         try{
    //             if($result){
    //                 Word::startTask($result->id);
    //                 $localFileName = public_path() . '/' . $result->filename;
    //                 $word = [];
    //                 $handle = fopen($localFileName, 'r');
    //                 while (($data = fgetcsv($handle)) !== false) {
    //                     $data[0] = convertStr($data[0]);
    //                     if ($data[0] == '关键词') {
    //                         continue;
    //                     }
    //                     $word[] = $data[0];
    //                 }
    //                 $data = [
    //                     'count_word' => count($word),
    //                     'count_article' => Word::ONE_WORD_TO_ARTICLE_COUNT * count($word),
    //                     'status' => Word::START
    //                 ];
    //                 $api = config('app.chatgpt_cluster_title');
    //                 $index = 0;
    //                 foreach($word as $w){
    //                     $index = ($index + 1) % count($api);
    //                     $redisData = [
    //                         'index' => $index, 
    //                         'task_id' => $result->id,
    //                         'word' => $w
    //                     ];
    //                     Redis::send(Word::WORD_QUEUE, $redisData);
    //                 }
    //                 Word::store($data, $result->id);
    //             }
                
    //         }catch(\Throwable $e){
    //             var_dump($e->getMessage());
    //             Word::startError($result->id);
    //         }
    // }

    private function patrolTask(): void
    {
        $result = Word::where('status', Word::STARTING)->first();
        try{
            if($result){
                Word::startTask($result->id);
                $localFileName = public_path() . '/' . $result->filename;
                $word = [];
                $handle = fopen($localFileName, 'r');
                while(! feof($handle)){
                    $order=["\r\n","\n","\r"];
                    $replace='';
                    $word[] = str_replace($order, $replace, fgets($handle));
                }
                fclose($handle);
                $data = [
                    'count_word' => count($word),
                    'count_article' => count($word),
                    'status' => Word::START
                ];
                foreach(array_slice($word ,$result->progress) as $w){
                    $redisData = [
                        'task_id' => $result->id,
                        'word' => str_replace(Word::REPLACE_STR, $w,$result->word),
                        'y_word' => $w
                    ];
                    Redis::send(Word::WORD_PROMPT_QUEUE, $redisData);
                }
                Word::store($data, $result->id);
            }
        }catch(\Throwable $e){
            var_dump($e->getMessage());
            Word::startError($result->id);
        }
    }

    /**
     * 发送文章
     *
     * @return void
     */
    private function sendArticle() : void
    {
        $result = Timing::where('status', Timing::TIMING_START)->get();
        if($result){
            $result->map(function ($item) {
                $current = Carbon::now();
                $unit = 1;
                if($item->push_unit === 1){
                    $unit = 60;
                }elseif ($item->push_unit === 2){
                    $unit = 60 * 60;
                }
                $pushTime = $item->push_time * $unit;
                if($item->before_time + $pushTime >= time() && $item->before_time !== 0){
                    return ;
                }

                if($current->hour >= $item->day_start && $current->hour < $item->day_end){
                    $articleResult = Article::where('word_id', $item->word_id)
                                        ->limit($item->push_count)
                                        ->orderBy('id', 'desc')
                                        ->get();
                    if(!$articleResult->isEmpty()){
                        $ids = $articleResult->pluck('id')->all();
                        try {
                            $client   = new Client();
                            $client->post($item->post_url, [
                                'form_params'   => [
                                    'status'    => 0,
                                    'data'      => $articleResult->toArray()
                                ],
                                'timeout'     => 3
                            ]);
                            $item->before_time = time();
                            $item->save();
                            if($item->is_test === Timing::STOP_TEST){
                                Article::destroy($ids);
                            }
                        } catch (\Throwable $e) {
                            Log::debug('ERROR: ' . $e->getMessage());
                            throw new Error($e->getMessage());
                        }
                    }else{
                        $item->status = Timing::TIMING_OVER;
                        $item->save();
                        $resultWord = Word::where('id', $item->word_id)->firstOrFail();
                        $resultWord->status = Word::PUSH_OVER;
                        $resultWord->save();
                    }
                }
                
            });
        }
    }

}