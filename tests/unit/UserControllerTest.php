<?php 
class UserControllerTest extends \Codeception\Test\Unit
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

    // tests
    public function testGetUserInfo()
    {
        $res = \RpcClient\TextClient::inst('passport')->setClass('user')->userInfo(['userId' => 130754]);
        $this->assertNotEmpty($res);
        $this->assertEquals(0, $res['code']);
        $this->assertEquals(130754, $res['data']['id']);
    }
}