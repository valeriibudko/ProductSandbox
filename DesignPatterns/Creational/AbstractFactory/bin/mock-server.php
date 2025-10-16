<?php
// Router for internal PHP server. Catch POST for: /mail/send AND /sms/send.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($path, ['/mail/send', '/sms/send'], true)) {
    $body = file_get_contents('php://input');
    file_put_contents(__DIR__ . '/mock.log', date('c') . " $path $body\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    return;
}
http_response_code(404);
echo 'Not Found';
