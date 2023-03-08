<?php
namespace process;

use App\model\Article;
use app\model\Word;
use Workerman\Timer;
use App\model\Timing;
use Webman\RedisQueue\Redis;
use Workerman\Crontab\Crontab;
use Carbon\Carbon;
use Error;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                    if (!Redis::exists('{redis-queue}-waiting'.Article::ARTICLE_QUEUE) && 
                    !Redis::exists('{redis-queue}-delayed')){
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
        new Crontab('*/10 * * * * *', function(){
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
    private function patrolTask(): void
    {
        $result = Word::where('status', Word::STARTING)->first();
            try{
                if($result){
                    Word::startTask($result->id);
                    $localFileName = public_path() . '/' . $result->filename;
                    $word = [];
                    $handle = fopen($localFileName, 'r');
                    while (($data = fgetcsv($handle)) !== false) {
                        $data[0] = convertStr($data[0]);
                        if ($data[0] == '关键词') {
                            continue;
                        }
                        $word[] = $data[0];
                    }
                    $data = [
                        'count_word' => count($word),
                        'count_article' => Word::ONE_WORD_TO_ARTICLE_COUNT * count($word),
                        'status' => Word::START
                    ];
                    $api = config('app.chatgpt_cluster_title');
                    $index = 0;
                    foreach($word as $w){
                        $index = ($index + 1) % count($api);
                        $redisData = [
                            'index' => $index, 
                            'task_id' => $result->id,
                            'word' => $w
                        ];
                        Redis::send(Word::WORD_QUEUE, $redisData);
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
                            Article::destroy($ids);
                        } catch (\Throwable $e) {
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