<?php

use App\Style;
use App\Template;
use Detection\MobileDetect;

require dirname(__DIR__) . '/vendor/autoload.php';

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

header('Content-Type: text/html; charset=utf-8');

$detect = new MobileDetect();
$isMobile = $detect->isMobile();

Style::init();
Template::init();

Template::getSmarty()->assign('isMobile', $isMobile);

