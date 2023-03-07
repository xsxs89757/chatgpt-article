<?php
namespace App\model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends BaseModel{
    protected $table = 'article';
    
    const ARTICLE_QUEUE = 'article_queue';
    
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
    public function task(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
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
     * 删除生成的文章
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