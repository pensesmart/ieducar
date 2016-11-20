<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Session
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

namespace CoreExt\Session\Storage;

use CoreExt\Session\Storage\StorageAbstract;

/**
 * StorageDefault class.
 *
 * Storage de session padr�o de Session, usa o mecanismo built-in do
 * PHP.
 *
 * Op��es dispon�veis:
 * - session_use_cookies: se � para setar um cookie de session no browser do
 *   usu�rio. Usa o valor configurado no php.ini caso n�o informado
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Session
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class StorageDefault extends StorageAbstract
{
	/**
	* @see StorageAbstract#_init()
	*/
	protected function _init(array $options = array())
	{
		$options = array_merge(array(
			'session_use_cookies' => ini_get('session.use_cookies')
		), $options);

		parent::_init($options);

		if (!is_null($this->getOption('session_name')))
		{
			session_name($this->getOption('session_name'));
		}

		if (!is_null(self::$_sessionId))
		{
			session_id(self::$_sessionId);
		}

		if (TRUE == $this->getOption('session_auto_start'))
		{
			$this->start();
		}
	}

	/**
	* @see StorageInterface#read($key)
	*/
	public function read($key)
	{
		$returnValue = NULL;

		if (isset($_SESSION[$key]))
		{
			$returnValue = $_SESSION[$key];
		}

		return $returnValue;
	}

	/**
	* @see StorageInterface#write($key, $value)
	*/
	public function write($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	/**
	* @see StorageInterface#remove($key)
	*/
	public function remove($key)
	{
		unset($_SESSION[$key]);
	}

	/**
	* @see StorageInterface#start()
	*/
	public function start()
	{
		if (!$this->isStarted() && session_start()) {
			self::$_sessionStarted = TRUE;
			self::$_sessionId = session_id();
		}
	}

	/**
	* @see StorageInterface#destroy()
	*/
	public function destroy()
	{
		if ($this->isStarted()) {
			return session_destroy();
		}
	}

	/**
	* @see StorageInterface#regenerate()
	*/
	public function regenerate($destroy = FALSE)
	{
		if ($this->isStarted())
		{
			session_regenerate_id($destroy);
			self::$_sessionId = session_id();
		}
	}

	/**
	* Persiste os dados da session no sistema de arquivos.
	* @see StorageInterface#shutdown()
	*/
	public function shutdown()
	{
		if ($this->isStarted())
		{
			session_write_close();
		}
	}

	/**
	* @link http://br.php.net/manual/en/countable.count.php
	*/
	public function count()
	{
		return count($_SESSION);
	}

	/**
	* @see StorageAbstract#getSessionData()
	*/
	public function getSessionData()
	{
		return $_SESSION;
	}
}
