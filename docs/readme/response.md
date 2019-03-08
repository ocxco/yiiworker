
### 说明
ResponseHelper封装了一些方法方便接口返回数据使用.

失败返回`code:1`,成功返回`code:0`

#### 一般使用方法
1. 返回失败

    `return ResponseHelper::instance()->failed($msg, $data);`
    
2. 返回成功 

    `return ResponseHelper::instance()->success($msg, $data);`
    

#### 设置分页数据.

上述方法会返回`code`、`msg`、`data`数据.

比如调用测试接口（[调用方法见RpcClient说明](RpcClient.md)），成功返回值如下：
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

如果是查询数据，需要返回列表和分页数据,可以使用

`ResponseHelper::instance()->setPagination(1, 20, 10);`

如调用`test/test-list`接口返回如下:
```php
array(4) {
  ["code"]=>
  int(0)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  array(10) {
    [0]=>
    int(0)
    ......
    [9]=>
    int(9)
  }
  ["pagination"]=>
  array(4) {
    ["current"]=>
    int(1)
    ["pages"]=>
    int(2)
    ["total"]=>
    int(20)
    ["size"]=>
    int(10)
  }
}

```

#### 设置额外数据

有时我们查询一个列表的时候，希望除了列表之外，还能返回比如用户数据，但是又不改变列表内容本身的层次结构。

可以使用以下方法

`ResponseHelper::instance()->setExtra('user', ['username' => 'test', 'state' => 1]);`

当我们调用`test/test-user-in-list`方法时，接口返回如下:
```php
array(5) {
  ["code"]=>
  int(0)
  ["msg"]=>
  string(7) "success"
  ["data"]=>
  array(10) {
    [0]=>
    int(0)
    ......
    [9]=>
    int(9)
  }
  ["user"]=>
  array(2) {
    ["username"]=>
    string(4) "test"
    ["state"]=>
    int(1)
  }
  ["pagination"]=>
  array(4) {
    ["current"]=>
    int(1)
    ["pages"]=>
    int(2)
    ["total"]=>
    int(20)
    ["size"]=>
    int(10)
  }
}

```

#### 设置额外数据的两种方式

`setExtra`方法接收两个参数`$name`、`$val`

`ResponseHelper::instance()->setExtra('user', ['username' => 'test', 'state' => 1]);`

如果`$name`是数组，会自动忽略`$val`参数

`ResponseHelper::instance()->setExtra(['user' => ['username' => 'test', 'state' => 1]]);`

***注意:*** 上述两个语句完全等价.


#### 其他

当服务端给客户端返回数据时，会调用`ResponseHelper`对象的`toJson`方法，以json字符串的格式返回

当然，这一步是自动完成的，无需你做任何额外操作。

详见 [web/start.php](../../web/start.php)