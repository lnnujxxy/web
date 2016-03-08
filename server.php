<?php
require "application/thirdparty/vendor/autoload.php";
use JsonRPC\Server;

class Api {
	public function doSomething($arg1, $arg2 = 3) {
		return $arg1 + $arg2;
	}

	public function doSomething2($arg1, $arg2 = 4) {
		return $arg1 + $arg2;
	}
}

class Api2 {
	public function doSomething2($arg1, $arg2 = 5) {
		return $arg1 + $arg2;
	}
}

$server = new Server;

// // Bind the method Api::doSomething() to the procedure myProcedure
// $server->bind('myProcedure', 'Api', 'doSomething');

// // Use a class instance instead of the class name
// $server->bind('mySecondProcedure', new Api, 'doSomething');

// The procedure and the method are the same
// $server->bind('doSomething', 'Api');

// Attach the class, client will be able to call directly Api::doSomething()
$server->attach(new Api);
$server->attach(new Api2);

echo $server->execute();

?>