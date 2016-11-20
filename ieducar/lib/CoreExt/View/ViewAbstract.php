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
 * @package   View
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

/**
 * ViewAbstract abstract class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   View
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */

namespace CoreExt\View;

abstract class ViewAbstract
{
	/**
	* Conte�do gerado pela execu��o de um controller.
	* @var string
	*/
	protected $_contents = NULL;

	/**
	* Setter.
	*
	* @param  string $contents
	* @return ViewAbstract Prov� interface flu�da.
	*/
	public function setContents($contents)
	{
		$this->_contents = $contents;
		return $this;
	}

	/**
	* Getter.
	* @return string
	*/
	public function getContents()
	{
		return $this->_contents;
	}

	/**
	* Implementa��o do m�todo m�gico __toString(). Retorna o valor de $contents.
	* @link http://php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	* @return string
	*/
	public function __toString()
	{
		return $this->getContents();
	}
}
