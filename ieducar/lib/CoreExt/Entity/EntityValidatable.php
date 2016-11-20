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
 * @package   Entity
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

namespace CoreExt\Entity;

use CoreExt\Validate\CoreExt_Validate_Validatable;
use CoreExt\Entity;

/**
 * EntityValidatable interface.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Entity
 * @since     Interface dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
interface EntityValidatable extends CoreExt_Validate_Validatable
{
	/**
	* Configura uma cole��o de CoreExt_Validate_Interface na inst�ncia.
	* @return Entity Prov� interface flu�da
	*/
	public function setValidatorCollection(array $validators);

	/**
	* Retorna um array de itens CoreExt_Validate_Interface da inst�ncia.
	* @return array
	*/
	public function getValidatorCollection();

	/**
	* Retorna um array de CoreExt_Validate_Interface padr�o para as propriedades
	* de Entity.
	*
	* Cada item do array precisa ser um item associativo com o mesmo nome do
	* atributo p�blico definido pelo array $_data:
	*
	* <code>
	* <?php
	* // Uma classe concreta de Entity com as propriedades p�blicas
	* // nome e telefone poderia ter os seguintes validadores.
	* array(
	*   'nome' => new CoreExt_Validate_Alpha(),
	*   'telefone' => new CoreExt_Validate_Alphanum()
	* );
	* </code>
	*
	* @return array
	*/
	public function getDefaultValidatorCollection();
}
