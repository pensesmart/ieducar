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

namespace Smart;

/**
 * Use \Smart\Loader::setup() or \Smart\Loader::get() instead.
 *
 * This class separates Loader logic from the \Smart\Loader class. By adding this extra class we are able to upgrade
 * Smart and initializing the new version during a single request -- as long as Smart has not been initialized.
 *
 * @internal
 */
abstract class RealLoader
{
	protected static $errorMessagePhpMin = 'A versão %s instalada de seu PHP não é suportada. O i-Educar requer o PHP na versão %s.';
	protected static $errorMessageIEducarLoaded = 'Incluindo i-Educar loader multiplas vezes.';

	/**
	 * Initializes Smart and returns Composer ClassLoader.
	 *
	 * @return \Composer\Autoload\ClassLoader
	 * @throws \RuntimeException
	 * @throws \LogicException
	 */
	public static function getClassLoader()
	{
		// Fail safe version check for PHP <5.4.0.
		if (version_compare($phpVersion = PHP_VERSION, '5.4.0', '<'))
		{
			throw new \RuntimeException(sprintf(self::$errorMessagePhpMin, $phpVersion, '5.4.0'));
		}

		if (defined('SMART_VERSION'))
		{
			throw new \LogicException(self::$errorMessageIEducarLoaded);
		}

		define('SMART_VERSION', '@version@');
		define('SMART_VERSION_DATE', '@versiondate@');

		if (!defined('DS')) {
			define('DS', DIRECTORY_SEPARATOR);
		}

		define('GANTRY_DEBUGGER', class_exists('Gantry\\Debugger'));

		return $autoload = self::autoload();
	}

	/**
	 * @return \Composer\Autoload\ClassLoader
	 * @throws \LogicException
	 * @internal
	 */
	protected static function autoload()
	{
		// Register platform specific overrides.
		define('SMART_PLATFORM', 'ieducar');
		define('SMART_ROOT', ROOT);
		define('GANTRY5_ROOT', ROOT);

		$base = ROOT;
		$vendor = "{$base}/lib";
		$dev = is_dir($vendor);

		$autoload = "{$vendor}/vendor/autoload.php";

		// Initialize auto-loading.
		if (!file_exists($autoload))
		{
			echo $autoload;
			throw new \LogicException('Instale o composer presente na pasta do i-Educar');
		}

		/** @var \Composer\Autoload\ClassLoader $loader */
		$loader = require_once $autoload;

		if ($dev) {
			$loader->addPsr4('CoreExt\\', "{$base}/CoreExt");
			$loader->addPsr4('Gantry\\', "{$base}/Gantry");
		}

		return $loader;
	}
}
