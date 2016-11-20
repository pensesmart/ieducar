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

use CoreExt\Session\Storage\StorageInterface;

/**
 * StorageAbstract abstract class.
 *
 * Implementa opera��es b�sicas para facilitar a implementa��o de
 * StorageInterface.
 *
 * Op��es dispon�veis:
 * - session_name: o nome da session, o padr�o � o valor definido no php.ini
 * - session_auto_start: se a session deve ser iniciada na instancia��o da
 *   classe. Padr�o � TRUE
 * - session_auto_shutdown: se um m�todo de shutdown deve ser chamado no
 *   encerramento da execu��o do script PHP. Padr�o � TRUE.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Session
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
abstract class StorageAbstract
	implements StorageInterface, \Countable
{
	/**
	* Flag para definir se a session foi iniciada ou n�o, �til para impedir que
	* fun��es que enviem headers sejam chamadas novamente (session_start, p.ex.)
	* @var bool
	*/
	protected static $_sessionStarted = FALSE;

	/**
	* Id da session atual.
	* @var string
	*/
	protected static $_sessionId = NULL;

	/**
	* Op��es de configura��o geral da classe.
	* @var array
	*/
	protected $_options = array(
		'session_name'          => NULL,
		'session_auto_start'    => TRUE,
		'session_auto_shutdown' => TRUE
	);

	/**
	* Construtor.
	* @param array $options Array de op��es de configura��o.
	*/
	public function __construct(array $options = array())
	{
		$this->_init($options);

		if (TRUE == $this->getOption('session_auto_shutdown')) {
			register_shutdown_function(array($this, 'shutdown'));
		}
	}

	/**
	* M�todo de inicializa��o do storage. As subclasses devem sobrescrever
	* este m�todo para alterar o comportamento do mecanismo de session do PHP.
	*
	* @return StorageAbstract Prov� interfae flu�da
	*/
	protected function _init(array $options = array())
	{
		$this->setOptions($options);
	}

	/**
	* @see Configurable#setOptions($options)
	*/
	public function setOptions(array $options = array())
	{
		$this->_options = array_merge($this->getOptions(), $options);
		return $this;
	}

	/**
	* @see Configurable#getOptions()
	*/
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	* Verifica se uma op��o est� setada.
	*
	* @param string $key
	* @return bool
	*/
	protected function _hasOption($key)
	{
		return array_key_exists($key, $this->_options);
	}

	/**
	* Retorna um valor de op��o de configura��o ou NULL caso a op��o n�o esteja
	* setada.
	*
	* @param string $key
	* @return mixed|null
	*/
	public function getOption($key)
	{
		return $this->_hasOption($key) ? $this->_options[$key] : NULL;
	}

	/**
	* Getter.
	* @return string
	*/
	public static function getSessionId()
	{
		return self::$_sessionId;
	}

	/**
	* Getter.
	* @return bool
	*/
	public static function isStarted()
	{
		return self::$_sessionStarted;
	}

	/**
	* Getter.
	*
	* Deve ser implementado pelas subclasses para retornar o array de dados
	* persistidos na session, permitindo que clientes iterem livremente pelos
	* dados.
	*
	* @return array
	*/
	public abstract function getSessionData();
}
