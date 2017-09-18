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

require_once 'Educacenso/Model/Aluno.php';
require_once 'Educacenso/Model/CodigoReferenciaDataMapper.php';

/**
 * Educacenso_Model_AlunoDataMapper class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Educacenso
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.2.0
 * @version     @@package_version@@
 */
class Educacenso_Model_AlunoDataMapper extends Educacenso_Model_CodigoReferenciaDataMapper
{
  protected $_entityClass = 'Educacenso_Model_Aluno';
  protected $_tableName   = 'educacenso_cod_aluno';

  protected $_attributeMap = array(
    'aluno'      => 'cod_aluno',
    'alunoInep'  => 'cod_aluno_inep',
    'nomeInep'   => 'nome_inep',
    'fonte'      => 'fonte',
    'created_at' => 'created_at',
    'updated_at' => 'updated_at'
  );

  // aparentemente o campo alunoInep n�o deveria fazer parte da chave primaria, pois este pode
  // ser alterado no cadastro de aluno, #TODO criar migracao para remover PK de tal campo ?
  protected $_primaryKey = array(
    'aluno' #, 'alunoInep'
  );

  // fixup para find funcionar em tabelas cujo PK n�o se chama id
  protected function _getFindStatment($pkey)
  {
    if (! is_array($pkey))
      $pkey = array('cod_aluno' => $pkey);

    return parent::_getFindStatment($pkey);
  }
}