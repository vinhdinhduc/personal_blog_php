<?php
require_once __DIR__ . '/src/app/routes/route.php';

echo "<h1>CSS Path Testing</h1>";
echo "<pre>";

echo "Router::url('css/auth.css') = " . Router::url('css/auth.css') . "\n";
echo "Router::url('css/components.css') = " . Router::url('css/components.css') . "\n\n";

echo "Expected paths:\n";
echo "http://localhost/personal-blog/css/auth.css\n";
echo "http://localhost/personal-blog/css/components.css\n\n";

// Check if files exist
$authCss = __DIR__ . '/public/css/auth.css';
$componentsCss = __DIR__ . '/public/css/components.css';

echo "File check:\n";
echo "auth.css exists: " . (file_exists($authCss) ? 'YES' : 'NO') . "\n";
echo "Path: $authCss\n\n";

echo "components.css exists: " . (file_exists($componentsCss) ? 'YES' : 'NO') . "\n";
echo "Path: $componentsCss\n";

echo "</pre>";

// Try to access directly
echo "<hr>";
echo "<h2>Try loading CSS:</h2>";
echo '<link rel="stylesheet" href="' . Router::url('/css/components.css') . '">';
echo '<link rel="stylesheet" href="' . Router::url('css/auth.css') . '">';

echo '<div class="alert alert--success" style="margin: 20px;">
    If this has green background, CSS loaded successfully!
</div>';
