<?php

/**
 * Web Routes
 * Định nghĩa tất cả routes cho web application
 */

// Include sub-routes
require_once __DIR__ . '/public.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin.php';

/**
 * Register all routes
 * @param Router $router
 */
function registerWebRoutes(Router $router)
{
    // Register public routes
    registerPublicRoutes($router);

    // Register auth routes
    registerAuthRoutes($router);

    // Register admin routes
    registerAdminRoutes($router);

    // 404 handler
    $router->setNotFound(function () {
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
    });
}
