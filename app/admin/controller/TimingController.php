<?php
namespace app\admin\controller;

use App\model\Timing as ModelTiming;
use support\Request;
use support\Response;

class TimingController extends BaseController{
    
    /**
     *  列表
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request) : Response 
    {
        $params = $this->getPageParams($request);
        $name = $request->input('name');
        $status = $request->input('status',null);
        $list = ModelTiming::with('task:id,name')
        ->when($name, fn ($query) =>
            $query->where('name', 'like', '%'.$name.'%')
        )->when($status !==null, fn($query) => 
            $query->where('status', $status)
        )->orderBy('id', 'desc')
        ->paginate($params['limit'], ['*'], 'page', $params['page']);
        return $this->page($list);

    }
    

    /**
     * 创建定时任务
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request) : Response
    {
        $data = $this->timingParams($request);
        ModelTiming::store($data);
        return $this->success();
    }

    /**
     * 更新
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function update(Request $request, int $id) : Response
    {
        $data = $this->timingParams($request);
        ModelTiming::store($data, $id);
        return $this->success();
    }

    /**
     * 定时任务开始
     *
     * @param Request $request
     * @return Response
     */
    public function start(Request $request,int $id) : Response
    {
        ModelTiming::start($id);
        return $this->success();
    }

    /**
     * 定时任务结束
     *
     * @param Request $request
     * @return Response
     */
    public function stop(Request $request,int $id) : Response
    {
        ModelTiming::stop($id);
        return $this->success();
    }

    /**
     * 定时任务删除
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function delete(Request $request,int $id) : Response
    {
        ModelTiming::del($id);
        return $this->success();
    }


    /**
     * 校检提交的参数
     *
     * @param Request $request
     * @return array
     */
    private function timingParams(Request $request): array
    {
        $data = $this->validateParams($request->all(), [
            'name' => ['required|string|max:198', '定时任务名称'],
            'word_id' => ['required|integer', '词库任务id'],
            'post_url' => ['required|url|max:198', '接收数据url'],
            'time_picker' => ['required|array', '时间'],
            'push_count' => ['required|integer|max:100', '每次发布的文章数量'],
        ]);
        list($data['day_start'], $data['day_end']) = array_map('intval', $data['time_picker']);
        unset($data['time_picker']);
        return $data;
    }

}   