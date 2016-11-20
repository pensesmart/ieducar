<?php

/**
 * @package   TrilhaJovem - i-Educar
 * @author	Smart http://www.pensesmart.com
 * @copyright Copyright (C) 2014 - 2016 Smart, LTDA
 * @license   Licença simples: GNU/GPLv2 e posteriores
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 */
define('PRIME_PROFILER', true);

define('PRIME_ROOT', dirname($_SERVER['SCRIPT_FILENAME']));
define('ROOT', dirname($_SERVER['SCRIPT_FILENAME']));
define('PRIME_URI', dirname($_SERVER['SCRIPT_NAME']));
define('IEDUCAR_URI', dirname($_SERVER['SCRIPT_NAME']));

PRIME_PROFILER && profiler_enable();

// Load debugger if it exists.
$include = ROOT . '/debugbar/Debugger.php';
if (file_exists($include)) {
	include_once $include;
}

include_once ROOT . '/includes/bootstrap.php';
//include_once ROOT . '/includes/CoreLoader.php';

use Gantry\Component\Filesystem\Folder;
use Gantry\Framework\Gantry;

//CoreLoader::get();

$gantry = Gantry::instance();

// Get current theme and path.
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : Folder::getRelativePath($_SERVER['REQUEST_URI'], IEDUCAR_URI);
$path = explode('?', $path, 2);
$path = reset($path);
$extension = strrchr(basename($path), '.');
if ($extension) {
    $path = substr($path, 0, -strlen($extension));
}
$theme = strpos($path, 'admin') !== 0 ? Folder::shift($path) : null;

define('THEME', 'hydrogen');
define('PAGE_PATH', $path ?: ($theme ? 'home' : ''));
define('PAGE_EXTENSION', trim($extension, '.') ?: 'html');

// Bootstrap selected theme.
$include = ROOT . "/themes/{$theme}/includes/gantry.php";
if (is_file($include)) {
    include $include;
}

PRIME_PROFILER && profiler_results();

// Enter to administration if we are in /ROOT/theme/admin. Also display installed themes if no theme has been selected.
if (strpos($path, 'admin') === 0) {
    require_once ROOT . '/admin/admin.php';
    exit();
}

// Boot the service.
/** @var Gantry\Framework\Theme $theme */
$theme = $gantry['theme'];

try {
    // Render the page.
    echo $theme->setLayout('default')->render('@pages/' . PAGE_PATH . '.' . PAGE_EXTENSION . '.twig');
} catch (Twig_Error_Loader $e) {
    // Or display error if template file couldn't be found.
    echo $theme->setLayout('_error')->render('@pages/_error.html.twig', ['error' => $e]);
}

/*
 * Enable profiler.
 */
function profiler_enable()
{
    if (!function_exists('xhprof_enable')) return;

    xhprof_enable(XHPROF_FLAGS_NO_BUILTINS);
}

/**
 * Display profiler results.
 */
function profiler_results()
{
	echo 'tste;';
    if (!function_exists('xhprof_disable')) return;

    $info = xhprof_disable();

    $treshholds = [
        '#660000' => 500,
        '#880000' => 370,
        '#AA0000' => 250,
        '#CC0000' => 180,
        '#CC2200' => 120,
        '#CC4400' => 80,
        '#CC6600' => 55,
        '#CC8800' => 35,
        '#CCAA00' => 25,
        '#CCCC00' => 18,
        '#AACC00' => 12,
        '#88CC00' => 9,
        '#66CC00' => 6,
        '#44CC00' => 4,
        '#22CC00' => 3,
        '#00CC00' => 2,
        '' => 1
    ];
    asort($treshholds);

    echo "<h1>Profiler Information</h1>";
    echo '<div style="padding:0 2em">';
    foreach ($info as $call => $data) {
        $count = $data['ct'];
        $time = $data['wt'] / 1000;
        $color = '';
        foreach ($treshholds as $color => $treshhold) {
            if ((float) $time < (float) $treshhold) {
                break;
            }
        }
        if (!$color) {
            continue;
        }
        echo sprintf(
            "<font color='%s'><b>%0.3f</b> ms</font> (<b>%d</b> calls): <i>%s</i><br/>\n",
            $color, $time, $count, $call
        );
    }
    echo "</div>";
}
