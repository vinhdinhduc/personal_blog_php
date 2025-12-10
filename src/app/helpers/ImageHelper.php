<?php



class ImageHelper
{
    /*
    //Lấy URL hình ảnh
     */
    public static function url($imagePath, $default = 'assets/images/default-post.jpg')
    {
        if (!empty($imagePath)) {
            // Nếu đã là absolute URL (http/https)
            if (preg_match('/^https?:\/\//', $imagePath)) {
                return $imagePath;
            }

            // Đường dẫn tương đối - thêm BASE_URL
            return BASE_URL . '/public/' . ltrim($imagePath, '/');
        }

        // Ảnh dự phòng
        return BASE_URL . '/' . $default;
    }

    /*
        Lấy ảnh đại diện
     */
    public static function avatar($avatar)
    {
        return self::url($avatar, 'assets/images/default-avatar.jpg');
    }

    /*
        Lấy ảnh bìa bài viết
   
     */
    public static function postCover($cover)
    {
        return self::url($cover, 'assets/images/default-post.jpg');
    }





    /*       Lấy Gravatar từ email
     */
    public static function gravatar($email, $size = 200, $default = 'mp')
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}";
    }

    /* Kiểm tra file hình ảnh có tồn tại không
    
     */
    public static function exists($path)
    {
        if (empty($path)) {
            return false;
        }

        // Nếu là external URL
        if (preg_match('/^https?:\/\//', $path)) {
            return true; // Giả sử các URL bên ngoài tồn tại
        }

        // Kiểm tra file local
        $fullPath = __DIR__ . '/../../../public/' . ltrim($path, '/');
        return file_exists($fullPath);
    }

    /* Lấy kích thước hình ảnh
   
     */
    public static function getDimensions($path)
    {
        if (!self::exists($path)) {
            return null;
        }

        $fullPath = __DIR__ . '/../../../public/' . ltrim($path, '/');

        if (!is_file($fullPath)) {
            return null;
        }

        $size = @getimagesize($fullPath);

        if ($size === false) {
            return null;
        }

        return [
            'width' => $size[0],
            'height' => $size[1],
            'type' => $size[2],
            'mime' => $size['mime']
        ];
    }
}
