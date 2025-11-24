<?php

/**
 * Public Routes
 * Routes cho các trang công khai
 */

function registerPublicRoutes(Router $router)
{
    // Trang chủ
    $router->get("/", function () {
        $controller = new HomeController();
        $controller->index();
    });

    // Trang chủ với pagination
    $router->get('/page/{page}', function ($params) {
        $controller = new HomeController();
        $controller->index($params['page']);
    });

    // Xem bài viết
    $router->get('/post/{slug}', function ($params) {
        $controller = new PostController();
        $controller->show($params['slug']);
    });



    // Lọc theo category
    $router->get('/category/{slug}', function ($params) {
        $controller = new HomeController();
        $controller->byCategory($params['slug']);
    });

    // Lọc theo category với pagination
    $router->get('/category/{slug}/page/{page}', function ($params) {
        $controller = new HomeController();
        $controller->byCategory($params['slug'], $params['page']);
    });

    // Lọc theo tag
    $router->get('/tag/{slug}', function ($params) {
        $controller = new HomeController();
        $controller->byTag($params['slug']);
    });

    // Lọc theo tag với pagination
    $router->get('/tag/{slug}/page/{page}', function ($params) {
        $controller = new HomeController();
        $controller->byTag($params['slug'], $params['page']);
    });

    // Tìm kiếm
    $router->get('/search', function () {
        $controller = new HomeController();
        $controller->search();
    });

    // Trang giới thiệu
    $router->get('/about', function () {
        $controller = new HomeController();
        $controller->about();
    });

    // // Trang liên hệ
    // $router->get('/contact', function () {
    //     $controller = new HomeController();
    //     $controller->contact();
    // });

    // // POST contact form
    // $router->post('/contact', function () {
    //     $controller = new HomeController();
    //     $controller->submitContact();
    // });
}
