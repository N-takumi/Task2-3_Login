<?php
$loader = new \Phalcon\Loader();
/**
 * We're a registering a set of directories taken from the configuration file
 */

//名前空間の定義

$loader->registerNamespaces(
    [
        'Store\Models' => $config->application->modelsDir,
    //    'OAuth'        => $config->application->twitteroauth
    ]
);


$loader->registerDirs(
    [
        $config->application->controllersDir,
        $config->application->modelsDir,
  //      $config->application->twitteroauth
    ]
);

$loader->register();
