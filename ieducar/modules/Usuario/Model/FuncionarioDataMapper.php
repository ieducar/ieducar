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
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'Usuario/Model/Funcionario.php';

/**
 * Usuario_Model_FuncionarioDataMapper class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Usuario
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Usuario_Model_FuncionarioDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'Usuario_Model_Funcionario';
  protected $_tableName   = 'funcionario';
  protected $_tableSchema = 'portal';

  protected $_attributeMap = array(
    #'ref_cod_pessoa_fj' => 'ref_cod_pessoa_fj',
    #'matricula'         => 'matricula',
    #'email'             => 'email',
    #'data_troca_senha'  => 'data_troca_senha'
  );

  protected $_notPersistable = array(
    #'ippes'
  );

  protected $_primaryKey = array('ref_cod_pessoa_fj');

  protected function _getFindStatment($pkey)
  {
    if (!is_array($pkey))
      $pkey = array("ref_cod_pessoa_fj" => $pkey);

    return parent::_getFindStatment($pkey);
  }
}
