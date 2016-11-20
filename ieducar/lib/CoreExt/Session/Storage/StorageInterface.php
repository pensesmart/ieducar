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
 * @package   Session
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

namespace CoreExt\Session\Storage;

use CoreExt\Configurable;

/**
 * StorageInterface interface.
 *
 * Interface m�nima para que um storage de session possa ser criado. Define
 * os m�todos b�sicos de escrita e inicializa��o/destrui��o de uma session.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Session
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
interface StorageInterface extends Configurable
{
	/**
	* Inicializa a session.
	*/
	public function start();

	/**
	*
	* @param string $key
	* @return mixed
	*/
	public function read($key);

	/**
	* Persiste um dado valor na session.
	* @param string $key
	* @param mixed $value
	*/
	public function write($key, $value);

	/**
	* Remove/apaga um dado na session.
	* @param string $key
	*/
	public function remove($key);

	/**
	* Destr�i os dados de uma session.
	*/
	public function destroy();

	/**
	* Gera um novo id para a session.
	*/
	public function regenerate($destroy = FALSE);

	/**
	* Persiste os dados da session no storage definido ao final da execu��o
	* do script PHP.
	*/
	public function shutdown();
}
