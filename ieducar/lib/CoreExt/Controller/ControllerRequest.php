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
 * @package   CoreExt_Controller
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

namespace CoreExt\Controller;

use CoreExt\Controller\Request\RequestInterface;
use InvalidArgumentException;
use CoreExt\View\Helper\ViewHelperUrlHelper;

/**
 * ControllerRequest class.
 *
 * Classe de gerenciamento de dados de uma requisi��o HTTP.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class ControllerRequest implements RequestInterface
{
	/**
	* Op��es de configura��o geral da classe.
	* @var array
	*/
	protected $_options = array(
		'baseurl' => NULL
	);

	/**
	* Construtor.
	* @param array $options
	*/
	public function __construct(array $options = array())
	{
		$this->setOptions($options);
	}

	/**
	* @see Configurable#setOptions($options)
	*/
	public function setOptions(array $options = array())
	{
		$defaultOptions = array_keys($this->getOptions());
		$passedOptions  = array_keys($options);

		if (0 < count(array_diff($passedOptions, $defaultOptions))) {
			throw new InvalidArgumentException(
				sprintf('A classe %s n�o suporta as op��es: %s.', get_class($this), implode(', ', $passedOptions))
			);
		}

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
	* @return mixed|NULL
	*/
	protected function _getOption($key)
	{
		return $this->_hasOption($key) ? $this->_options[$key] : NULL;
	}

	/**
	* Implementa��o do m�todo m�gico __get().
	*
	* @param  string $key
	* @return mixed
	*/
	public function __get($key)
	{
		switch (true) 
		{
			case isset($_GET[$key]):
				return $_GET[$key];
			case isset($_POST[$key]):
				return $_POST[$key];
			case isset($_COOKIE[$key]):
				return $_COOKIE[$key];
			case isset($_SERVER[$key]):
				return $_SERVER[$key];
			default:
				break;
		}
		return NULL;
	}

	/**
	* Getter para as vari�veis de requisi��o.
	* @param string $key
	* @return mixed
	*/
	public function get($key)
	{
		return $this->__get($key);
	}

	/**
	* Implementa��o do m�todo m�gico __isset().
	*
	* @link   http://php.net/manual/en/language.oop5.overloading.php
	* @param  string $key
	* @return bool
	*/
	public function __isset($key)
	{
		return isset($val);
	}

	/**
	* Setter para a op��o de configura��o baseurl.
	* @param string $url
	* @return RequestInterface Prov� interface flu�da
	*/
	public function setBaseurl($url)
	{
		$this->setOptions(array('baseurl' => $url));
		return $this;
	}

	/**
	* Getter para a op��o de configura��o baseurl.
	*
	* Caso a op��o n�o esteja configurada, determina um valor baseado na
	* vari�vel $_SERVER['REQUEST_URI'] da requisi��o, usando apenas os
	* componentes scheme e path da URL. Veja {@link http://php.net/parse_url}
	* para mais informa��es sobre os componentes de uma URL.
	*
	* @return string
	*/
	public function getBaseurl()
	{
		if (is_null($this->_getOption('baseurl'))) {
			$url = ViewHelperUrlHelper::url(
				$this->get('REQUEST_URI'),
				array('absolute' => TRUE, 'components' => ViewHelperUrlHelper::URL_HOST)
			);
			$this->setBaseurl($url);
		}
		
		return $this->_getOption('baseurl');
	}
}
