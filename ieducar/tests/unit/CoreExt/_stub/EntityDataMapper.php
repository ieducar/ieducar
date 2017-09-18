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
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id: /ieducar/branches/1.1.0-dev/ieducar/tests/unit/CoreExt/_stub/Entity.php 586 2009-10-14T23:26:48.478692Z eriksen  $
 */

require_once 'CoreExt/DataMapper.php';
require_once 'CoreExt/_stub/Entity.php';

/**
 * CoreExt_EntityDataMapperStub class.
 *
 * Entidade para testes de integra��o do componente CoreExt_DataMapper com
 * o banco de dados.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_DataMapper
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_EntityDataMapperStub extends CoreExt_DataMapper
{
  protected $_entityClass = 'CoreExt_EntityStub';
  protected $_tableName   = 'pessoa';
  protected $_tableSchema = '';

  protected $_attributeMap = array(
    'estadoCivil' => 'estado_civil'
  );

  /**
   * Cria a tabela pessoa para testes de integra��o.
   *
   * SQL compat�vel com SQLite.
   *
   * @param  clsBancoPdo $db
   * @return mixed Retorna FALSE em caso de erro
   */
  public static function createTable(clsBanco $db)
  {
    $sql = "
CREATE TABLE pessoa(
  id integer primary key,
  nome character varying(100) NOT NULL,
  estado_civil character varying(20) NOT NULL DEFAULT 'solteiro',
  doador char(1) NULL DEFAULT 't'
);";

    return $db->Consulta($sql);
  }
}