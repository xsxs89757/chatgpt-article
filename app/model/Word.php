<?php
namespace App\model;

use Onlyoung4u\AsApi\Model\AsUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Onlyoung4u\AsApi\Kernel\Exception\AsErrorException;

class Word extends BaseModel {
    
    const STOP = 0;
    const START = 1;
    const OVER = 2;
    const STARTING = 3;
    const START_ERROR = 4;
    const START_TASK = 5;
    const PUSH_OVER = 6;

    const WORD_QUEUE = 'word_queue';

    const ONE_WORD_TO_ARTICLE_COUNT = 10;
    protected $table = 'word';

    /**
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date): string {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }


    /**
     * 操作人
     *
     * @return BelongsTo
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(AsUser::class, 'create_by');
    }




    /**
     * 启动任务
     *
     * @param integer $id
     * @return void
     */
    public static function start(int $id): void
    {
        try{
            $sql = self::whereIn('status', [self::STARTING, self::START, self::START_TASK])->first();
            if($sql){
                throw new AsErrorException('请先停止运行中的任务');
            }
            $data = [
                'status' => self::STARTING
            ];
            self::store($data, $id);
        } catch (\Throwable $e) {
            self::handleError($e);
        }
    }
    /**
     * 停止任务
     *
     * @param integer $id
     * @return void
     */
    public static function stop(int $id): void
    {
        try{
            $data = [
                'status' => self::STOP
            ];
            self::store($data, $id);
        } catch (\Throwable $e) {
            self::handleError($e);
        }
    }

    /**
     * 任务检查
     *
     * @param integer $id
     * @return void
     */
    public static function startTask(int $id): void
    {
        try{
            $data = [
                'status' => self::START_TASK
            ];
            self::store($data, $id);
        } catch (\Throwable $e) {
            self::handleError($e);
        }
    }

    /**
     * 任务失败
     *
     * @param integer $id
     * @return void
     */
    public static function startError(int $id): void
    {
        try{
            $data = [
                'status' => self::START_ERROR
            ];
            self::store($data, $id);
        } catch (\Throwable $e) {
            self::handleError($e);
        }
    }

    /**
     * 进度更新
     *
     * @param integer $id
     * @return void
     */
    public static function progress(int $id) :void
    {
        try {
            self::where('id',$id)->increment('progress');
        }catch (\Throwable $e) {
            self::handleError($e);
        }
    }

    /**
     * 保存
     *
     * @param array $data
     * @param integer|null $id
     * @return object
     */
    public static function store (array $data,?int $id = null) : object
    {
        try {
            if ($id !== null) {
                $sql = self::findOrFail($id);
            } else {
                $sql = new self;
                $sql->word = '';
                $sql->count_word = 0;
                $sql->count_article = 0;
                $sql->progress = 0;
                $sql->status = self::STOP;
                $sql->create_by = AsUser::getCurrentUserId();
            }
            foreach ($data as $key => $value) {
                $sql->$key = $value;
            }
            $sql->save();
            return $sql;
        } catch (\Throwable $e) {
            self::handleError($e);
        }
    }

    /**
     * 删除任务
     *
     * @param integer $id
     * @return void
     */
    public static function del(int $id) : void
    {
        try {
            $word = self::findOrFail($id);
            if($word->status === self::STOP || $word->status === self::PUSH_OVER){
                $word->delete();
            }else{
                throw new AsErrorException('只能删除停止和发布完成状态下的任务');
            }
        }catch(\Throwable $e){
            self::handleError($e);
        }
    }
}