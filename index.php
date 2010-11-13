<?php
/* http://localhost/index.php?url=hello_world/index
 * http://localhost/hello_world/index
 * http://localhost/hello_world/add/2/3
 */
use lithium\core\Libraries;
use lithium\net\http\Router;
use lithium\core\Environment;
use lithium\action\Dispatcher;
use lithium\action\Request;

// define lithium library path
define('LITHIUM_LIBRARY_PATH', dirname(__DIR__) . '/lithium/libraries');
if (!include LITHIUM_LIBRARY_PATH . '/lithium/core/Libraries.php') {
	$message  = "Lithium core could not be found.  Check the value of LITHIUM_LIBRARY_PATH in ";
	$message .= __FILE__ . ".  It should point to the directory containing your ";
	$message .= "/libraries directory.";
	throw new ErrorException($message);
}

//Autoload
Libraries::add('lithium');

//Default Routes
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}.{:type}', array('id' => null));
Router::connect('/{:controller}/{:action}/{:id:[0-9]+}');
Router::connect('/{:controller}/{:action}/{:args}');

class HelloWorldController extends \lithium\action\Controller {

	public function index() {
		return "Hello World";
	}

	public function add($first, $second) {
		return (string)($first + $second);
	}
}

//Filter __callable and return a new instance of the controller. Filters FTW.
Dispatcher::applyFilter('_callable', function($self, $params, $chain) {
	return new HelloWorldController();
});

//GO!!
echo Dispatcher::run(new Request());