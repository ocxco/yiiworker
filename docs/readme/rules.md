### 框架要求.

* 每个action都使用post方式接收参数。

* 每个action都要返回一个`ResponseHelper`对象，使用方式详见[ResponseHelper使用方法](response.md)


### 编码规范建议

* 变量采用camelCase命名方法

* model层仅建立与table的映射关系以及一些简单的sql查询.

* controller层仅接收与验证参数，不要做复制的逻辑操作，这回导致controller代码过长，并且不利于代码复用

* module层是位于controller和model之间的逻辑层，有较复杂的数据库操作以及复杂的业务逻辑处理，都应该放到这一层处理

### 文档

拟采用phpDocumentor作为文档生成工具，这要求我们有良好的方法注释习惯，有利于文档的维护.