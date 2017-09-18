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
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Calendario
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';

/**
 * Calendario_Model_Turma class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Calendario
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.2.0
 * @version     @@package_version@@
 */
class Calendario_Model_Turma extends CoreExt_Entity
{
  protected $_data = array(
    'calendarioAnoLetivo' => NULL,
    'ano'                 => NULL,
    'mes'                 => NULL,
    'dia'                 => NULL,
    'turma'               => NULL
  );

  public function getDefaultValidatorCollection()
  {
    return array(
      'calendarioAnoLetivo' => new CoreExt_Validate_Numeric(array('min' => 0)),
      'ano'                 => new CoreExt_Validate_Numeric(array('min' => 0)),
      'mes'                 => new CoreExt_Validate_Numeric(array('min' => 0)),
      'dia'                 => new CoreExt_Validate_Numeric(array('min' => 0)),
      'turma'               => new CoreExt_Validate_Numeric(array('min' => 0))
    );
  }

  public function __construct(array $options = array())
  {
    parent::__construct($options);
    unset($this->_data['id']);
  }
}