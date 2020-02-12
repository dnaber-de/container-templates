<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

echo '<h1>Hello container visitor</h1>';
echo "<p>
Container IP: {$_SERVER['SERVER_ADDR']}<br>
Client IP: {$_SERVER['REMOTE_ADDR']}
</p>";
