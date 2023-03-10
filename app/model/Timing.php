<?php
namespace App\model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timing extends BaseModel{
    protected $table = 'timing';
    
    const TIMING_START = 1;
    const TIMING_STOP = 0;
    const TIMING_OVER = 2;

    const START_TEST = 0;
    const STOP_TEST = 1;

    protected $appends = ['time_picker'];
    
     /**
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date): string {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }


    /**
     * 附加timepicker
     *
     * @return array
     */
    public function getTimePickerAttribute() : array
    { 
        return [
            (string)$this->attributes['day_start'],
            (string)$this->attributes['day_end'],
        ];
    }

    /**
     * 操作人
     *
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    /**
     * 开始任务
     *
     * @param integer $id
     * @return void
     */
    public static function start(int $id): void
    {
        try{
            $sql = self::findOrFail($id);
            $sql->status = self::TIMING_START;
            $sql->save();
        }catch(\Throwable $e){
            self::handleError($e);
        }
    }
    /**
     * 结束任务
     *
     * @param integer $id
     * @return void
     */
    public static function stop(int $id): void
    {
        try{
            $sql = self::findOrFail($id);
            $sql->status = self::TIMING_STOP;
            $sql->save();
        }catch(\Throwable $e){
            self::handleError($e);
        }
    }
    
    /**
     * 结束了
     *
     * @param integer $id
     * @return void
     */
    public static function over(int $id): void
    {
        try{
            $sql = self::findOrFail($id);
            $sql->status = self::TIMING_OVER;
            $sql->save();
        }catch(\Throwable $e){
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
                $sql->status = self::TIMING_STOP;
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
     * @return object
     */
    public static function del(int $id): object
    {
        try{
            $article = self::findOrFail($id);
            $article->delete();
            return $article;
        }catch (\Throwable  $e){    
            self::handleError($e);
        }
    }
}