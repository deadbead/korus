<?php

// Composer autoload require
if (file_exists($file = __DIR__ . '/../../vendor/autoload.php')) {
    require_once $file;

    // Kint settings
    Kint\Renderer\RichRenderer::$folder = false;
    Kint::$max_depth = 20;
    Kint::$enabled_mode = true;
}

require_once __DIR__ . '/timeman.php';
require_once __DIR__ . '/user.php';
require_once __DIR__ . '/workday.php';
require_once __DIR__ . '/workdaypause.php';