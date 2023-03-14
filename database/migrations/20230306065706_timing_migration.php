<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class TimingMigration extends AbstractMigration
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
        $timing = $this->table('timing');
        $timing->addColumn('name', 'string', ['comment' => '定时任务名称'])
            ->addColumn('word_id', 'integer', ['signed' => false, 'comment' =>'词库任务id'])
            ->addColumn('post_url', 'string', [ 'comment' =>'发布的url'])
            ->addColumn('day_start', 'integer', ['default' => 0, 'limit' => MysqlAdapter::INT_TINY, 'comment' => '开始时间'])
            ->addColumn('day_end', 'integer', ['default' => 0, 'limit' => MysqlAdapter::INT_TINY, 'comment' => '结束时间'])
            ->addColumn('push_count','integer', ['comment' => '每次发送的数量'])
            ->addColumn('is_test', 'integer', ['default' => 0, 'limit' => MysqlAdapter::INT_TINY, 'comment' => '是否测试 0 测试 1 不测试'])
            ->addColumn('push_time', 'integer', ['default' => 0, 'comment' => '发布时间'])
            ->addColumn('push_unit', 'integer', ['default' => 0, 'limit' => MysqlAdapter::INT_TINY, 'comment' => '单位 0秒 1分钟 2小时'])
            ->addColumn('before_time', 'integer', ['default' => 0, 'comment' => '上次发布时间'])
            ->addColumn('status', 'integer', ['default' => 0, 'limit' => MysqlAdapter::INT_TINY, 'comment' => '状态 0 停止 1 开始 2 完成'])
            
            ->addTimestamps()
            ->create();
    }
}
