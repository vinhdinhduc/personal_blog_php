<?php

/**
 * Authentication Routes
 * Routes cho đăng ký, đăng nhập, đăng xuất
 */

function registerAuthRoutes(Router $router)
{
    // Đăng ký
    $router->get('/register', function () {
        $controller = new AuthController();
        $controller->showRegister();
    });

    $router->post('/register', function () {
        $controller = new AuthController();
        $controller->register();
    });

    // Đăng nhập
    $router->get('/login', function () {
        $controller = new AuthController();
        $controller->showLogin();
    });

    $router->post('/login', function () {
        $controller = new AuthController();
        $controller->login();
    });

    // Đăng xuất
    $router->get('/logout', function () {
        $controller = new AuthController();
        $controller->logout();
    });

    // Quên mật khẩu
    $router->get('/forgot-password', function () {
        $controller = new AuthController();
        $controller->showForgotPassword();
    });

    $router->post('/forgot-password', function () {
        $controller = new AuthController();
        $controller->forgotPassword();
    });

    // Reset mật khẩu
    $router->get('/reset-password', function () {
        $controller = new AuthController();
        $controller->showResetPassword($_GET['token'] ?? '');
    });

    $router->post('/reset-password', function () {
        $controller = new AuthController();
        $controller->resetPassword();
    });
}
