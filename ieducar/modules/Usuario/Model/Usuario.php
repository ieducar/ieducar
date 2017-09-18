<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'App/Model/IedFinder.php';
require_once 'CoreExt/Validate/Email.php';

/**
 * ComponenteCurricular_Model_Componente class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Usuario_Model_Usuario extends CoreExt_Entity
{
  protected $_data = array(
    'id'               => NULL,
    'escolaId'         => NULL,
    'instituicaoId'    => NULL,
    'funcionarioCadId' => NULL,
    'funcionarioExcId' => NULL,
    'tipoUsuarioId'    => NULL,
    'dataCadastro'     => NULL,
    'dataExclusao'     => NULL,
    'ativo'            => NULL
  );

  /*protected $_dataTypes = array(
  );

  protected $_references = array(
  );*/

  public function getDataMapper()
  {
    if (is_null($this->_dataMapper)) {
      require_once 'Usuario/Model/UsuarioDataMapper.php';
      $this->setDataMapper(new Usuario_Model_UsuarioDataMapper());
    }
    return parent::getDataMapper();
  }

  public function getDefaultValidatorCollection()
  {
    return array();
  }

  // TODO remover metodo? j� que foi usado $_attributeMap id
  protected function _createIdentityField()
  {
    $id = array('id' => NULL);
    $this->_data = array_merge($id, $this->_data);
    return $this;
  }
}
