<?php

// Load toast messages from session

// Get all flash messages
$toastMessages = [
    'success' => Session::getFlash('success'),
    'error' => Session::getFlash('error'),
    'warning' => Session::getFlash('warning'),
    'info' => Session::getFlash('info')
];

// Lọc bỏ các mục rỗng
$toastMessages = array_filter($toastMessages);

if (empty($toastMessages)) {
    return; // Không có gì để hiển thị
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php foreach ($toastMessages as $type => $data): ?>
            <?php
            // Kiểm tra nếu $data là mảng để lấy title và message
            if (is_array($data)) {
                $title = $data['title'] ?? ucfirst($type);
                $message = $data['message'] ?? '';
            } else {
                $title = ucfirst($type);
                $message = $data;
            }
            ?>
            // Gọi hàm hiển thị toast tương ứng
            toast.<?php echo $type; ?>(
                <?php echo json_encode($title); ?>,
                <?php echo json_encode($message); ?>
            );
        <?php endforeach; ?>
    });
</script>