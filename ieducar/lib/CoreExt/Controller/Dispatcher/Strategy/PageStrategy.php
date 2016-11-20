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

namespace CoreExt\Controller\Dispatcher\Strategy;

use CoreExt\Controller\ControllerInterface;
use CoreExt\Controller\Dispatcher\DispatcherAbstract;
use CoreExt\Controller\Dispatcher\Strategy\StrategyInterface;
use CoreExt\Exception\FileNotFoundException;

/**
 * CoreExt_Controller_Strategy_PageStrategy class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class PageStrategy
	extends DispatcherAbstract
	implements StrategyInterface
{
  /**
	* Inst�ncia de ControllerInterface.
	* @var ControllerInterface
	*/
	protected $_controller = NULL;

	/**
	* Construtor.
	* @see StrategyInterface#__construct($controller)
	*/
	public function __construct(ControllerInterface $controller)
	{
		$this->setController($controller);
	}

	/**
	* @see CStrategyInterfacesetController($controller)
	*/
	public function setController(ControllerInterface $controller)
	{
		$this->_controller = $controller;
		return $this;
	}

	/**
	* @see CoStrategyInterfaceetController()
	*/
	public function getController()
	{
		return $this->_controller;
	}

	/**
	* Determina qual page controller ir� assumir a requisi��o.
	*
	* Determina basicamente o caminho no sistema de arquivos baseado nas
	* informa��es recebidas pela requisi��o e pelas op��es de configura��o.
	*
	* � importante ressaltar que uma a��o no contexto do Page Controller
	* significa uma inst�ncia de PageInterface em si.
	*
	* Um controller nesse contexto pode ser pensado como um m�dulo auto-contido.
	*
	* Exemplo:
	* <code>
	* Um requisi��o HTTP para a URL:
	* http://www.example.com/notas/listar
	*
	* Com ControllerInterface configurado da seguinte forma:
	* basepath = /var/www/ieducar/modules
	* controller_dir = controllers
	*
	* Iria mapear para o arquivo:
	* /var/www/ieducar/modules/notas/controllers/ListarController.php
	* </code>
	*
	* @global DS Constante para DIRECTORY_SEPARATOR
	* @see    CorStrategyInterfacespatch()
	* @todo   Fun��es de controle de buffer n�o funcionam por conta de chamadas
	*         a die() e exit() nas classes clsDetalhe, clsCadastro e clsListagem.
	* @throws FileNotFoundException
	* @return bool
	*/
	public function dispatch()
	{
		$this->setRequest($this->getController()->getRequest());

		$controller     = $this->getControllerName();
		$pageController = ucwords($this->getActionName()) . 'Controller';
		$basepath       = $this->getController()->getOption('basepath');
		$controllerDir  = $this->getController()->getOption('controller_dir');
		$controllerType = $this->getController()->getOption('controller_type');

		$controllerFile = array($basepath, $controller, $controllerDir, $pageController);
		$controllerFile = sprintf('%s.php', implode(DS, $controllerFile));

		if (!file_exists($controllerFile)) {
			throw new FileNotFoundException('Nenhuma classe PageInterface para o controller informado no caminho: "' . $controllerFile . '"');
		}

		require_once $controllerFile;
		$pageController = new $pageController();

		// Injeta as inst�ncias CoreExt_Dispatcher_Interface, CoreExt_Request_Interface
		// Session no page controller
		$pageController->setDispatcher($this);
		$pageController->setRequest($this->getController()->getRequest());
		$pageController->setSession($this->getController()->getSession());

		ob_start();
		$pageController->generate($pageController);
		$this->getController()->getView()->setContents(ob_get_contents());
		ob_end_clean();

		return TRUE;
	}
}
