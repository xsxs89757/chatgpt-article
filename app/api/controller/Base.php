<?php

namespace app\api\controller;

use support\Request;
use app\kernel\traits\JsonResponse;
use Onlyoung4u\AsApi\Helpers\AsValidator;
use app\kernel\exception\JsonErrorException;

class Base
{
    use JsonResponse;

    /**
     * 验证参数
     *
     * @param array $params
     * @param array $rules
     * @param bool $withError
     * @return array
     * @throws JsonErrorException
     */
    protected function validateParams(array $params, array $rules, bool $withError = true): array
    {
        try {
            $v = AsValidator::asValidate($params, $rules);

            if ($v->fails()) {
                $msg = $withError ? $v->errors()->first() : '';
                throw new JsonErrorException($msg, $this->CodeAdapter()::STATUS_ERROR_PARAM);
            }

            return $v->validated();
        } catch (JsonErrorException $exception) {
            throw $exception;
        } catch (\Throwable) {
            throw new JsonErrorException('', $this->CodeAdapter()::STATUS_ERROR_PARAM);
        }
    }

    /**
     * 验证参数
     *
     * @param array $params
     * @param array $rules
     * @return array
     * @throws JsonErrorException
     */
    protected function validateParamsWithoutErrorMessage(array $params, array $rules): array
    {
        try {
            $v = AsValidator::validate($params, $rules);

            if ($v->fails()) {
                throw new JsonErrorException('', $this->CodeAdapter()::STATUS_ERROR_PARAM);
            }

            return $v->validated();
        } catch (JsonErrorException $exception) {
            throw $exception;
        } catch (\Throwable) {
            throw new JsonErrorException('', $this->CodeAdapter()::STATUS_ERROR_PARAM);
        }
    }

    /**
     * 验证ID
     *
     * @param $id
     * @param bool $withResponse
     * @return bool
     * @throws JsonErrorException
     */
    protected function validateId($id, bool $withResponse = false): bool
    {
        $res = as_validate_id($id);

        if (!$res && $withResponse) {
            throw new JsonErrorException('', $this->CodeAdapter()::STATUS_ERROR_PARAM);
        }

        return $res;
    }

    /**
     * 验证ID并抛出错误
     *
     * @param $id
     * @return void
     * @throws JsonErrorException
     */
    protected function validateIdWithResponse($id): void
    {
        $this->validateId($id, true);
    }

    /**
     * 获取分页大小
     *
     * @param Request $request
     * @return int
     * @throws JsonErrorException
     */
    protected function getPageSize(Request $request): int
    {
        $limit = $request->input('limit', 20);

        if (!$this->validateId($limit)) throw new JsonErrorException('分页参数错误');

        $maxPageSize = config('plugin.onlyoung4u.as-api.app.max_page_size', 100);

        if ($limit > $maxPageSize) throw new JsonErrorException('每页最多' . $maxPageSize . '条');

        return $limit;
    }

    /**
     * 获取分页参数
     *
     * @param Request $request
     * @return array
     */
    protected function getPageParams(Request $request): array
    {
        $page = $request->input('page', 1);

        if (!$this->validateId($page)) $page = 1;

        $limit = $this->getPageSize($request);

        $offset = ($page - 1) * $limit;

        return compact('page', 'limit', 'offset');
    }
}