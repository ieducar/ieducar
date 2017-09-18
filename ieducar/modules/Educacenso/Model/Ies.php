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
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';

/**
 * Educacenso_Model_Ies class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_Ies extends CoreExt_Entity
{
  protected $_data = array(
    'ies'                       => NULL,
    'nome'                      => NULL,
    'dependenciaAdministrativa' => NULL,
    'tipoInstituicao'           => NULL,
    'uf'                        => NULL,
    'user'                      => NULL,
    'created_at'                => NULL,
    'updated_at'                => NULL
  );

  public function getDefaultValidatorCollection()
  {
    return array(
      'ies'                       => new CoreExt_Validate_Numeric(array('min' => 0)),
      'nome'                      => new CoreExt_Validate_String(array('min' => 1)),
      'dependenciaAdministrativa' => new CoreExt_Validate_Numeric(array('min' => 0)),
      'tipoInstituicao'           => new CoreExt_Validate_Numeric(array('min' => 0)),
      'uf'                        => new CoreExt_Validate_String(array('required' => FALSE)),
      'user'                      => new CoreExt_Validate_Numeric(array('min' => 0))
    );
  }

  public function __toString()
  {
    return $this->nome;
  }
}