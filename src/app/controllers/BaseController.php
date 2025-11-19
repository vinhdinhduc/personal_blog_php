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
            Session::flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            $this->redirect($redirectTo);
        }
    }

    //Check quyền admin
    protected function requireAdmin($redirectTo = "/")
    {
        // Bước 1: Kiểm tra đã đăng nhập chưa
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            // Lưu URL hiện tại để redirect sau khi login
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            $this->redirect('/login');
            exit;
        }

        // Bước 2: Kiểm tra role
        if (!Session::isAdmin()) {
            Session::flash('error', 'Bạn không có quyền truy cập trang này.');
            $this->redirect($redirectTo);
            exit;
        }

        // Bước 3: Admin có thể truy cập
        return true;
    }

    /**
     * Check quyền admin hoặc owner của resource
     * @param int $resourceUserId ID của user sở hữu resource
     * @param string $redirectTo
     */
    protected function requireAdminOrOwner($resourceUserId, $redirectTo = "/")
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            $this->redirect('/login');
            exit;
        }

        $currentUserId = Session::getUserId();
        $isAdmin = Session::isAdmin();
        $isOwner = $currentUserId == $resourceUserId;

        if (!$isAdmin && !$isOwner) {
            Session::flash('error', 'Bạn không có quyền truy cập trang này.');
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
            Session::flash('error', 'Yêu cầu không hợp lệ. Vui lòng thử lại.');
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

    protected function uploadFile($fieldName, $destination = "uploads/")
    {
        if (isset($_FILES[$fieldName])) {
            return ["success" => false, "message" => "Chưa có file được tải lên."];
        }

        $file = $_FILES[$fieldName];

        //validate 

        $validate = Security::validateFileUpload($file);
        if (!$validate["success"]) {
            return $validate;
        }

        //Tạo file name duy nhất

        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $fileName = uniqid() . "_" . time() . "." . $extension;
        $uploadPath = __DIR__ . "/../../public/" . $destination;

        // Create directory if not exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fullPath = $uploadPath . $fileName;

        // Move file
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return [
                'success' => true,
                'filename' => $fileName,
                'path' => $destination . $fileName,
                'url' => Router::url($destination . $fileName)
            ];
        }

        return ['success' => false, 'message' => 'Failed to upload file'];
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
}
