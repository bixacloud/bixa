<?php
require_once __DIR__ . '/../vendor/autoload.php';

use IconCaptcha\IconCaptcha;

try {
    session_start();
    
    // Load IconCaptcha config
    $options = require __DIR__ . '/../config/iconcaptcha.php';
    
    // Create IconCaptcha instance
    $captcha = new IconCaptcha($options);
    
    // Process request
    $captcha->request()->process();
    
    // No valid request found
    http_response_code(400);
    echo json_encode(['error' => 'Unsupported request']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}