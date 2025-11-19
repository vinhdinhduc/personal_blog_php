<?php

/**
 * Router Class
 * Xử lý routing và điều hướng request
 */

class Router
{
    private $routes = [];
    private $notFound;

    /**
     * Thêm route GET
     * @param string $path
     * @param callable $callback
     */
    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Thêm route POST
     * @param string $path
     * @param callable $callback
     */
    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Thêm route
     * @param string $method
     * @param string $path
     * @param callable $callback
     */
    private function addRoute($method, $path, $callback)
    {
        // Chuyển đổi path thành regex pattern
        $pattern = $this->convertPathToPattern($path);
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }

    /**
     * Chuyển path thành regex pattern
     * @param string $path
     * @return string
     */
    private function convertPathToPattern($path)
    {
        // Chuyển {param} thành named capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        // Escape dấu /
        $pattern = str_replace('/', '\/', $pattern);
        return '/^' . $pattern . '$/';
    }

    /**
     * Set callback cho 404
     * @param callable $callback
     */
    public function setNotFound($callback)
    {
        $this->notFound = $callback;
    }

    /**
     * Dispatch request
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->getCurrentPath();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                // Lọc ra các params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Gọi callback
                if (!empty($params)) {
                    call_user_func_array($route['callback'], [$params]);
                } else {
                    call_user_func($route['callback']);
                }
                return;
            }
        }
        // Không tìm thấy route
        if ($this->notFound) {
            call_user_func($this->notFound);
        } else {
            $this->default404();
        }
    }

    /**
     * Lấy path hiện tại
     * @return string
     */
    private function getCurrentPath()
    {
        $path = $_GET['url'] ?? '/';

        // Loại bỏ query string
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
        }

        // Thêm / nếu không có
        if ($path === '') {
            $path = '/';
        } elseif ($path[0] !== '/') {
            $path = '/' . $path;
        }
        return rtrim($path, '/') ?: '/';
    }

    /**
     * 404 mặc định
     */
    private function default404()
    {
        http_response_code(404);
        echo "404 - Page Not Found";
    }

    /**
     * Redirect
     * @param string $url
     * @param int $code
     */
    public static function redirect($url, $code = 302)
    {
        header("Location: $url", true, $code);
        exit;
    }

    /**
     * Get base URL
     * @return string
     */
    public static function getBaseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $script = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . '://' . $host . rtrim($script, '/');
    }

    /**
     * Generate URL
     * @param string $path
     * @return string
     */
    public static function url($path = '')
    {
        $baseUrl = self::getBaseUrl();
        $path = ltrim($path, '/');

        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i', $path)) {
            return $baseUrl . '/public/' . $path;
        }

        return $baseUrl . '/' . $path;
    }
}
