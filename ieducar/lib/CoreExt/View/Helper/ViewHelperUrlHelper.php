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
 * ViewHelperUrlHelper class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   View
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class ViewHelperUrlHelper extends ViewHelperAbstract
{
	/**
	* Constantes para definir que parte da URL deve ser gerada no m�todo
	* url().
	*/
	const URL_SCHEME = 1;
	const URL_HOST = 4;
	const URL_PORT = 16;
	const URL_USER = 32;
	const URL_PASS = 64;
	const URL_PATH = 128;
	const URL_QUERY = 128;
	const URL_FRAGMENT = 256;

	/**
	* @var array
	*/
	private $_components = array(
		'scheme' => self::URL_SCHEME,
		'host' => self::URL_HOST,
		'port' => self::URL_PORT,
		'user' => self::URL_USER,
		'pass' => self::URL_PASS,
		'path' => self::URL_PATH,
		'query' => self::URL_QUERY,
		'fragment' => self::URL_FRAGMENT
	);

	/**
	* URL base para a gera��o de url absoluta.
	* @var string
	*/
	protected $_baseUrl = '';

	/**
	* Schema padr�o para a gera��o de url absoluta.
	* @var string
	*/
	protected $_schemeUrl = 'http://';

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
		return self::_getInstance(__CLASS__);
	}

	/**
	* Setter para $_baseUrl.
	* @param string $baseUrl
	*/
	public static function setBaseUrl($baseUrl)
	{
		$instance = self::getInstance();
		$instance->_baseUrl = $baseUrl;
	}

	/**
	* Retorna uma URL formatada. Interface externa.
	*
	* A gera��o da URL � bastante din�mica e simples, j� que aceita tanto
	* caminhos absolutos ou relativos.
	*
	* As op��es para o array $options s�o:
	* - absolute: gera uma URL absoluta
	* - query: array associativo para criar uma query string (ver ex.)
	* - fragment: adiciona um fragmento ao final da URL
	* - components: um valor num�rico que define at� que componente dever� ser
	* retornado na URL parseada. Uma valor de URL_HOST, por exemplo, retornaria
	* os componentes URL_FRAGMENT e URL_HOST. Veja valores das constantes URL_*
	* para saber qual o n�vel de detalhe desejado
	*
	* Exemplo:
	* <code>
	* <?php
	* $options = array(
	*   'absolute' => TRUE,
	*   'query' => array('param1' => 'value1', 'param2' => 'value2'),
	*   'fragment' => 'Fragment',
	*   'components' => ViewHelperUrlHelper::URL_HOST
	* );
	* // http://example.com/index?param1=value1&param2=value2#Fragment
	*
	* $options = array(
	*   'absolute' => TRUE,
	*   'query' => array('param1' => 'value1', 'param2' => 'value2'),
	*   'fragment' => 'Fragment',
	*   'components' => ViewHelperUrlHelper::URL_HOST
	* );
	* print ViewHelperUrlHelper::url('example.com/index', $options);
	* // http://example.com
	* </code>
	*
	* @param  string  $path     O caminho relativo ou absoluto da URL
	* @param  array   $options  Op��es para gera��o da URL
	* @return string
	*/
	public static function url($path, array $options = array())
	{
		$instance = self::getInstance();
		return $instance->_url($path, $options);
	}

	/**
	* Retorna uma URL formatada. Veja a documenta��o de url().
	*
	* @param  string  $path
	* @param  array   $options
	* @return string
	*/
	protected function _url($path, array $options = array())
	{
		$url = array(
			'scheme' => '',
			'host' => '',
			'user' => '',
			'pass' => '',
			'path' => '',
			'query' => '',
			'fragment' => ''
		);

		$parsedUrl = parse_url($path);
		$url = array_merge($url, $parsedUrl);

		// Adiciona "://" caso o scheme seja parseado (caso das URLs absolutas impl�citas)
		if ('' != $url['scheme'])
		{
			$url['scheme'] = $url['scheme'] . '://';
		}

		// Op��es do m�todo
		if (isset($options['absolute']))
		{
			$url['scheme'] = $url['scheme'] != '' ? $url['scheme'] : $this->_schemeUrl;
			$url['host']   = $url['host'] != '' ? $url['host'] : $this->_baseUrl . '/';
		}

		if (isset($options['query']))
		{
			$url['query'] = '?' . http_build_query($options['query']);
		}
		if (isset($options['fragment']))
		{
			$url['fragment'] = '#' . (string)$options['fragment'];
		}

		// Remove da URL final os componentes que tem valor maior que o especificado
		// por 'components'.
		if (isset($options['components']))
		{
			foreach ($this->_components as $key => $value)
			{
				if ($value > $options['components'])
				{
					unset($url[$key]);
				}
			}
		}

		return implode('', $url);
	}

	/**
	* Retorna um link HTML simples. Interface externa.
	*
	* @param  string  $text     O texto a ser apresentado como link HTML
	* @param  string  $path     O caminho relativo ou absoluto da URL do link
	* @param  array   $options  Op��es para gerar a URL do link
	* @return string
	*/
	public static function l($text, $path, array $options = array())
	{
		$instance = self::getInstance();
		return $instance->_link($text, $path, $options);
	}

	/**
	* Retorna um link HTML simples.
	*
	* @param  string  $text     O texto a ser apresentado como link HTML
	* @param  string  $path     O caminho relativo ou absoluto da URL do link
	* @param  array   $options  Op��es para gerar a URL do link
	* @return string
	*/
	protected function _link($text, $path, array $options = array())
	{
		return sprintf('<a href="%s">%s</a>', self::url($path, $options), $text);
	}
}
