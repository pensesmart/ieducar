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

namespace CoreExt\Controller\Dispatcher;

use CoreExt\Configurable;
use CoreExt\Controller\Request\RequestInterface;
use CoreExt\Controller\ControllerRequest;
use InvalidArgumentException;

/**
 * DispatcherAbstract abstract class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
abstract class DispatcherAbstract
	implements DispatcherInterface, Configurable
{
	/**
	* Inst�ncia de RequestInterface
	* @var RequestInterface
	*/
	protected $_request = NULL;

	/**
	* Op��es de configura��o geral da classe.
	* @var array
	*/
	protected $_options = array(
		'controller_default_name' => 'index',
		'action_default_name' => 'index'
	);

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
	* @see DispatcherInterface#setRequest($request)
	*/
	public function setRequest(RequestInterface $request)
	{
		$this->_request = $request;
		return $this;
	}

	/**
	* @see DispatcherInterface#getRequest()
	*/
	public function getRequest()
	{
		if (is_null($this->_request))
		{
			require_once 'CoreExt/Controller/Request.php';
			$this->setRequest(new ControllerRequest());
		}

		return $this->_request;
	}

	/**
	* Retorna o componente 'path' de uma URL como array, onde cada item
	* corresponde a um elemento do path.
	*
	* Exemplo:
	* <code>
	* <?php
	* // $_SERVER['REQUEST_URI'] = 'http://www.example.com/path1/path2/path3?qs=1';
	* print_r($this->_getUrlPath());
	* // Array
	* (
	*   [0] => path1
	*   [1] => path2
	*   [2] => path3
	* )
	* </code>
	*
	* @return array
	*/
	protected function _getUrlPath()
	{
		$path    = parse_url($this->getRequest()->get('REQUEST_URI'), PHP_URL_PATH);
		$path    = explode('/', $path);

		$baseurl = parse_url($this->getRequest()->getBaseurl(), PHP_URL_PATH);
		$baseurl = explode('/', $baseurl);

		$script  = explode('/', $this->getRequest()->get('SCRIPT_FILENAME'));
		$script  = array_pop($script);

		// Retorna os elementos de path diferentes entre a REQUEST_URI e a baseurl
		$path = array_diff_assoc($path, $baseurl);

		$items = count($path);

		if ($items >= 1)
		{
			// Combina os elementos em um array cujo o �ndice come�a do '0'
			$path = array_combine(range(0, $items - 1), $path);

			// Caso o primeiro elemento seja o nome do script, remove-o
			if (strtolower($script) === strtolower($path[0]) || '' === $path[0])
			{
				array_shift($path);
			}
		} else {
			$path = array();
		}

		return $path;
	}

	/**
	* @see DispatcherInterface#getController()
	*/
	public function getControllerName()
	{
		$path = $this->_getUrlPath();
		return isset($path[0]) ? $path[0] : $this->getOption('controller_default_name');
	}

	/**
	* @see DispatcherInterface#getAction()
	*/
	public function getActionName()
	{
		$path = $this->_getUrlPath();
		return isset($path[1]) ? $path[1] : $this->getOption('action_default_name');
	}
}
