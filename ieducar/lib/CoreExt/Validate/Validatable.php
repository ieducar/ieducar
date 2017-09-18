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
 * @package   CoreExt_Validate
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Validate/Choice.php';
require_once 'CoreExt/Validate/ChoiceMultiple.php';
require_once 'CoreExt/Validate/String.php';
require_once 'CoreExt/Validate/Numeric.php';

/**
 * CoreExt_Validatable interface.
 *
 * A classe que implementar essa interface ter� definir m�todos que permitam
 * relacionar uma propriedade a um CoreExt_Validate_Interface, criando um
 * mecanismo simples e efetivo de valida��o.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Interface dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
interface CoreExt_Validate_Validatable
{
  /**
   * Retorna TRUE caso a propriedade seja v�lida.
   *
   * @param  string $key
   * @return bool
   */
  public function isValid($key = '');

  /**
   * Configura um CoreExt_Validate_Interface para uma propriedade da classe.
   *
   * @param  string $key
   * @param  CoreExt_Validate_Interface $validator
   * @return CoreExt_Validate_Validatable Prov� interface flu�da
   */
  public function setValidator($key, CoreExt_Validate_Interface $validator);

  /**
   * Retorna a inst�ncia CoreExt_Validate_Interface para uma propriedade da
   * classe ou NULL caso nenhum validador esteja atribu�do.
   *
   * @param  string $key
   * @return CoreExt_Validate_Interface|NULL
   */
  public function getValidator($key);
}