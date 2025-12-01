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

    // ✅ XEM TẤT CẢ BÀI VIẾT
    $router->get('/posts', function () {
        $controller = new HomeController();
        $controller->allPosts();
    });

    // ✅ TÌM KIẾM
    $router->get('/search', function () {
        $controller = new HomeController();
        $controller->search();
    });

    // Xem bài viết chi tiết
    $router->get('/post/{slug}', function ($params) {
        $controller = new PostController();
        $controller->show($params['slug']);
    });

    // Danh sách tất cả categories
    $router->get('/category', function () {
        $controller = new CategoryController();
        $controller->categoryList();
    });

    // Chi tiết một category
    $router->get('/category/{slug}', function ($params) {
        $controller = new CategoryController();
        $controller->show($params['slug']);
    });

    // Lọc theo category với pagination
    $router->get('/category/{slug}/page/{page}', function ($params) {
        $controller = new CategoryController();
        $controller->show($params['slug'], $params['page']);
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

    // Trang giới thiệu
    $router->get('/about', function () {
        $controller = new HomeController();
        $controller->about();
    });

    // Tạo comment
    $router->post('/comment/create', function () {
        $controller = new CommentController();
        $controller->create();
    });
    // Show edit form (GET) - redirect về post với edit mode
    $router->get('/comment/{id}/edit', function ($params) {
        $controller = new CommentController();
        $controller->edit($params['id']);
    });

    // Update comment (POST)
    $router->post('/comment/{id}/update', function ($params) {
        $controller = new CommentController();
        $controller->update($params['id']);
    });

    // Delete comment (POST)
    $router->post('/comment/{id}/delete', function ($params) {
        $controller = new CommentController();
        $controller->delete($params['id']);
    });

    // Approve comment (POST)
    $router->post('/comment/{id}/approve', function ($params) {
        $controller = new CommentController();
        $controller->approve($params['id']);
    });

    // Unapprove comment (POST)
    $router->post('/comment/{id}/unapprove', function ($params) {
        $controller = new CommentController();
        $controller->unapprove($params['id']);
    });

    // Profile
    $router->get('/profile', function () {
        $controller = new ProfileController();
        $controller->index();
    });

    $router->post('/profile/update-info', function () {
        $controller = new ProfileController();
        $controller->updateInfo();
    });

    $router->post('/profile/change-password', function () {
        $controller = new ProfileController();
        $controller->changePassword();
    });

    $router->post('/profile/update-avatar', function () {
        $controller = new ProfileController();
        $controller->updateAvatar();
    });
}
