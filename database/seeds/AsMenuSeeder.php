<?php


use Phinx\Seed\AbstractSeed;

class AsMenuSeeder extends AbstractSeed
{
    public function getActions(string $name, int $pid, array $keys = ['create', 'update', 'delete']): array
    {
        $actions = [
            'create' => '添加',
            'update' => '编辑',
            'delete' => '删除',
            'sort' => '排序',
        ];

        $data = [];

        foreach ($keys as $key) {
            $title = $actions[$key] ?? '';

            if (empty($title)) continue;

            $data[] = [
                'key' => $name . '.' . $key,
                'name' => $key,
                'title' => $title,
                'hidden' => 1,
                'pid' => $pid,
            ];
        }

        return $data;
    }

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $table = $this->table('as_menus');

        // 清空表
        $table->truncate();

        // 系统设置
        $data = [
            'key' => 'system',
            'name' => 'system',
            'title' => '系统设置',
            'redirect' => '/system/configs',
            'icon' => 'ant-design:setting-filled',
            'pid' => 0,
            'sort' => 99,
        ];

        $table->insert($data)->saveData();

        $pid = $this->getAdapter()->getConnection()->lastInsertId();

        $data = [
            'key' => 'system.configs',
            'name' => 'configs',
            'title' => '系统设置',
            'pid' => $pid,
        ];

        $table->insert($data)->saveData();

        $data = [
            [
                'name' => 'store',
                'key' => 'system.configs.store',
                'title' => '保存',
                'hidden' => 1,
                'pid' => $this->getAdapter()->getConnection()->lastInsertId(),
            ]
        ];

        $table->insert($data)->save();

        $data = [
            'key' => 'system.config.list',
            'name' => 'config',
            'title' => '配置管理',
            'pid' => $pid,
        ];

        $table->insert($data)->saveData();

        $data = $this->getActions('system.config', $this->getAdapter()->getConnection()->lastInsertId(), ['create', 'update', 'delete', 'sort']);

        $table->insert($data)->saveData();

        // 管理员
        $data = [
            'key' => 'user',
            'name' => 'user',
            'title' => '管理员',
            'redirect' => '/users/list',
            'icon' => 'ant-design:user-outlined',
            'pid' => 0,
            'sort' => 99,
        ];

        $table->insert($data)->saveData();

        $pid = $this->getAdapter()->getConnection()->lastInsertId();

        // 角色管理
        $data = [
            'key' => 'role.list',
            'name' => 'role',
            'title' => '角色管理',
            'pid' => $pid,
        ];

        $table->insert($data)->saveData();

        $data = $this->getActions('role', $this->getAdapter()->getConnection()->lastInsertId());

        $table->insert($data)->saveData();

        // 管理员管理
        $data = [
            'key' => 'user.list',
            'name' => 'user',
            'title' => '管理员管理',
            'pid' => $pid,
        ];

        $table->insert($data)->saveData();

        $data = $this->getActions('user', $this->getAdapter()->getConnection()->lastInsertId());

        $table->insert($data)->saveData();

        // 操作记录
        $data = [
            'key' => 'actionLogs.list',
            'name' => 'logs',
            'title' => '操作日志',
            'redirect' => '/logs/index',
            'icon' => 'akar-icons:eye-closed',
            'pid' => 0,
            'sort' => 99,
        ];

        $table->insert($data)->saveData();

        $data = [
            'key' => 'actionLogs.clear',
            'name' => 'clear',
            'title' => '清空日志',
            'hidden' => 1,
            'pid' => $this->getAdapter()->getConnection()->lastInsertId(),
        ];

        $table->insert($data)->saveData();


        // 自动化
        $data = [
            'key' => 'automation',
            'name' => 'automation',
            'title' => '自动化管理',
            'redirect' => '/automation/thesaurus',
            'icon' => 'carbon:workflow-automation',
            'pid' => 0,
            'sort' => 1,
        ];
        $table->insert($data)->saveData();
        $pid = $this->getAdapter()->getConnection()->lastInsertId();

