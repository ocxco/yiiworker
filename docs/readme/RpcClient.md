### 关于RpcClient

RpcClient封装了两中rpc接口调用方式：`HttpClient`、`TextClient`。

一般PHP中调用仅需使用`TextClient`即可，`HttpClient`作为开发框架时调试使用。

当然你也完全可以使用`HttpClient`作为客户端使用，它与`TextClient`无任何区别。


### 使用说明

#### 配置。使用之前需要先配置一些服务端相关的参数并加载
```php
$textConfig = array(
    'service1' => array(
        'host' => [
            '127.0.0.1:8802',
        ],
        'mapType' => 'rand',
        'app'  => 'test',
        'secret' => 'test'
    ),
    'sercice2' => array(
        'host' => '127.0.0.1:8802',
        'mapType' => 'rand',
        'app'  => 'test',
        'secret' => 'test'
    ),
);

\RpcClient\TextClient::config($textConfig);
```

可以看到上述配置添加了两个service，表示当前RpcClient可以调用多个不同的服务接口。

每个配置项说明如下：

`host`：服务端的IP和端口，可以看到service1中该项是个数组，当服务端采用分布式部署时，即可用数组的方式配置.
`mapType`：调用服务端的轮询方式，可选值有
       
   * `rand`：随机选择，当调用次数足够大时，访问每个服务的概率分布是均匀的.
   * `loop`：轮询方式，每个服务器调用一次，依次调用.
   * `hash`：根据请求的url进行hash，相同url请求会落到同一个服务器上.
   
`app`：调用接口时认证需要，由服务端分配.
`secret`：调用接口时认证需要，由服务端分配.

#### 接口调用

上面已经通过`\RpcClient\TextClient::config($textConfig);`添加了配置.

然后即可通过`\RpcClient\TextClient::inst('service1')->setClass('test')->test();`调用服务器方法.

在demo中，上述调用的返回值为：
```php
array(3) {
  ["code"]=>
  int(0)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  array(5) {
    ["remoteIP"]=>
    string(13) "192.168.2.109"
    ["requestSource"]=>
    string(3) "go2"
    ["app"]=>
    string(4) "test"
    ["sign"]=>
    string(32) "f27f1e9afa62d2c4a606acce15e22142"
    ["timestamp"]=>
    int(1552027403)
  }
}

```

#### 如果服务端接口是多层目录结构，只需要修改`setClass`参数即可

`\RpcClient\TextClient::inst('service1')->setClass('go2/test')->testInDir();`

可以看到接口返回值为：
```php
array(3) {
  ["code"]=>
  int(0)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  string(43) "success access to testController in go2 dir"
}

```

注意上面`setClass`之后的方法调用`testInDir()`，当服务端提供的接口是驼峰命名的多个单词时，对应的调用方法就像这样。

所以上述调用对应的接口为：`controllers\go2\TestController.php`中的`actionTestInDir()`.

#### 带参数的调用

当接口需要参数时，需要以数组的方式提供参数.
```php
\RpcClient\TextClient::inst('service1')->setClass('test')->testParams(['name' => 'Chen']);

返回值为：

array(3) {
  ["code"]=>
  int(0)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  string(10) "Hello Chen"
}

```