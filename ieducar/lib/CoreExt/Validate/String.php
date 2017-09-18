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
 * CoreExt_Validate_String class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Validate_String extends CoreExt_Validate_Abstract
{
  /**
   * @see CoreExt_Validate_Abstract#_getDefaultOptions()
   */
  protected function _getDefaultOptions()
  {
    return array(
      'min' => NULL,
      'max' => NULL,
      'min_error' => '"@value" � muito curto (@min caracteres no m�nimo)',
      'max_error' => '"@value" � muito longo (@max caracteres no m�ximo)',
    );
  }

  /**
   * @see CoreExt_Validate_Abstract#_validate($value)
   */
  protected function _validate($value)
  {
    $length = strlen($value);

    if ($this->_hasOption('min') && $length < $this->getOption('min')) {
      throw new Exception($this->_getErrorMessage('min_error',
        array('@value' => $this->getSanitizedValue(), '@min' => $this->getOption('min')))
      );
    }

    if ($this->_hasOption('max') && $length > $this->getOption('max')) {
      throw new Exception($this->_getErrorMessage('max_error',
        array('@value' => $this->getSanitizedValue(), '@max' => $this->getOption('max')))
      );
    }

    return TRUE;
  }

  /**
   * @see CoreExt_Validate_Abstract#_sanitize($value)
   */
  protected function _sanitize($value)
  {
    return (string) parent::_sanitize($value);
  }
}