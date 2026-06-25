<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

const OCTARINEPRESS_SLUG = 'octarinepress';
const OCTARINEPRESS_SHORT = 'octarinepress_';
const OCTARINEPRESS_THEME_CATEGORY = 'octarinepress-theme';
define('OCTARINEPRESS_ROOT', str_replace(ABSPATH, '/', dirname(__FILE__)));
define('OCTARINEPRESS_PATH', dirname(__FILE__));
define('OCTARINEPRESS_URI', home_url(OCTARINEPRESS_ROOT));
