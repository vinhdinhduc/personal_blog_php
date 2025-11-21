<?php

/**
 * Admin Routes
 */

function registerAdminRoutes(Router $router)
{
    // Admin dashboard
    $router->get('/admin', function () {
        $controller = new AdminController();
        $controller->dashboard();
    });



    // Danh sách bài viết

    $router->get('/admin/posts', function () {
        $controller = new PostController();
        $controller->posts();
    });

    // Tạo bài viết mới
    $router->get('/admin/posts/create', function () {
        $controller = new PostController();
        $controller->showCreate();
    });

    $router->post('/admin/posts/create', function () {
        $controller = new PostController();
        $controller->create();
    });

    // Sửa bài viết
    $router->get('/admin/posts/edit/{id}', function ($params) {
        $controller = new PostController();
        $controller->showEdit($params['id']);
    });

    $router->post('/admin/posts/edit/{id}', function ($params) {
        $controller = new PostController();
        $controller->update($params['id']);
    });

    // Xóa bài viết
    $router->post('/admin/posts/delete/{id}', function ($params) {
        $controller = new PostController();
        $controller->delete($params['id']);
    });

    // Bulk actions
    // $router->post('/admin/posts/bulk-action', function () {
    //     $controller = new PostController();
    //     $controller->bulkAction();
    // });

    // ====================
    // CATEGORIES MANAGEMENT
    // ====================

    // $router->get('/admin/categories', function () {
    //     $controller = new CategoryController();
    //     $controller->categories();
    // });

    // $router->post('/admin/categories/create', function () {
    //     $controller = new CategoryController();
    //     $controller->createCategory();
    // });

    // $router->post('/admin/categories/update/{id}', function ($params) {
    //     $controller = new CategoryController();
    //     $controller->updateCategory($params['id']);
    // });

    // $router->post('/admin/categories/delete/{id}', function ($params) {
    //     $controller = new CategoryController();
    //     $controller->deleteCategory($params['id']);
    // });

    // ====================
    // TAGS MANAGEMENT
    // ====================

    // $router->get('/admin/tags', function () {
    //     $controller = new TagController();
    //     $controller->tags();
    // });

    // $router->post('/admin/tags/create', function () {
    //     $controller = new TagController();
    //     $controller->createTag();
    // });

    // $router->post('/admin/tags/update/{id}', function ($params) {
    //     $controller = new TagController();
    //     $controller->updateTag($params['id']);
    // });

    // $router->post('/admin/tags/delete/{id}', function ($params) {
    //     $controller = new TagController();
    //     $controller->deleteTag($params['id']);
    // });

    // $router->post('/admin/tags/bulk-delete', function () {
    //     $controller = new TagController();
    //     $controller->bulkDeleteTags();
    // });

    // ====================
    // COMMENTS MANAGEMENT
    // ====================

    // $router->get('/admin/comments', function () {
    //     $controller = new CommentController();
    //     $controller->comments();
    // });

    // $router->post('/admin/comments/approve/{id}', function ($params) {
    //     $controller = new CommentController();
    //     $controller->approveComment($params['id']);
    // });

    // $router->post('/admin/comments/delete/{id}', function ($params) {
    //     $controller = new CommentController();
    //     $controller->deleteComment($params['id']);
    // });

    // ====================
    // USERS MANAGEMENT
    // ====================

    $router->get('/admin/users', function () {
        $controller = new UserController();
        $controller->index();
    });
    $router->get("/admin/users/create", function () {
        $controller = new UserController();
        $controller->showFormCreate();
    });
    $router->post('/admin/users/create', function () {
        $controller = new UserController();
        $controller->create();
    });
    $router->get('/admin/users/update/{id}', function ($params) {
        $controller = new UserController();
        $controller->showFormEdit($params['id']);
    });


    $router->post('/admin/users/update/{id}', function ($params) {
        $controller = new UserController();
        $controller->update($params['id']);
    });

    // $router->post('/admin/users/delete/{id}', function ($params) {
    //     $controller = new UserController();
    //     $controller->deleteUser($params['id']);
    // });

    // $router->get('/admin/users/get/{id}', function ($params) {
    //     $controller = new UserController();
    //     $controller->getUser($params['id']);
    // });

    // ====================
    // SETTINGS
    // ====================

    // $router->get('/admin/settings', function () {
    //     $controller = new CommentController();
    //     $controller->settings();
    // });

    // $router->post('/admin/settings/update', function () {
    //     $controller = new CommentController();
    //     $controller->updateSettings();
    // });
}
