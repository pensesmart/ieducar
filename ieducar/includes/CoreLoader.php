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

use CoreExt\Config\ConfigIni;
use CoreExt\Locale;

abstract class CoreLoader
{
	public static function get()
	{
		define('PROJECT_ROOT', ROOT);

		define('APP_ROOT', ROOT . DS . 'intranet');

		$root = realpath(dirname(__FILE__) . '/../');

		$paths = array();
		$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'intranet'));
		$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'lib'));
		$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'modules'));
		$paths[] = join(DIRECTORY_SEPARATOR, array($root, '.'));

		//set_include_path(join(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());

		if (getenv('CORE_EXT_CONFIGURATION_ENV')) {
			define('CORE_EXT_CONFIGURATION_ENV', getenv('CORE_EXT_CONFIGURATION_ENV'));
		} else {
			define('CORE_EXT_CONFIGURATION_ENV', 'production');
		}

		// por padr�o busca uma configura��o para o ambiente atual definido em CORE_EXT_CONFIGURATION_ENV
		$configFile = ROOT . '/configuration/' . CORE_EXT_CONFIGURATION_ENV . '.ini';

		// caso n�o exista o ini para o ambiente atual, usa o arquivo padr�o ieducar.ini
		if (! file_exists($configFile))
			$configFile = ROOT . '/configuration/ieducar.ini';

		// Array global de objetos de classes do pacote CoreExt
		global $coreExt;
		$coreExt = array();

		// Localiza��o para pt_BR
		$locale = Locale::getInstance();
		$locale->setCulture('pt_BR')->setLocale();

		// Instancia objeto CoreExt_Configuration
		$coreExt['Config'] = new ConfigIni($configFile, CORE_EXT_CONFIGURATION_ENV);
		$coreExt['Locale'] = $locale;

		// Timezone
		date_default_timezone_set($coreExt['Config']->app->locale->timezone);

		$tenantEnv = $_SERVER['HTTP_HOST'];

		// tenta carregar as configura��es da se��o especifica do tenant,
		// ex: ao acessar http://tenant.ieducar.com.br ser� carregado a se��o tenant.ieducar.com.br caso exista
		if ($coreExt['Config']->hasEnviromentSection($tenantEnv))
			$coreExt['Config']->changeEnviroment($tenantEnv);

		/**
		 * Altera o diret�rio da aplica��o. chamadas a fopen() na aplica��o n�o
		 * verificam em que diret�rio est�, assumindo sempre uma requisi��o a
		 * intranet/.
		 */
		//chdir($root . DS . 'intranet');
		//unset($root, $paths);
	}
}
