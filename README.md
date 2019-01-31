## YiiWorker

    a rpc framework based on yii2-basic and Workerman
    
You can use the majority advantage of Yii2. Such as Controller、ActiveRecord、traits、behavior、logger and so on。 

Notice:
```
    avoid to use the methods that workerman does not support：
    exit、header、Yii::$app->end and so on
```

### installation
``` 
git clone git@github.com:ocxco/yiiworker.git
cd yiiworker && composer install
```

### how to use it

First, start the service. ` php web/start.php start `

Then, run the demo. `cd RpcClient && php demo.php`

Now you get the response from service and it display on the terminal.

### In other project

first you should copy RpcClient to your project.

then import the Text.php or Http.php to any where you want use it.

finally, reference to the demo.php to call the rpc api.

### Enjoy