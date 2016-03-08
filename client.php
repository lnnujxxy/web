<?php
require "application/thirdparty/vendor/autoload.php";
use JsonRPC\Client;

$client = new Client('http://wenzhen.dev.shiyuehehu.com/server.php');
// $result1 = $client->execute("doSomething", [1]);
// $result2 = $client->execute("doSomething2", [2]);

$results = $client->batch()->execute("doSomething", [1])->execute("doSomething2", [1])->send();
var_dump($results);