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
 * @package   Singleton
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

/**
 * Singleton abstract class.
 *
 * Funciona como uma interface de atalho para minimizar a duplica��o de c�digo
 * para criar inst�ncias singleton. Internamente, entretanto, funciona como um
 * {@link http://martinfowler.com/eaaCatalog/registry.html Registry} j� que
 * todas as suas subclasses estar�o armazenadas em um array est�tico desta
 * classe.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://martinfowler.com/eaaCatalog/registry.html Registry pattern
 * @package   Singleton
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */

namespace CoreExt;

use CoreExt\Exception;

abstract class Singleton
{
	/**
	* A inst�ncia singleton de Singleton
	* @var array
	*/
	private static $_instance = array();

	/**
	* Construtor.
	*/
	private function __construct()
	{
	}

	/**
	* Sobrescreva esse m�todo para garantir que a subclasse possa criar um
	* singleton. Esta deve fazer uma chamada ao m�todo _getInstance, passando
	* uma string que tenha como valor o nome da classe. Uma forma conveniente
	* de fazer isso � chamando _getInstance passando como par�metro a constante
	* m�gica __CLASS__.
	*
	* Exemplo:
	* <code>
	* <?php
	* ... // extends Singleton
	* public static function getInstance()
	* {
	*   return self::_getInstance(__CLASS__);
	* }
	* </code>
	*
	* @return Singleton
	*/
	public static function getInstance()
	{
		throw new Exception('� necess�rio sobrescrever o m�todo "getInstance()" de Singleton.');
	}

	/**
	* Retorna uma inst�ncia singleton, instanciando-a quando necess�rio.
	*
	* @param  string $self  Nome da subclasse de Singleton que ser� instanciada
	* @return Singleton
	*/
	protected static function _getInstance($self)
	{
		if (!isset(self::$_instance[$self])) {
			self::$_instance[$self] = new $self();
		}
		return self::$_instance[$self];
	}
}