        // 词库管理
        $data = [
            'key' => 'automation.thesaurus',
            'name' => 'thesaurus',
            'title' => '词库任务管理',
            'pid' => $pid,
        ];
        $table->insert($data)->saveData();
        $thesaurusId = $this->getAdapter()->getConnection()->lastInsertId();
        $data = [
            'key' => 'automation.thesaurus.create',
            'name' => 'create',
            'title' => '创建任务',
            'hidden' => 1,
            'pid' => $thesaurusId,
        ];
        $table->insert($data)->saveData();
        $data = [
            'key' => 'automation.thesaurus.start',
            'name' => 'start',
            'title' => '启动',
            'hidden' => 1,
            'pid' => $thesaurusId,
        ];
        $table->insert($data)->saveData();
        $data = [
            'key' => 'automation.thesaurus.stop',
            'name' => 'stop',
            'title' => '停止',
            'hidden' => 1,
            'pid' => $thesaurusId,
        ];
        $table->insert($data)->saveData();$data = [
            'key' => 'automation.thesaurus.delete',
            'name' => 'delete',
            'title' => '删除',
            'hidden' => 1,
            'pid' => $thesaurusId,
        ];
        $table->insert($data)->saveData();

        // 任务管理
        $data = [
            'key' => 'automation.scheduled',
            'name' => 'scheduled',
            'title' => '定时任务管理',
            'pid' => $pid,
        ];
        $table->insert($data)->saveData();
        $scheduledId = $this->getAdapter()->getConnection()->lastInsertId();
        $data = [
            'key' => 'automation.scheduled.create',
            'name' => 'create',
            'title' => '创建任务',
            'hidden' => 1,
            'pid' => $scheduledId,
        ];
        $table->insert($data)->saveData();
        $data = [
            'key' => 'automation.scheduled.update',
            'name' => 'update',
            'title' => '修改',
            'hidden' => 1,
            'pid' => $scheduledId,
        ];
        $table->insert($data)->saveData();
        $data = [
            'key' => 'automation.scheduled.start',
            'name' => 'start',
            'title' => '启动',
            'hidden' => 1,
            'pid' => $scheduledId,
        ];
        $table->insert($data)->saveData();
        $data = [
            'key' => 'automation.scheduled.stop',
            'name' => 'stop',
            'title' => '停止',
            'hidden' => 1,
            'pid' => $scheduledId,
        ];
        $table->insert($data)->saveData();$data = [
            'key' => 'automation.scheduled.delete',
            'name' => 'delete',
            'title' => '删除',
            'hidden' => 1,
            'pid' => $scheduledId,
        ];
        $table->insert($data)->saveData();


        // 内容管理
        $data = [
            'key' => 'content',
            'name' => 'content',
            'title' => '内容管理',
            'redirect' => '/content/list',
            'icon' => 'material-symbols:article',
            'pid' => 0,
            'sort' => 1,
        ];
        $table->insert($data)->saveData();
        $pid = $this->getAdapter()->getConnection()->lastInsertId();

        // gpt文章管理
        $data = [
            'key' => 'content.list',
            'name' => 'list',
            'title' => 'gpt文章管理',
            'pid' => $pid,
        ];
        $table->insert($data)->saveData();
        $contentId = $this->getAdapter()->getConnection()->lastInsertId();

        $data = [
            'key' => 'content.list.delete',
            'name' => 'delete',
            'title' => '删除',
            'hidden' => 1,
            'pid' => $contentId,
        ];
        $table->insert($data)->saveData();

        // 聚合文章管理
        // $data = [
        //     'key' => 'content.polymerization',
        //     'name' => 'polymerization',
        //     'title' => '聚合文章管理',
        //     'pid' => $pid,
        // ];
        // $table->insert($data)->saveData();

    }
}
