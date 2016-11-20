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

namespace CoreExt\View\Helper;

use CoreExt\View\Helper\ViewHelperAbstract;

/**
 * ViewHelperTable class.
 *
 * Helper para a cria��o de tabelas HTML atrav�s de arrays associativos.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   View
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class ViewHelperTable extends ViewHelperAbstract
{
	protected $_header = array();
	protected $_body   = array();
	protected $_footer = array();

	/**
	* Construtor singleton.
	*/
	protected function __construct()
	{
	}

	/**
	* Retorna uma inst�ncia singleton.
	* @return ViewHelperAbstract
	*/
	public static function getInstance()
	{
		$instance = self::_getInstance(__CLASS__);
		$instance->resetTable();
		return $instance;
	}

	/**
	* Reseta os dados da tabela.
	* @return ViewHelperTable Prov� interface flu�da
	*/
	public function resetTable()
	{
		$this->_header = array();
		$this->_body   = array();
		$this->_footer = array();
		return $this;
	}

	/**
	* Adiciona um array para um row de tabela para a tag cabe�alho (thead). Cada
	* valor do array precisa ser um array associativo, podendo conter os
	* seguintes atributos:
	*
	* - data: o valor que ser� impresso na c�lula (td) da tabela
	* - colspan: valor inteiro para colspan {@link }
	* - attributes: array associativo onde o nome do atributo � o �ndice
	*
	* <code>
	* $table = ViewHelperTable::getInstance();
	*
	* $data = array(
	*   array('data' => 'Example 1', 'colspan' => 1),
	*   array('data' => 'Example 2', 'attributes' => array(
	*     'class' => 'tdd1', 'style' => 'border: 1px dashed green'
	*   ))
	* );
	*
	* $table->addHeaderRow($data);
	* print $table;
	*
	* // <table>
	* //   <thead>
	* //     <tr>
	* //       <td colspan="1">Example 1</td>
	* //       <td class="tdd1" style="border: 1px dashed green">Example 2</td>
	* //     </tr>
	* //   </thead>
	* </code>
	*
	* @param array $cols
	* @return ViewHelperTable Prov� interface flu�da
	*/
	public function addHeaderRow(array $cols, array $rowAttributes = array())
	{
		$this->_header[] = array('row' => $cols, 'attributes' => $rowAttributes);
		return $this;
	}

	/**
	* Adiciona um array para um row de tabela para a tag corpo (tbody).
	*
	* @param $cols
	* @return ViewHelperTable Prov� interface flu�da
	* @see ViewHelperTable::addHeaderRow(array $cols)
	*/
	public function addBodyRow(array $cols, array $rowAttributes = array())
	{
		$this->_body[] = array('row' => $cols, 'attributes' => $rowAttributes);
		return $this;
	}

	/**
	* Adiciona um array para um row de tabela para a tag rodap� (tfooter).
	*
	* @param $cols
	* @return ViewHelperTable Prov� interface flu�da
	* @see ViewHelperTable::addHeaderRow(array $cols)
	*/
	public function addFooterRow(array $cols, array $rowAttributes = array())
	{
		$this->_footer[] = array('row' => $cols, 'attributes' => $rowAttributes);
		return $this;
	}

	/**
	* Cria uma tabela HTML usando os valores passados para os m�todos add*().
	*
	* @param array $tableAttributes
	* @return string
	*/
	public function createTable(array $tableAttributes = array())
	{
		return sprintf(
			'<table%s>%s%s%s%s%s</table>',
			$this->_attributes($tableAttributes),
			PHP_EOL,
			$this->createHeader(TRUE),
			$this->createBody(TRUE),
			$this->createFooter(TRUE),
			PHP_EOL
		);
	}

	/**
	* Cria a se��o thead de uma tabela.
	* @param bool $indent
	* @return string
	*/
	public function createHeader($indent = FALSE)
	{
		return $this->_createHtml($this->_getTag('thead', $indent), $this->_header, $indent);
	}

	/**
	* Cria a se��o tbody de uma tabela.
	* @param bool $indent
	* @return string
	*/
	public function createBody($indent = FALSE)
	{
		return $this->_createHtml($this->_getTag('tbody', $indent), $this->_body, $indent);
	}

	/**
	* Cria a se��o tfooter de uma tabela.
	* @param bool $indent
	* @return string
	*/
	public function createFooter($indent = FALSE)
	{
		return $this->_createHtml($this->_getTag('tfooter', $indent), $this->_footer, $indent);
	}

	/**
	* Formata uma string para o uso de _createHtml().
	*
	* @param string $name
	* @param bool $indent
	* @return string
	*/
	protected function _getTag($name, $indent = TRUE)
	{
		$indent = $indent ? '  ' : '';
		return sprintf('%s<%s>%s%s%s</%s>', $indent, $name, '%s', '%s', $indent, $name);
	}

	/**
	* Cria c�digo Html de um row de tabela.
	*
	* @param string $closure
	* @param array $rows
	* @param bool $indent
	* @return string
	*/
	protected function _createHtml($closure, $rows = array(), $indent = FALSE)
	{
		$html = '';
		$indent = $indent ? '  ' : '';

		foreach ($rows as $cols)
		{
			$row = '';

			$cells = $cols['row'];
			$rowAttributes = $cols['attributes'];

			foreach ($cells as $cell)
			{
				$attributes = isset($cell['attributes']) ? $cell['attributes'] : array();
				$data       = isset($cell['data']) ? $cell['data'] : '&nbsp;';

				if (isset($cell['colspan']))
				{
					$attributes['colspan'] = $cell['colspan'];
				}

				$row .= sprintf('%s    %s<td%s>%s</td>', PHP_EOL, $indent,
				$this->_attributes($attributes), $data);
			}

			$html .= sprintf('  %s<tr%s>%s%s%s  </tr>%s', $indent,
			$this->_attributes($rowAttributes), $row, PHP_EOL, $indent, PHP_EOL);
		}

		if (0 == strlen(trim($html)))
		{
			return '';
		}

		return sprintf($closure, PHP_EOL, $html);
	}

	/**
	* Cria uma string de atributos HTML.
	*
	* @param array $attributes
	* @return string
	*/
	protected function _attributes(array $attributes = array())
	{
		if (0 == count($attributes))
		{
			return '';
		}

		$html = array();
		ksort($attributes);
		foreach ($attributes as $key => $value)
		{
			$html[] = sprintf('%s="%s"', $key, $value);
		}

		return ' ' . implode(' ', $html);
	}

	/**
	* Implementa m�todo m�gico __toString().
	* @link
	*/
	public function __toString()
	{
		return $this->createTable();
	}
}
