<?php
require_once 'handlers/config.php';
require_once 'handlers/router.php';
require_once 'handlers/db.php';

require_once 'modells/Project.php';

require_once 'controllers/Intelipro.php';

$config = new Config(configPath:
    'config.ini'
);

if ($config->get('mode') === 'development') {
    header('Access-Control-Allow-Origin: http://localhost:4200');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

$router = new Router(
    contentType: $config->get('content_type'),
    basePath:$config->get('base_path')
);

$db = new db(
    host:$config->get('db_host'), 
    db:$config->get('db_name'),
    user: $config->get('db_user'), 
    pass:$config->get('db_pass')
);

$projectModell = new Project($db);
$Intelipro = new Intelipro($projectModell, $config->get('deepseek_key'));

if ($config->get('mode') === 'development') {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

$router->addRoute(
    method: 'POST', 
    path: '/api', 
    callback: [$Intelipro, 'handleQuery'],
    permissionCallback: true, 
    loginCallback: true
);  

$router->dispatch();