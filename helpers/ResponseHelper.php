<?php
/**
 * Created by PhpStorm.
 * User: cxc
 * Date: 2019/1/9 0009
 * Time: 17:25
 */

namespace app\helpers;


class ResponseHelper
{
    private static $_instance;

    private $userId;

    private $extra;

    const CODE_SUCCESS = 0;

    const CODE_FAILED = 1;


    private $pagination = [
        'current' => 1,
        'pages' => 1,
        'total' => 0,
        'size' => 10,
    ];

    public static function instance($refresh = false)
    {
        if (!self::$_instance || $refresh) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setPagination($current, $total, $pageSize)
    {
        $pages = ceil($total / $pageSize);
        $this->pagination = [
            'current' => $current,
            'pages' => $pages,
            'total' => $total,
            'size' => $pageSize,
        ];
    }

    public function setExtra($name, $val = null)
    {
        if (is_array($name)) {
            $this->extra = array_merge($this->extra, $name);
        } elseif ($val) {
            $this->extra[$name] = $val;
        }
    }

    public function json($msg, $data = null, $code = self::CODE_FAILED)
    {
        $data = [
            'data' => $data,
        ];
        if (!empty($this->extra)) {
            $data = array_merge($data, $this->extra);
        }
        $data['pagination'] = $this->pagination;
        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        return json_encode($data);
    }

    public function success($msg, $data)
    {
        return $this->json($msg, $data, self::CODE_SUCCESS);
    }

}