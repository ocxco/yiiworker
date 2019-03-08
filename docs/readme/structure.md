

```
basedir
 │
 ├─ api                 远程接口调用
 ├─ commands            yii控制台命令
 ├─ components          一些组件，继承自Yii\base\Component,可通过配置自动加载
 ├─ config              配置目录
 ├─ helpers             一些通用方法类封装，为方便使用，类方法通常都是静态方法.
 │    ├─ SignHelper     封装了验证客户端请求是否有效的方法.
 │    └─ ResponseHelper 封装统一的接口返回数据。注意，每个action都应该返回一个该对象.
 │
 ├─ controllers         控制器目录。注意每个controller的action都用post接收参数.
 ├─ models              关联数据库表的model，继承自\yii\db\ActiveRecord，可以方便使用ORM
 ├─ modules             一些复杂点的逻辑业务就放在本目录下，应该保持model层只做简单的sql查询，controller做参数过滤。  
 ├─ web                 服务入口目录
 │   ├─ index.php       yii框架的启动配置，由原来的$app->run(); 改为$app->init() 把程序启动工作交给start.php
 │   └─ start.php       用于启动rpc服务.本文件加载了上述index.php文件，用以初始化yii
 │
 └─ RpcClient           封装了用于调用使用本框架启动的rpc客户端，可以方便调用接口.
     ├─ Client.php      客户端的基类。封装一些配置加载解析，服务器选取等方法
     ├─ TextClient.php  基于TCP协议的接口调用方式.继承自Client.php
     ├─ HttpClient.php  基于Http协议的接口调用方式.继承自Client.php。所有请求参数都用POST传输.
     └─ demo.php        Client调用的例子.     
     
 ```
