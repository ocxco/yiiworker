<?php
/**
 * Created by PhpStorm.
 * User: cxc
 * Date: 2019/1/9 0009
 * Time: 17:25
 */

namespace app\helpers;


use Yii;

class ResponseHelper
{

    const STATUS_SUCCESS = 0;
    const STATUS_FAILED = 1;

    /**
     * @var []ResponseHelper
     */
    private static $_instance = [];

    private $reqId = null;
    private $userId;
    private $extra = [];
    private $data;
    private $msg;
    private $code = self::STATUS_FAILED;
    public  $isSuccess = false;
    private $pagination = null;

    /**
     * @return ResponseHelper
     */
    public static function instance()
    {
        $reqId = Yii::$app->params['requestId'] ?: self::buildRequestId();
        if (!isset(self::$_instance[$reqId]) || !self::$_instance[$reqId] instanceof self) {
            self::$_instance[$reqId] = new self;
        }
        self::$_instance[$reqId]->reqId = $reqId;
        return self::$_instance[$reqId];
    }

    /**
     * @param $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * 设置分页数据.
     * @param int $currentPage 当前页码
     * @param int $totalNum    总数据条数
     * @param int $pageSize    每页数据条数.
     *
     * @return ResponseHelper
     */
    public function setPagination($currentPage, $totalNum, $pageSize)
    {
        $pages = ceil($totalNum / $pageSize);
        $this->pagination = [
            'current' => $currentPage,
            'pages' => $pages,
            'total' => $totalNum,
            'size' => $pageSize,
        ];
        return $this;
    }

    /**
     * @param $name
     * @param null $val
     * @return ResponseHelper
     */
    public function setExtra($name, $val = null)
    {
        if (is_array($name)) {
            unset($name['code'], $name['msg'], $name['data'], $name['pagination']);
            $this->extra = array_merge($this->extra, $name);
        } elseif ($val) {
            if (in_array($name, ['code', 'msg', 'data', 'pagination'])) {
                // 不能设置这四个关键字的数据.
                return;
            }
            $this->extra[$name] = $val;
        }
        return $this;
    }

    /**
     * 返回失败.
     * @param $msg
     * @param $data
     * @param int $code
     *
     * @return ResponseHelper
     */
    public function failed($msg, $data = null, $code = self::STATUS_FAILED)
    {
        $this->msg = $msg;
        $this->data = $data;
        $this->code = $code;
        $this->isSuccess = $code == self::STATUS_SUCCESS;
        return $this;
    }

    /**
     * 返回成功.
     *
     * @param $msg
     * @param $data
     * @return ResponseHelper
     */
    public function success($msg, $data = null)
    {
        return $this->failed($msg, $data, self::STATUS_SUCCESS);
    }

    /**
     * 直接返回json数据.
     *
     * @param $msg
     * @param null $data
     * @param int $code
     *
     * @return mixed
     */
    public function json($msg, $data = null, $code = self::STATUS_FAILED)
    {
        $resp = $this->failed($msg, $data, $code);
        return $resp->toJson();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        $data = [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data,
        ];
        if (!empty($this->extra)) {
            $data = array_merge($data, $this->extra);
        }
        if ($this->pagination) {
            $data['pagination'] = $this->pagination;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return bool
     */
    public function destroy()
    {
        self::$_instance[$this->reqId] = null;
        unset(self::$_instance[$this->reqId]);
        return true;
    }

    /**
     * 每次请求生成唯一ID，方便追踪日志.
     *
     * @return string
     */
    public static function buildRequestId()
    {
        return md5(crc32(microtime()));
    }

}