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
 * @package   CoreExt_Validate
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

namespace CoreExt\Validate;

use CoreExt\Validate\CoreExt_Validate_Abstract;
use CoreExt\Locale;
use Exception;

/**
 * CoreExt_Validate_Numeric class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Validate_Numeric extends CoreExt_Validate_Abstract
{
	/**
	* @see CoreExt_Validate_Abstract#_getDefaultOptions()
	*/
	protected function _getDefaultOptions()
	{
		return array(
			'min'       => NULL,
			'max'       => NULL,
			'trim'      => FALSE,
			'invalid'   => 'O valor "@value" n�o � um tipo num�rico',
			'min_error' => '"@value" � menor que o valor m�nimo permitido (@min)',
			'max_error' => '"@value" � maior que o valor m�ximo permitido (@max)',
		);
	}

	/**
	* @see CoreExt_DataMapper#_getFindStatment($pkey) Sobre a convers�o com floatval()
	* @see CoreExt_Validate_Abstract#_validate($value)
	*/
	protected function _validate($value)
	{
		if (FALSE === $this->getOption('required') && is_null($value))
		{
			return TRUE;
		}

		if (!is_numeric($value))
		{
			throw new Exception($this->_getErrorMessage('invalid', array('@value' => $value)));
		}

		// Converte usando floatval para evitar problemas com range do tipo int.
		$value = floatval($value);

		if ($this->_hasOption('min') && $value < floatval($this->getOption('min')))
		{
			throw new Exception($this->_getErrorMessage('min_error', array(
				'@value' => $value, '@min' => $this->getOption('min')
			)));
		}

		if ($this->_hasOption('max') && $value > floatval($this->getOption('max')))
		{
			throw new Exception($this->_getErrorMessage('max_error', array(
				'@value' => $value, '@max' => $this->getOption('max')
			)));
		}

		return TRUE;
	}

	/**
	* Realiza um sanitiza��o de acordo com o locale, para permitir que valores
	* flutuantes ou n�meros de precis�o arbitr�ria utilizem a pontua��o sem
	* localiza��o.
	*
	* @see CoreExt_Validate_Abstract#_sanitize($value)
	*/
	protected function _sanitize($value)
	{
		$locale = Locale::getInstance();
		$decimalPoint = $locale->getCultureInfo('decimal_point');

		// Verifica se possui o ponto decimal do locale e substitui para o
		// padr�o do locale en_US (ponto ".")
		if (FALSE !== strstr($value, $decimalPoint))
		{
			$value = strtr($value, $decimalPoint, '.');
			$value = floatval($value);
		}

		return parent::_sanitize($value);
	}
}
