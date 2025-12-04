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
    $router->get('/admin/posts/add', function () {
        $controller = new PostController();
        $controller->showCreate();
    });

    $router->post('/admin/posts/store', function () {
        $controller = new PostController();
        $controller->create();
    });

    // Sửa bài viết
    $router->get('/admin/posts/edit/{id}', function ($params) {
        $controller = new PostController();
        $controller->showEdit($params['id']);
    });

    $router->post('/admin/posts/update/{id}', function ($params) {
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

    $router->get('/admin/category', function () {
        $controller = new CategoryController();
        $controller->categories();
    });

    $router->post('/admin/categories/create', function () {
        $controller = new CategoryController();
        $controller->createCategory();
    });

    $router->post('/admin/categories/update/{id}', function ($params) {
        $controller = new CategoryController();
        $controller->updateCategory($params['id']);
    });

    $router->post('/admin/categories/delete/{id}', function ($params) {
        $controller = new CategoryController();
        $controller->deleteCategory($params['id']);
    });

    // TAGS MANAGEMENT
    $router->get('/admin/tags', function () {
        $controller = new TagController();
        $controller->index();
    });

    $router->get('/admin/tags/create', function () {
        $controller = new TagController();
        $controller->showCreate();
    });

    $router->post('/admin/tags/create', function () {
        $controller = new TagController();
        $controller->create();
    });

    $router->get('/admin/tags/edit/{id}', function ($params) {
        $controller = new TagController();
        $controller->showEdit($params['id']);
    });

    $router->post('/admin/tags/update/{id}', function ($params) {
        $controller = new TagController();
        $controller->update($params['id']);
    });

    $router->post('/admin/tags/delete/{id}', function ($params) {
        $controller = new TagController();
        $controller->delete($params['id']);
    });

    $router->post('/admin/tags/bulk-delete', function () {
        $controller = new TagController();
        $controller->bulkDelete();
    });

    $router->get('/admin/tags/view/{id}', function ($params) {
        $controller = new TagController();
        $controller->detail($params['id']);
    });

    $router->get('/admin/tags/search', function () {
        $controller = new TagController();
        $controller->search();
    });

    // ====================
    // COMMENTS MANAGEMENT
    // ====================

    $router->get('/admin/comments', function () {
        $controller = new AdminCommentController();
        $controller->comments();
    });

    $router->post('/admin/comments/approve/{id}', function ($params) {
        $controller = new AdminCommentController();
        $controller->approveComment($params['id']);
    });

    $router->post('/admin/comments/unapprove/{id}', function ($params) {
        $controller = new AdminCommentController();
        $controller->unapproveComment($params['id']);
    });

    $router->post('/admin/comments/delete/{id}', function ($params) {
        $controller = new AdminCommentController();
        $controller->deleteComment($params['id']);
    });

    $router->get('/admin/comments/edit/{id}', function ($params) {
        $controller = new AdminCommentController();
        $controller->showEditComment($params['id']);
    });
    $router->post('/admin/comments/edit/{id}', function ($params) {
        $controller = new AdminCommentController();
        $controller->editComment($params['id']);
    });

    $router->get('/admin/comments/view/{id}', function ($params) {
        $controller = new AdminCommentController();
        $controller->viewComment($params['id']);
    });

    $router->post('/admin/comments/bulk-approve', function () {
        $controller = new AdminCommentController();
        $controller->bulkApprove();
    });

    $router->post('/admin/comments/bulk-delete', function () {
        $controller = new AdminCommentController();
        $controller->bulkDelete();
    });
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

    $router->post('/admin/users/delete/{id}', function ($params) {
        $controller = new UserController();
        $controller->delete($params['id']);
    });
}
