<?php
defined('ROOT') or die;

define('GANTRYADMIN_PATH', ROOT . '/includes/admin');

$gantry['router'] = function ($c) {
    return new Gantry\Admin\Router($c);
};

$gantry['router']->dispatch();
