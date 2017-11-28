<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Session\Factory;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require_once '../../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

define( 'CONSUMER_KEY',getenv('CONS_KEY'));
define( 'CONSUMER_SECRET', getenv('CONS_SECRET'));
define('SQL_PASS',getenv('SQL_PASS'));
define( 'OAUTH_CALLBACK', 'http://localhost/restapi/callback' );

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    $di->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => SQL_PASS,
                'dbname'   => 'shop',//shopに変更
            ]
        );
    }
    );


    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo str_replace(["\n","\r","\t"], '', $application->handle()->getContent());

} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
