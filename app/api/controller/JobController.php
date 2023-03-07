<?php
namespace app\api\controller;

use support\Request;
use support\Response;

class JobController extends Base{
    public function create (Request $request) : Response
    {
        return $this->success();
    }
}   