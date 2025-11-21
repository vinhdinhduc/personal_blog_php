<?php

/**
 * Toast Partial
 * Include this in your layouts to enable toast notifications
 */

// Get all flash messages
$toastMessages = [
    'success' => Session::getFlash('success'),
    'error' => Session::getFlash('error'),
    'warning' => Session::getFlash('warning'),
    'info' => Session::getFlash('info')
];

// Filter out empty messages
$toastMessages = array_filter($toastMessages);

if (empty($toastMessages)) {
    return; // Nothing to show
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php foreach ($toastMessages as $type => $data): ?>
            <?php
            // Check if data is array (with title and message) or string
            if (is_array($data)) {
                $title = $data['title'] ?? ucfirst($type);
                $message = $data['message'] ?? '';
            } else {
                $title = ucfirst($type);
                $message = $data;
            }
            ?>

            toast.<?php echo $type; ?>(
                <?php echo json_encode($title); ?>,
                <?php echo json_encode($message); ?>
            );
        <?php endforeach; ?>
    });
</script>