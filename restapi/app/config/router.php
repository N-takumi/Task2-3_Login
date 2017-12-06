<?php
use Phalcon\Mvc\Router;

$router = $di->getRouter();

// Define your routes here


$router->addGet(
    '/login',
    [
        'controller' => 'Login',
        'action'     => 'login',
    ]
);


$router->addGet(
    '/callback',
    [
        'controller' => 'Login',
        'action'     => 'callback',
    ]
);


$router->addGet(
    '/mypage',
    [
        'controller' => 'Login',
        'action'     => 'showUserPage',
    ]
);

$router->addGet(
    '/logout',
    [
        'controller' => 'Login',
        'action'     => 'logout',
    ]
);


//データの取得 全件取得
$router->addGet(
    '/products',
    [
        'controller' => 'Products',
        'action'     => 'getAll',
    ]
);

//個別取得
$router->addGet(
    '/products/:int',
    [
        'controller' => 'Products',
        'action'     => 'getPiece',
        'int'        =>1,
    ]
);

//検索 //部分一致
$router->addGet(
    '/products/search/{name}',
    [
        'controller' => 'Products',
        'action'     => 'search',
    ]
);

//データの挿入
$router->addPost(
    '/products',
    [
        'controller' => 'Products',
        'action'     => 'add',
    ]
);

//データの変更(更新)
$router->addPut(
    '/products/:int',
    [
        'controller' => 'Products',
        'action'     => 'update',
        'int'        =>1,
    ]
);

//データの削除
$router->addDelete(
    '/products/:int',
    [
        'controller' => 'Products',
        'action'     => 'delete',
        'int'        =>1,
    ]
);

//画像保存
$router->addPost(
    '/products/img',
    [
      'controller' => 'Imgs',
      'action'     => 'uploadImg',
    ]
);

//画像表示
$router->addGet(
  '/products/img/{name}',
  [
    'controller' => 'Imgs',
    'action'     => 'showImg',
  ]
);




$router->handle();
