<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Helper: require file with existence check
function requireFile(string $path)
{
    if (!file_exists($path)) {
        error_log("Required file not found: {$path}");
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo 'Server configuration error. Check server error log.';
        exit;
    }
    require_once $path;
}

// Load core files
requireFile(__DIR__ . '/src/app/routes/route.php');
requireFile(__DIR__ . '/src/app/helpers/Session.php');
requireFile(__DIR__ . '/src/app/helpers/AutoLoad.php');
requireFile(__DIR__ . '/src/config/config.php');
// Start session
Session::start();

// Load controllers
requireFile(__DIR__ . '/src/app/controllers/HomeController.php');
requireFile(__DIR__ . '/src/app/controllers/AuthController.php');
requireFile(__DIR__ . '/src/app/controllers/PostController.php');
requireFile(__DIR__ . '/src/app/controllers/CategoryController.php');
requireFile(__DIR__ . '/src/app/controllers/AdminCommentController.php');
requireFile(__DIR__ . '/src/app/controllers/ProfileController.php');
requireFile(__DIR__ . '/src/app/controllers/AdminController.php');
requireFile(__DIR__ . '/src/app/controllers/CommentController.php');
requireFile(__DIR__ . '/src/app/controllers/UserController.php');



// Initialize router
$router = new Router();

// Load and register all routes
requireFile(__DIR__ . '/src/app/routes/web.php');
registerWebRoutes($router);

// Dispatch request
$router->dispatch();
