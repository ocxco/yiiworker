<?php 
class TestControllerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
    }

    protected function _after()
    {

    }

    public function testTest()
    {
        $res = \RpcClient\TextClient::inst('passport')->setClass('test')->test();
        $this->assertNotEmpty($res);
        $this->assertEquals(0, $res['code']);
        $this->assertEquals('success', $res['msg']);
        $this->assertEquals(\RpcClient\TextClient::getRemoteIp(), $res['data']['remoteIP']);
    }

    public function testTestParams()
    {
        $name = 'YiiWorker';
        $res = \RpcClient\TextClient::inst('passport')->setClass('test')->testParams(['name' => $name]);
        $this->assertNotEmpty($res);
        $this->assertEquals("Hello $name", $res['data']);
    }
}