<?php


require_once __DIR__ . "/../helpers/Session.php";
require_once __DIR__ . "/../helpers/Security.php";
require_once __DIR__ . "/../routes/route.php";
class BaseController
{



    //Render view with layout
    protected function viewWithLayout($view, $data = [], $layout = "layouts/main")
    {
        // Extract data to make variables available in view
        extract($data);

        // Start output buffering
        ob_start();

        // Include view - view has access to extracted $data variables
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            ob_end_clean();
            http_response_code(500);
            echo "View not found: $view";
            return;
        }

        // Capture view output into $content
        $content = ob_get_clean();

        // Now include layout - layout has access to $content and original $data variables
        $layoutFile = __DIR__ . '/../views/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            // Fallback: output content directly if layout missing
            echo $content;
        }
    }
    //render view  với layout
    protected function view($view, $data = [])
    {
        //Extract data to variables
        extract($data);
        $viewFile = __DIR__ . "/../views/{$view}.php";

        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            http_response_code(500);
            echo "View not found: $view";
        }
    }



    /**
     * JSON response
     * @param array $data
     * @param int $status
     */
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    //Redirect

    protected function redirect($url)
    {
        Router::redirect(Router::url($url));
    }

    //Check đăng nhập
    protected function requireAuth($redirectTo = "/login")
    {
        if (!Session::isLoggedIn()) {
            Toast::error('Vui lòng đăng nhập để tiếp tục.');
            $this->redirect($redirectTo);
        }
    }

    //Check quyền admin
    protected function requireAdmin($redirectTo = "/")
    {

        if (!Session::isLoggedIn()) {
            Toast::error('Vui lòng đăng nhập để tiếp tục.');
            // Lưu URL hiện tại để redirect sau khi login
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            $this->redirect('/login');
            exit;
        }

        if (!Session::isAdmin()) {
            Toast::error('Bạn không có quyền truy cập trang này.');

            $this->redirect($redirectTo);
            exit;
        }

        return true;
    }

    //Check quyền admin hoặc owner
    protected function requireAdminOrOwner($resourceUserId, $redirectTo = "/")
    {
        if (!Session::isLoggedIn()) {
            Toast::error('Vui lòng đăng nhập để tiếp tục.');
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            $this->redirect('/login');
            exit;
        }

        $currentUserId = Session::getUserId();
        $isAdmin = Session::isAdmin();
        $isOwner = $currentUserId == $resourceUserId;

        if (!$isAdmin && !$isOwner) {
            Toast::error('Bạn không có quyền truy cập trang này.');
            $this->redirect($redirectTo);
            exit;
        }

        return true;
    }

    //Kiểm tra CSRF token
    protected function validateCSRF()
    {
        $token = $_POST["csrf_token"] ?? '';
        if (!Security::verifyCSRFToken($token)) {
            Toast::error('Yêu cầu không hợp lệ. Vui lòng thử lại.');
            return false;
        }
        return true;
    }

    //Validate method

    protected function validateMethod($method)
    {
        if ($_SERVER["REQUEST_METHOD"] !== strtoupper($method)) {
            http_response_code(405);
            echo "Phương thức không được phép.";
            exit;
        }
    }
    //Lấy dữ liệu input

    protected function input($key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    //Lấy tất cả dữ liệu input

    protected function allInput()
    {
        return array_merge($_GET, $_POST);
    }

    //Check AJAX request

    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    //Hỗ trơợ tải file

    protected function uploadFile($fieldName, $uploadDir = 'uploads/')
    {
        // Check file có tồn tại k
        if (!isset($_FILES[$fieldName])) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy file upload'
            ];
        }

        $file = $_FILES[$fieldName];

        // Check lỗi upload
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'success' => false,
                'message' => 'Chưa có file được tải lên'
            ];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'File quá lớn',
                UPLOAD_ERR_FORM_SIZE => 'File quá lớn',
                UPLOAD_ERR_PARTIAL => 'File upload không hoàn chỉnh',
                UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm',
                UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file',
            ];

            return [
                'success' => false,
                'message' => $errors[$file['error']] ?? 'Lỗi upload'
            ];
        }

        // Validate kiểu file (chỉ chấp nhận ảnh)
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)'
            ];
        }

        // Validate kích thước file (tối đa 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'File không được vượt quá 5MB'
            ];
        }

        // Tạo thư mục upload nếu chưa tồn tại
        $uploadPath = __DIR__ . '/../../../public/' . $uploadDir;
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Tạo tên file duy nhất
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadPath . $filename;

        // Di chuyển file đã upload
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => $uploadDir . $filename,
                'url' => Router::url() . '/' . $uploadDir . $filename
            ];
        }

        return [
            'success' => false,
            'message' => 'Không thể lưu file'
        ];
    }

    //Phân trang
    protected function paginate($total, $page = 1, $perPage = 10)
    {
        $totalPages = ceil($total / $perPage);
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'offset' => $offset,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages
        ];
    }

    /**
     * Hiển thị trang 404
     */
    protected function notFound($message = '404 - Không tìm thấy trang')
    {
        error_log('404 Not Found: ' . $_SERVER['REQUEST_URI']);
        error_log('Request Method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('Current User: ' . Session::getUserId());
        error_log('Session Data: ' . print_r($_SESSION, true));

        http_response_code(404);
        $this->view('errors/404', [
            'message' => $message,
            'pageTitle' => '404 - Không tìm thấy trang'
        ]);
        exit;
    }
}
