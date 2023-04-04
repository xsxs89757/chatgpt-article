<?php
namespace app\admin\controller;

use support\Redis;
use App\model\Word;
use support\Request;
use support\Response;

class JobController extends BaseController{

    /**
     * 创建任务
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $name = $request->input('name');
        $filename = $request->input('filename'); 
        $word = $request->input('word');   
        $data = [
            'name' => $name,
            'filename' => $filename,
            'word' => $word
        ];
        Word::store($data);
        return $this->success();
    }

    /**
     * 列表
     *
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        $params = $this->getPageParams($request);
        $name = $request->input('name');
        $status = $request->input('status',null);
        $list = Word::with('operator:id,username,nickname')
        ->when($name, function ($query) use($name){
            $query->where('name', 'like', '%'.$name.'%');
        })->when($status !==null, function ($query) use($status){
            $query->where('status', $status);
        })->orderBy('id', 'desc')
        ->paginate($params['limit'], ['*'], 'page', $params['page']);
        return $this->page($list);
    }

    /**
     * 获取可使用的词库任务
     *
     * @param Request $request
     * @return Response
     */
    public function word(Request $request): Response
    {
        $map = [];
        Word::where('status', Word::OVER)->get(['id', 'name'])->map(function ($item) use (&$map){
            array_push($map, [
                'value' => $item->id,
                'label' => $item->name
            ]);
        });
        return $this->success($map);
    }

    /**
     * 删除任务
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function delete(Request $request,int $id): Response
    {
        Word::del($id);
        return $this->success();
    }

    /**
     * 启动任务
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function start(Request $request, int $id): Response
    {
        Word::start($id);
        return $this->success();
    }

    /**
     * 停止任务
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function stop(Request $request, int $id): Response
    {
        Word::stop($id);
        Redis::del('{redis-queue}-waiting'.Word::WORD_PROMPT_QUEUE);
        return $this->success();
    }
}

