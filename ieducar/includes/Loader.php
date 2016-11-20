<?php

/**
 * @package   TrilhaJovem - i-Educar
 * @author	Smart http://www.pensesmart.com
 * @copyright Copyright (C) 2014 - 2016 Smart, LTDA
 * @license   Licen�a simples: GNU/GPLv2 e posteriores
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
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
