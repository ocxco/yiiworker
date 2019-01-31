<?php

return [
    'class' 	=> 'yii\db\Connection',
    'dsn' 		=> 'mysql:host=127.0.0.1;port=3306;dbname=test',
    'username' 	=> 'root',
    'password' 	=> 'root',
    'charset' 	=> 'utf8',
    'slaveConfig' => [
        'username' 		=> 'root',
        'password' 		=> 'root',
        'attributes' 	=> [
            PDO::ATTR_TIMEOUT => 10,
            PDO::ATTR_PERSISTENT => true,
        ],
        'charset' => 'utf8',
    ],
    'slaves' => [
        ['dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=test'],
    ],
];
