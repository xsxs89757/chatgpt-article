<?php
namespace app\admin\controller;

use App\model\Article as ModelArticle;
use support\Request;
use support\Response;

class GptArticleController extends BaseController
{
    /**
     * 文章列表
     *
     * @param Request $request
     * @return Response
     */
    public function list (Request $request) : Response
    {
        $params = $this->getPageParams($request);
        $list = ModelArticle::with('task:id,name')
        // ->select(['id', 'title', 'word', 'word_id', 'created_at'])
        ->orderBy('id', 'desc')
        ->paginate($params['limit'], ['*'], 'page', $params['page']);
        return $this->page($list);
    }

    /**
     * 删除文章
     *
     * @param Request $request
     * @param integer $id
     * @return Response
     */
    public function del (Request $request, int $id) : Response
    {
        ModelArticle::del($id);
        return $this->success();
    }
}