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
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';

/**
 * Avaliacao_Model_NotaComponenteMedia class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Model_NotaComponenteMedia extends CoreExt_Entity
{
  protected $_data = array(
    'notaAluno'            => NULL,
    'componenteCurricular' => NULL,
    'media'                => NULL,
    'mediaArredondada'     => NULL,
    'etapa'                => NULL
  );

  protected $_dataTypes = array(
    'media' => 'numeric'
  );

  protected $_references = array(
    'notaAluno' => array(
      'value' => NULL,
      'class' => 'Avaliacao_Model_NotaAlunoDataMapper',
      'file'  => 'Avaliacao/Model/NotaAlunoDataMapper.php'
    ),
    'componenteCurricular' => array(
      'value' => NULL,
      'class' => 'ComponenteCurricular_Model_ComponenteDataMapper',
      'file'  => 'ComponenteCurricular/Model/ComponenteDataMapper.php'
    )
  );

  public function __construct($options = array())
  {
    parent::__construct($options);
    unset($this->_data['id']);
  }

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    return array(
      'media' => new CoreExt_Validate_Numeric(array('min' => 0, 'max' => 10)),
      'mediaArredondada' => new CoreExt_Validate_String(array('max' => 5)),
      'etapa' => new CoreExt_Validate_String(array('max' => 2))
    );
  }
}