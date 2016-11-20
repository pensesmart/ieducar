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

use CoreExt\Controller\ControllerAbstract;
use CoreExt\Controller\ControllerInterface;
use CoreExt\View\ViewAbstract;
use CoreExt\Configurable;
use CoreExt\View;
use CoreExt\Controller\Dispatcher\DispatcherInterface;
use CoreExt\Controller\Dispatcher\Strategy\PageStrategy;
use CoreExt\Controller\Dispatcher\Strategy\FrontStrategy;

/**
 * ControllerFront class.
 *
 * Essa � uma implementa��o simples do design pattern {@link http://martinfowler.com/eaaCatalog/frontController.html front controller},
 * que tem como objetivo manusear e encaminhar a requisi��o para uma classe
 * que se responsabilize pelo processamento do recurso solicitado.
 *
 * Apesar de ser um front controller, o encaminhamento para uma classe
 * {@link http://en.wikipedia.org/wiki/Command_pattern command} n�o est�
 * implementado.
 *
 * Entretanto, est� dispon�vel o encaminhamento para uma classe que implemente
 * o pattern {@link http://martinfowler.com/eaaCatalog/pageController.html page controller},
 * ou seja, qualquer classe que implemente a interface
 * PageInterface.
 *
 * O processo de encaminhamento (dispatching), � definido por uma classe
 * {@link http://en.wikipedia.org/wiki/Strategy_pattern strategy}.
 *
 * Algumas op��es afetam o comportamento dessa classe. As op��es dispon�veis
 * para configurar uma inst�ncia da classe s�o:
 * - basepath: diret�rio em que os implementadores de command e page controller
 *   ser�o procurados
 * - controller_dir: determina o nome do diret�rio em que os controllers dever�o
 *   estar salvos
 * - controller_type: tipo de controller a ser instanciado. Uma inst�ncia de
 *   ControllerFront pode usar apenas um tipo por processo de
 *   dispatch() e o valor dessa op��o determina qual strategy de dispatch ser�
 *   utilizada (CoreExt_Controller_Strategy).
 *
 * Por padr�o, os valores de controller_dir e controller_type s�o definidos para
 * 'Views' e 2, respectivamente. Isso significa que a estrat�gia de page
 * controller ser� utilizada durante a chamada ao m�todo dispatch().
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class ControllerFront extends ControllerAbstract
{
	/**
	* Op��es para defini��o de qual tipo de controller utilizar durante a
	* execu��o de dispatch().
	* @var int
	*/
	const CONTROLLER_FRONT = 1;
	const CONTROLLER_PAGE  = 2;

	/**
	* A inst�ncia singleton de ControllerInterface.
	* @var ControllerInterface|null
	*/
	protected static $_instance = NULL;

	/**
	* Op��es de configura��o geral da classe.
	* @var array
	*/
	protected $_options = array(
		'basepath'        => NULL,
		'controller_type' => self::CONTROLLER_PAGE,
		'controller_dir'  => 'Views'
	);

	/**
	* Cont�m os valores padr�o da configura��o.
	* @var array
	*/
	protected $_defaultOptions = array();

	/**
	* Uma inst�ncia de ViewAbstract
	* @var ViewAbstract
	*/
	protected $_view = NULL;

	/**
	* Construtor singleton.
	*/
	protected function __construct()
	{
		$this->_defaultOptions = $this->getOptions();
	}

	/**
	* Retorna a inst�ncia singleton.
	* @return ControllerFront
	*/
	public static function getInstance()
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	* Recupera os valores de configura��o original da inst�ncia.
	* @return Configurable Prov� interface flu�da
	*/
	public function resetOptions()
	{
		$this->setOptions($this->_defaultOptions);
		return $this;
	}

	/**
	* Encaminha a execu��o para o objeto CoreExt_Dispatcher_Interface apropriado.
	* @return ControllerInterface Prov� interface flu�da
	* @see ControllerInterface#dispatch()
	*/
	public function dispatch()
	{
		$this->_getControllerStrategy()->dispatch();
		return $this;
	}

	/**
	* Retorna o conte�do gerado pelo controller.
	* @return string
	*/
	public function getViewContents()
	{
		return $this->getView()->getContents();
	}

	/**
	* Setter.
	* @param ViewAbstract $view
	* @return ControllerInterface Prov� interface flu�da
	*/
	public function setView(ViewAbstract $view)
	{
		$this->_view = $view;
		return $this;
	}

	/**
	* Getter para uma inst�ncia de ViewAbstract.
	*
	* Inst�ncia via lazy initialization uma inst�ncia de View caso
	* nenhuma seja explicitamente atribu�da a inst�ncia atual.
	*
	* @return ViewAbstract
	*/
	public function getView()
	{
		if (is_null($this->_view))
		{
			$this->setView(new View());
		}
		return $this->_view;
	}

	/**
	* Getter para uma inst�ncia de DispatcherInterface.
	*
	* Inst�ncia via lazy initialization uma inst�ncia de
	* CoreExt_Controller_Dispatcher caso nenhuma seja explicitamente
	* atribu�da a inst�ncia atual.
	*
	* @return DispatcherInterface
	*/
	public function getDispatcher()
	{
		if (is_null($this->_dispatcher))
		{
			$this->setDispatcher($this->_getControllerStrategy());
		}
		return $this->_dispatcher;
	}

	/**
	* Getter para a estrat�gia de controller, definida em runtime.
	* @return PageStrategy|FrontStrategy
	*/
	protected function _getControllerStrategy()
	{
		switch($this->getOption('controller_type')) {
			case 1:
				$strategy = 'FrontStrategy';
			break;
			case 2:
				$strategy = 'PageStrategy';
			break;
		}
		return new $strategy($this);
	}
}
