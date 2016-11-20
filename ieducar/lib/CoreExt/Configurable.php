<?php

/**
 * i-Educar - Sistema de gestï¿½o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaï¿½
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa ï¿½ software livre; vocï¿½ pode redistribuï¿½-lo e/ou modificï¿½-lo
 * sob os termos da Licenï¿½a Pï¿½blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versï¿½o 2 da Licenï¿½a, como (a seu critï¿½rio)
 * qualquer versï¿½o posterior.
 *
 * Este programa ï¿½ distribuï¿½ï¿½do na expectativa de que seja ï¿½til, porï¿½m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implï¿½ï¿½cita de COMERCIABILIDADE OU
 * ADEQUAï¿½ï¿½O A UMA FINALIDADE ESPECï¿½FICA. Consulte a Licenï¿½a Pï¿½blica Geral
 * do GNU para mais detalhes.
 *
 * Vocï¿½ deve ter recebido uma cï¿½pia da Licenï¿½a Pï¿½blica Geral do GNU junto
 * com este programa; se nï¿½o, escreva para a Free Software Foundation, Inc., no
 * endereï¿½o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Eriksen Costa Paixï¿½o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Configurable
 * @since     Arquivo disponï¿½vel desde a versï¿½o 1.1.0
 * @version   $Id$
 */

/**
 * Configurable interface.
 *
 * Essa interface tem como objetivo prover uma API uniforme para classes que
 * definem parï¿½metros de configuraï¿½ï¿½o. Basicamente provï¿½ apenas o mï¿½todo
 * pï¿½blico setOptions, que recebe um array de parï¿½metros. Como o PHP nï¿½o
 * permite heranï¿½a mï¿½ltipla, essa API apenas reforï¿½a a idï¿½ia de se criar uma
 * uniformidade entre as diferentes classes configurï¿½veis do i-Educar.
 *
 * Uma sugestï¿½o de implementaï¿½ï¿½o do mï¿½todo setOptions ï¿½ dada pelo exemplo a
 * seguir:
 * <code>
 * <?php
 * protected $_options = array(
 *   'option1' => NULL,
 *   'option2' => NULL
 * );
 *
 * public function setOptions(array $options = array())
 * {
 *   $defaultOptions = array_keys($this->getOptions());
 *   $passedOptions  = array_keys($options);
 *
 *   if (0 < count(array_diff($passedOptions, $defaultOptions))) {
 *     throw new InvalidArgumentException(
 *       sprintf('A classe %s nï¿½o suporta as opï¿½ï¿½es: %s.', get_class($this), implode(', ', $passedOptions))
 *     );
 *   }
 *
 *   $this->_options = array_merge($this->getOptions(), $options);
 *   return $this;
 * }
 *
 * public function getOptions()
 * {
 *   return $this->_options;
 * }
 * </code>
 *
 * @author    Eriksen Costa Paixï¿½o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Configurable
 * @since     Interface disponï¿½vel desde a versï¿½o 1.1.0
 * @version   @@package_version@@
 */

namespace CoreExt;

interface Configurable
{
	/**
	* Setter.
	* @param  array $options
	* @return Configurable Provê interface fluída
	*/
	public function setOptions(array $options = array());

	/**
	* Getter.
	* @return array
	*/
	public function getOptions();
}
