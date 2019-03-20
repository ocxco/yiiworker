<?php
/**
 * Created by PhpStorm.
 * User: xuechaoc
 * Date: 2019-03-12
 * Time: 12:42
 */

namespace app\lib;

use Yii;

/**
 * 重写 yii\app\command 中的执行方法，当数据库连接失效之后，自动重连.
 *
 * Class Command
 * @package app\lib
 */
class Command extends \yii\db\Command
{

    private $retry = false;

    public function execute()
    {
        try {
            return parent::execute();
        } catch (\yii\db\Exception $e) {
            if ($this->shouldRetry($e)) {
                return parent::execute();
            } else {
                throw $e;
            }
        }
    }

    protected function queryInternal($method, $fetchMode = null)
    {
        try {
            return parent::queryInternal($method, $fetchMode);
        } catch (\yii\db\Exception $e) {
            if ($this->shouldRetry($e)) {
                return parent::queryInternal($method, $fetchMode);
            }
            throw $e;
        }
    }

    private function shouldRetry(\yii\db\Exception $ex)
    {
        $errorMsg = $ex->getMessage();
        $gone = strpos($errorMsg, 'MySQL server has gone away');
        if ($gone === false && empty($ex->errorInfo) && !in_array($ex->errorInfo[1], [2006, 2013])) {
            return false;
        }
        $this->retry = true;
        $this->pdoStatement = null;
        $this->db->close();
        $this->db->open();
        return true;
    }

    /**
     * 重写bindPendingParams方法，当当前是数据库重连之后重试的时候
     * 调用bindValues方法重写绑定一次参数.
     */
    protected function bindPendingParams()
    {
        if ($this->retry) {
            $this->retry = false;
            $this->bindValues($this->params);
        }
        parent::bindPendingParams();
    }


}