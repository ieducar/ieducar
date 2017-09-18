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

require_once 'CoreExt/Validate/Abstract.php';

/**
 * CoreExt_Validate_Choice class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Validate_Choice extends CoreExt_Validate_Abstract
{
  /**
   * @see CoreExt_Validate_Abstract#_getDefaultOptions()
   */
  protected function _getDefaultOptions()
  {
    $options = array(
      'choices'  => array(),
      'multiple' => FALSE,
      'trim'     => FALSE,
      'choice_error'   => 'A op��o "@value" n�o existe.',
    );

    $options['multiple_error'] = array(
      'singular' => $options['choice_error'],
      'plural'   => 'As op��es "@value" n�o existem.'
    );

    return $options;
  }

  /**
   * @see CoreExt_Validate_Abstract#_validate($value)
   */
  protected function _validate($value)
  {
    if ($this->_hasOption('choices')) {
      $value   = $this->_getStringArray($value);
      $choices = $this->_getStringArray($this->getOption('choices'));

      if ($this->_hasOption('multiple') && FALSE == $this->getOption('multiple')) {
        if (in_array($value, $choices, TRUE)) {
          return TRUE;
        }
        throw new Exception($this->_getErrorMessage('choice_error', array('@value' => $this->getSanitizedValue())));
      }
      else {
        if (in_array($value, array($choices), TRUE)) {
          return TRUE;
        }
        throw new Exception($this->_getErrorMessage(
          'multiple_error',
          array('@value' => array_diff($value, $this->getOption('choices'))))
        );
      }
    }
    return TRUE;
  }

  /**
   * Retorna um array de strings ou um valor num�rico como string.
   * @param array|numeric $value
   * @return array|string
   */
  protected function _getStringArray($value)
  {
    if (is_array($value)) {
      $return = array();
      foreach ($value as $v) {
        $return[] = (string) $v;
      }
      return $return;
    }
    return (string) $value;
  }
}