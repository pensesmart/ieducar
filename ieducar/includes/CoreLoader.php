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

		// por padrão busca uma configuração para o ambiente atual definido em CORE_EXT_CONFIGURATION_ENV
		$configFile = ROOT . '/configuration/' . CORE_EXT_CONFIGURATION_ENV . '.ini';

		// caso não exista o ini para o ambiente atual, usa o arquivo padrão ieducar.ini
		if (! file_exists($configFile))
			$configFile = ROOT . '/configuration/ieducar.ini';

		// Array global de objetos de classes do pacote CoreExt
		global $coreExt;
		$coreExt = array();

		// Localização para pt_BR
		$locale = Locale::getInstance();
		$locale->setCulture('pt_BR')->setLocale();

		// Instancia objeto CoreExt_Configuration
		$coreExt['Config'] = new ConfigIni($configFile, CORE_EXT_CONFIGURATION_ENV);
		$coreExt['Locale'] = $locale;

		// Timezone
		date_default_timezone_set($coreExt['Config']->app->locale->timezone);

		$tenantEnv = $_SERVER['HTTP_HOST'];

		// tenta carregar as configurações da seção especifica do tenant,
		// ex: ao acessar http://tenant.ieducar.com.br será carregado a seção tenant.ieducar.com.br caso exista
		if ($coreExt['Config']->hasEnviromentSection($tenantEnv))
			$coreExt['Config']->changeEnviroment($tenantEnv);

		/**
		 * Altera o diretório da aplicação. chamadas a fopen() na aplicação não
		 * verificam em que diretório está, assumindo sempre uma requisição a
		 * intranet/.
		 */
		//chdir($root . DS . 'intranet');
		//unset($root, $paths);
	}
}
