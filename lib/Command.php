<?php
/**
 * Created by PhpStorm.
 * User: xuechaoc
 * Date: 2019-03-12
 * Time: 12:42
 */

namespace app\lib;

/**
 * 重写 yii\app\command 中的执行方法，当数据库连接失效之后，自动重连.
 *
 * Class Command
 * @package app\lib
 */
class Command extends \yii\db\Command
{
    public function execute()
    {
        try {
            return parent::execute();
        } catch (\yii\db\Exception $e) {
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                $this->db->close();
                $this->db->open();
                $this->pdoStatement = null;
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
            var_dump($e);
            if ($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                $this->db->close();
                $this->db->open();
                $this->pdoStatement = null;
                return parent::queryInternal($method, $fetchMode);
            }
            throw $e;
        }
    }
}