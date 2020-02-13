<?php
declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

echo '<h1>Hello container visitor</h1>';
echo "<p>
Container IP: {$_SERVER['SERVER_ADDR']}<br>
Client IP: {$_SERVER['REMOTE_ADDR']}
</p>";

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
    echo "<pre>{$elasticResult}</pre>";
} else {
    $elasticError = curl_error($elasticCh);
    echo "<p><strong style='color: red'>offline</strong> <code>{$elasticError}</code></p>";
}
curl_close($elasticCh);
