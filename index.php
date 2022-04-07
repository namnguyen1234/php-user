<?php
session_start();
date_default_timezone_set("Asia/Ho_Chi_Minh");

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

header("Access-Control-Allow-Headers: *");

require_once __DIR__ . '/vendor/autoload.php';

$namespaceController = "app\\controllers\\";
$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

$controller = ucfirst($controller);
$controller .= "Controller";

$path_controller = "App/Controllers/$controller.php";

if (file_exists($path_controller) == false) {
    die('Trang bạn tìm không tồn tại');
}

//require_once "$path_controller";

$instanceClass = "App\\Controllers\\$controller";

$object = new $instanceClass();

if (method_exists($object , $action) == false) {
    die("Không tồn tại phương thức $action của class $controller");
}

$object->$action();
