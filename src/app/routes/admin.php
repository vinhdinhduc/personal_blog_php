<?php

/**
 * Admin Routes
 * Routes cho quản trị viên
 */

function registerAdminRoutes(Router $router)
{
    // Admin dashboard
    $router->get('/admin', function () {
        $controller = new AdminController();
        $controller->dashboard();
    });

    // ====================
    // POSTS MANAGEMENT
    // ====================

    // Danh sách bài viết
    $router->get('/admin/posts', function () {
        $controller = new AdminController();
        $controller->posts();
    });

    // Tạo bài viết mới
    $router->get('/admin/posts/create', function () {
        $controller = new AdminController();
        $controller->createPost();
    });

    $router->post('/admin/posts/create', function () {
        $controller = new AdminController();
        $controller->storePost();
    });

    // Sửa bài viết
    $router->get('/admin/posts/edit/{id}', function ($params) {
        $controller = new AdminController();
        $controller->editPost($params['id']);
    });

    $router->post('/admin/posts/edit/{id}', function ($params) {
        $controller = new AdminController();
        $controller->updatePost($params['id']);
    });

    // Xóa bài viết
    $router->post('/admin/posts/delete/{id}', function ($params) {
        $controller = new AdminController();
        $controller->deletePost($params['id']);
    });

    // Bulk actions
    $router->post('/admin/posts/bulk-action', function () {
        $controller = new AdminController();
        $controller->bulkActionPosts();
    });

    // ====================
    // CATEGORIES MANAGEMENT
    // ====================

    $router->get('/admin/categories', function () {
        $controller = new AdminController();
        $controller->categories();
    });

    $router->post('/admin/categories/create', function () {
        $controller = new AdminController();
        $controller->createCategory();
    });

    $router->post('/admin/categories/update/{id}', function ($params) {
        $controller = new AdminController();
        $controller->updateCategory($params['id']);
    });

    $router->post('/admin/categories/delete/{id}', function ($params) {
        $controller = new AdminController();
        $controller->deleteCategory($params['id']);
    });

    // ====================
    // TAGS MANAGEMENT
    // ====================

    $router->get('/admin/tags', function () {
        $controller = new AdminController();
        $controller->tags();
    });

    $router->post('/admin/tags/create', function () {
        $controller = new AdminController();
        $controller->createTag();
    });

    $router->post('/admin/tags/update/{id}', function ($params) {
        $controller = new AdminController();
        $controller->updateTag($params['id']);
    });

    $router->post('/admin/tags/delete/{id}', function ($params) {
        $controller = new AdminController();
        $controller->deleteTag($params['id']);
    });

    // Bulk delete tags
    $router->post('/admin/tags/bulk-delete', function () {
        $controller = new AdminController();
        $controller->bulkDeleteTags();
    });

    // ====================
    // COMMENTS MANAGEMENT
    // ====================

    $router->get('/admin/comments', function () {
        $controller = new AdminController();
        $controller->comments();
    });

    $router->post('/admin/comments/approve/{id}', function ($params) {
        $controller = new AdminController();
        $controller->approveComment($params['id']);
    });

    $router->post('/admin/comments/delete/{id}', function ($params) {
        $controller = new AdminController();
        $controller->deleteComment($params['id']);
    });

    // ====================
    // USERS MANAGEMENT
    // ====================

    $router->get('/admin/users', function () {
        $controller = new AdminController();
        $controller->users();
    });

    $router->post('/admin/users/create', function () {
        $controller = new AdminController();
        $controller->createUser();
    });

    $router->post('/admin/users/update/{id}', function ($params) {
        $controller = new AdminController();
        $controller->updateUser($params['id']);
    });

    $router->post('/admin/users/delete/{id}', function ($params) {
        $controller = new AdminController();
        $controller->deleteUser($params['id']);
    });

    // ====================
    // SETTINGS
    // ====================

    $router->get('/admin/settings', function () {
        $controller = new AdminController();
        $controller->settings();
    });

    $router->post('/admin/settings/update', function () {
        $controller = new AdminController();
        $controller->updateSettings();
    });
}
