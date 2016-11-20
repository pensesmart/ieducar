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

abstract class Loader
{
	public static function setup()
	{
		self::get();
	}

	/**
	 * @return mixed
	 */
	public static function get()
	{
		static $loader;

		if (!$loader) {
			require_once __DIR__ . '/RealLoader.php';
			$loader = RealLoader::getClassLoader();
		}

		return $loader;
	}
}
