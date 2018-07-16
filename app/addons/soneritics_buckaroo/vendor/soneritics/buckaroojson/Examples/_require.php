<?php
require_once __DIR__ . '/../vendor/autoload.php';

spl_autoload_register(function($className) {
    if (substr($className, 0, 9) === 'Buckaroo\\') {
        include __DIR__ . '/../Soneritics/' . str_replace('\\', '/', $className) . '.php';
    }
});

