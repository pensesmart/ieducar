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
 * @package   Enum
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

/**
 * Enum abstract class.
 *
 * Prov� uma interface simples de cria��o de inst�ncias semelhantes a um
 * Enum do Java.
 *
 * As semelhan�as s�o poucas mas a inten��o � a de dar uma forma direta de
 * criar tipos enumerados. Para isso, basta subclassificar essa classe e prover
 * valores para o array $_data. Adicionalmente, prover constantes que ajudaram
 * ao usuario da classe a facilmente acessar os valores dos enumerados � uma
 * sugest�o.
 *
 * O stub de teste CoreExt_Enum1Stub � um exemplo de como criar tipos
 * enumerados.
 *
 * Essa classe implementa tamb�m a interface ArrayAccess de SPL, provendo acesso
 * f�cil aos valores do enumerado em uma forma de array:
 *
 * <code>
 * <?php
 * $enum = new Enum();
 * print $enum[Enum::ONE];
 * </code>
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://br2.php.net/manual/en/class.arrayaccess.php ArrayAccess interface
 * @package   Singleton
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @todo      Verificar se � substitu�vel pela implementa��o de Enum dispon�vel
 *            em spl_types do Pecl {@link http://www.php.net/manual/en/splenum.construct.php SplEnum}.
 * @version   @@package_version@@
 */

namespace CoreExt;

use CoreExt\Singleton;
use CoreExt\Exception;

abstract class Enum extends Singleton implements \ArrayAccess
{
	/**
	* Array que emula um enum.
	* @var array
	*/
	protected $_data = array();

	/**
	* Retorna o valor para um dado �ndice de Enum.
	* @param  string|int $key
	* @return mixed
	*/
	public function getValue($key)
	{
		return $this->_data[$key];
	}

	/**
	* Retorna todos os valores de Enum.
	* @return array
	*/
	public function getValues()
	{
		return array_values($this->_data);
	}

	/**
	* Retorna o valor da �ndice para um determinado valor.
	* @param  mixed $value
	* @return int|string
	*/
	public function getKey($value)
	{
		return array_search($value, $this->_data);
	}

	/**
	* Retorna todos os �ndices de Enum.
	* @return array
	*/
	public function getKeys()
	{
		return array_keys($this->_data);
	}

	/**
	* Retorna o array de enums.
	* @return array
	*/
	public function getEnums()
	{
		return $this->_data;
	}

	/**
	* Implementa offsetExists da interface ArrayAccess.
	* @link   http://br2.php.net/manual/en/arrayaccess.offsetexists.php
	* @param  string|int $offset
	* @return bool
	*/
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}

	/**
	* Implementa offsetUnset da interface ArrayAccess.
	* @link  http://br2.php.net/manual/en/arrayaccess.offsetunset.php
	* @throws Exception
	*/
	public function offsetUnset($offset)
	{
		throw new Exception('Um "' . get_class($this) . '" � um objeto read-only.');
	}

	/**
	* Implementa offsetSet da interface ArrayAccess.
	*
	* Uma objeto Enum � apenas leitura.
	*
	* @link   http://br2.php.net/manual/en/arrayaccess.offsetset.php
	* @param  string|int $offset
	* @param  mixed $value
	* @throws Exception
	*/
	public function offsetSet($offset, $value)
	{
		throw new Exception('Um "' . get_class($this) . '" � um objeto read-only.');
	}

	/**
	* Implementa offsetGet da interface ArrayAccess.
	*
	* @link   http://br2.php.net/manual/en/arrayaccess.offsetget.php
	* @param  string|int $offset
	* @return mixed
	*/
	public function offsetGet($offset)
	{
		return $this->_data[$offset];
	}
}
