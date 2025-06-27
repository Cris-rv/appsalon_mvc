<?php
echo "<pre>";
print_r([
    'SERVER_INFO' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'N/A',
    'ALL_SERVER_KEYS' => array_keys($_SERVER),
    'PATH_INFO' => $_SERVER['PATH_INFO'] ?? 'N/A',
    'ORIG_REQUEST_URI' => $_SERVER['HTTP_X_ORIGINAL_URI'] ?? 'N/A' // Usado en algunos proxies
]);