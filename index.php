<?php
/* if you don't have a .htaccess
 * http://localhost/index.php?url=hello_world/index
 * else
 * http://localhost/hello_world/index
 * sample .htaccess content
 <IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !favicon.ico$
	RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
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

	//TODO: use templates, views & media classes instead of string representation
	public function add($first, $second) {
		return (string)($first + $second);
	}

	//Dispatcher::_callable() expects classname to create controller object and invoke the action
	public function __toString() {
		return __CLASS__;
	}
}

$controller_object = new HelloWorldController;

//Filter __callable and pass the controller object instead of locating it in the controllers folder. Filters FTW.
Dispatcher::applyFilter('_callable', function($self, $params, $chain) use($controller_object) {
	$params['params']['controller'] = $controller_object;
    return $chain->next($self, $params, $chain);
});

//GO!!
echo Dispatcher::run(new Request());

?>