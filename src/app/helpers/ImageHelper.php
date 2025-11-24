<?php

/**
 * Image Helper
 * Helper để xử lý URL ảnh với fallback
 */

class ImageHelper
{
    /**
     * Get image URL with fallback
     * @param string|null $imagePath
     * @param string $default
     * @return string
     */
    public static function url($imagePath, $default = 'assets/images/default-post.jpg')
    {
        if (!empty($imagePath)) {
            // Nếu đã là absolute URL (http/https)
            if (preg_match('/^https?:\/\//', $imagePath)) {
                return $imagePath;
            }

            // Relative path - thêm BASE_URL
            return BASE_URL . '/public/' . ltrim($imagePath, '/');
        }

        // Fallback image
        return BASE_URL . '/' . $default;
    }

    /**
     * Get avatar URL
     * @param string|null $avatar
     * @return string
     */
    public static function avatar($avatar)
    {
        return self::url($avatar, 'assets/images/default-avatar.jpg');
    }

    /**
     * Get post cover image URL
     * @param string|null $cover
     * @return string
     */
    public static function postCover($cover)
    {
        return self::url($cover, 'assets/images/default-post.jpg');
    }

    /**
     * Get category image URL
     * @param string|null $image
     * @return string
     */
    public static function categoryImage($image)
    {
        return self::url($image, 'assets/images/default-category.jpg');
    }

    /**
     * Get user profile image URL
     * @param string|null $profileImage
     * @param string|null $email For gravatar
     * @return string
     */
    public static function profile($profileImage, $email = null)
    {
        if (!empty($profileImage)) {
            return self::url($profileImage);
        }

        // Fallback to Gravatar nếu có email
        if (!empty($email)) {
            return self::gravatar($email);
        }

        return self::url(null, 'assets/images/default-avatar.jpg');
    }

    /**
     * Get Gravatar URL
     * @param string $email
     * @param int $size
     * @param string $default
     * @return string
     */
    public static function gravatar($email, $size = 200, $default = 'mp')
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}";
    }

    /**
     * Check if image exists
     * @param string $path
     * @return bool
     */
    public static function exists($path)
    {
        if (empty($path)) {
            return false;
        }

        // Nếu là external URL
        if (preg_match('/^https?:\/\//', $path)) {
            return true; // Assume external URLs exist
        }

        // Check local file
        $fullPath = __DIR__ . '/../../../public/' . ltrim($path, '/');
        return file_exists($fullPath);
    }

    /**
     * Get image dimensions
     * @param string $path
     * @return array|null
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

    /**
     * Generate responsive image srcset
     * @param string $path
     * @param array $sizes [width => suffix]
     * @return string
     */
    public static function srcset($path, $sizes = [320 => 'sm', 640 => 'md', 1024 => 'lg'])
    {
        if (empty($path)) {
            return '';
        }

        $srcset = [];
        $pathInfo = pathinfo($path);
        $dir = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $ext = $pathInfo['extension'];

        foreach ($sizes as $width => $suffix) {
            $resizedPath = "{$dir}/{$filename}-{$suffix}.{$ext}";
            if (self::exists($resizedPath)) {
                $url = self::url($resizedPath);
                $srcset[] = "{$url} {$width}w";
            }
        }

        return implode(', ', $srcset);
    }

    /**
     * Optimize image path for thumbnail
     * @param string $path
     * @param string $size (sm, md, lg)
     * @return string
     */
    public static function thumbnail($path, $size = 'sm')
    {
        if (empty($path)) {
            return self::url(null);
        }

        $pathInfo = pathinfo($path);
        $dir = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        $ext = $pathInfo['extension'];

        // Check if thumbnail exists
        $thumbPath = "{$dir}/{$filename}-{$size}.{$ext}";

        if (self::exists($thumbPath)) {
            return self::url($thumbPath);
        }

        // Fallback to original
        return self::url($path);
    }

    /**
     * Delete image file
     * @param string $path
     * @return bool
     */
    public static function delete($path)
    {
        if (empty($path)) {
            return false;
        }

        // Don't delete external URLs
        if (preg_match('/^https?:\/\//', $path)) {
            return false;
        }

        $fullPath = __DIR__ . '/../../../public/' . ltrim($path, '/');

        if (file_exists($fullPath) && is_file($fullPath)) {
            return @unlink($fullPath);
        }

        return false;
    }

    /**
     * Get image MIME type
     * @param string $path
     * @return string|null
     */
    public static function getMimeType($path)
    {
        $dimensions = self::getDimensions($path);
        return $dimensions['mime'] ?? null;
    }

    /**
     * Check if file is image
     * @param string $path
     * @return bool
     */
    public static function isImage($path)
    {
        if (empty($path)) {
            return false;
        }

        $allowedMimes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml'
        ];

        $mime = self::getMimeType($path);
        return in_array($mime, $allowedMimes);
    }

    /**
     * Format file size
     * @param int $bytes
     * @return string
     */
    public static function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get file size
     * @param string $path
     * @return int|null
     */
    public static function getSize($path)
    {
        if (!self::exists($path)) {
            return null;
        }

        $fullPath = __DIR__ . '/../../../public/' . ltrim($path, '/');

        if (!is_file($fullPath)) {
            return null;
        }

        return filesize($fullPath);
    }

    /**
     * Get formatted file size
     * @param string $path
     * @return string|null
     */
    public static function getFormattedSize($path)
    {
        $size = self::getSize($path);

        if ($size === null) {
            return null;
        }

        return self::formatSize($size);
    }
}
