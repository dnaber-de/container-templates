<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

if(array_key_exists('info', $_GET)) {
    phpinfo();
    exit;
}

$kibanaAddress = gethostbyname('kibana');
echo '<h1>Hello container visitor</h1>';
echo "<p>
Container IP: {$_SERVER['SERVER_ADDR']}<br>
Client IP: {$_SERVER['REMOTE_ADDR']}<br>
<a href='?info'>PHP Info</a><br>
<a href='//{$kibanaAddress}:5601'>Kibana</a>
</p>";

echo '<h2>Database</h2>';
try {
    $pdo = new PDO('mysql:dbname=db;host=db:3306', 'db', 'db');
    echo '<p><strong style="color:darkgreen">online</strong></p>';
    echo '<p>Host: <code>db</code><br>
        Port: <code>3306</code>
</p>';
} catch (PDOException $pdoException) {
    echo "<p><strong style='color: red'>offline</strong> <code>{$pdoException->getMessage()}</code></p>";
}

echo '<h2>Memcached</h2>';
$memcached = new \Memcached();
$memcachedConnected = $memcached->addServer('memcached', 11211);
$memcacheStats = $memcached->getStats();
if(false !== $memcacheStats) {
    echo '<p><strong style="color:darkgreen">online</strong></p>';
    echo '<p>Host: <code>memcached</code><br>
        Port: <code>11211</code>
</p>';
} else {
    $memcachedError = $memcached->getLastErrorMessage();
    echo "<p><strong style='color: red'>offline</strong> <code>{$memcachedError}</code></p>";
}

echo '<h2>Elasticsearch</h2>';
$elasticCh = curl_init();
curl_setopt_array(
    $elasticCh,
    [
        CURLOPT_URL => 'elasticsearch:9200/_cat/nodes?v&pretty',
        CURLOPT_RETURNTRANSFER => 1,
    ]
);
$elasticResult = curl_exec($elasticCh);


if(false !== $elasticResult) {
    echo '<p><strong style="color:darkgreen">online</strong></p>';
    echo '<p>Host: <code>elasticsearch</code><br>
        Port: <code>9200</code>
</p>';
    echo "<pre>{$elasticResult}</pre>";
} else {
    $elasticError = curl_error($elasticCh);
    echo "<p><strong style='color: red'>offline</strong> <code>{$elasticError}</code></p>";
}
curl_close($elasticCh);
