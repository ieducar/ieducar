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
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
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
 * @package   CoreExt_Configurable
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

/**
 * CoreExt_Configurable interface.
 *
 * Essa interface tem como objetivo prover uma API uniforme para classes que
 * definem par�metros de configura��o. Basicamente prov� apenas o m�todo
 * p�blico setOptions, que recebe um array de par�metros. Como o PHP n�o
 * permite heran�a m�ltipla, essa API apenas refor�a a id�ia de se criar uma
 * uniformidade entre as diferentes classes configur�veis do i-Educar.
 *
 * Uma sugest�o de implementa��o do m�todo setOptions � dada pelo exemplo a
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
 *       sprintf('A classe %s n�o suporta as op��es: %s.', get_class($this), implode(', ', $passedOptions))
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
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Configurable
 * @since     Interface dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
interface CoreExt_Configurable
{
  /**
   * Setter.
   * @param  array $options
   * @return CoreExt_Configurable Prov� interface flu�da
   */
  public function setOptions(array $options = array());

  /**
   * Getter.
   * @return array
   */
  public function getOptions();
}