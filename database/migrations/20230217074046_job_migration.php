<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class JobMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $user = $this->table('word');
        $user->addColumn('filename', 'string', ['comment' => 'csv词库地址'])
            ->addColumn('name', 'string', ['comment' => '词库任务名称'])
            ->addColumn('word', 'text', ['comment' => '', 'limit'=> MysqlAdapter::TEXT_LONG])
            ->addColumn('count_word', 'integer', ['comment' => '总词量'])
            ->addColumn('count_article', 'integer', ['comment' => '总文章量'])
            ->addColumn('progress', 'integer', ['comment' => '当前进度'])
            ->addColumn('status', 'integer', ['default' => 0, 'limit' => MysqlAdapter::INT_TINY, 'comment' => '状态 0 停止 1 开始 2 完成 3 启动中 4 启动失败 5 开始启动 6 发布完成'])
            ->addColumn('create_by', 'integer', ['signed' => false, 'comment' => '创建者'])
            ->addTimestamps()
            ->create();

        $article = $this->table('article');
        $article->addColumn('title', 'string', ['comment' => '标题'])
            ->addColumn('content', 'text', ['comment' => '内容', 'limit'=> MysqlAdapter::TEXT_LONG])
            ->addColumn('keywords', 'string', ['comment' => 'keywords'])
            ->addColumn('description', 'text', ['comment' => '简介'])
            ->addColumn('tags','string', ['comment' => '标签'])
            ->addColumn('word','string', ['comment' => '相关词'])
            ->addColumn('word_id', 'integer', ['signed' => false, 'comment' => '词库任务id'])
            ->addIndex(['word_id'])
            ->addTimestamps()
            ->create();

        
    }
}
