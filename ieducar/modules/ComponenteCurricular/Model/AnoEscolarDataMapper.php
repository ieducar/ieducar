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
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'ComponenteCurricular/Model/AnoEscolar.php';

/**
 * ComponenteCurricular_Model_AnoEscolarDataMapper class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_AnoEscolarDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'ComponenteCurricular_Model_AnoEscolar';
  protected $_tableName   = 'componente_curricular_ano_escolar';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'componenteCurricular' => 'componente_curricular_id',
    'anoEscolar'           => 'ano_escolar_id',
    'cargaHoraria'         => 'carga_horaria'
  );

  protected $_primaryKey = array(
    'componenteCurricular', 'anoEscolar'
  );

  /**
   * @var ComponenteCurricular_Model_ComponenteDataMapper
   */
  protected $_componenteDataMapper = NULL;

  /**
   * Setter.
   * @param ComponenteCurricular_Model_ComponenteDataMapper $mapper
   * @return CoreExt_DataMapper Prov� interface flu�da
   */
  public function setComponenteDataMapper(ComponenteCurricular_Model_ComponenteDataMapper $mapper)
  {
    $this->_componenteDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return ComponenteCurricular_Model_ComponenteDataMapper
   */
  public function getComponenteDataMapper()
  {
    if (is_null($this->_componenteDataMapper)) {
      require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
      $this->_componenteDataMapper = new ComponenteCurricular_Model_ComponenteDataMapper();
    }
    return $this->_componenteDataMapper;
  }

  /**
   * Finder para componentes por curso.
   *
   * @param int $cursoId
   * @return array
   */
  public function findComponentePorCurso($cursoId)
  {
    $sql = "
SELECT
  DISTINCT(mca.%s)
FROM
  %s mca, pmieducar.serie ps
WHERE
  mca.%s = ps.cod_serie AND ps.ref_cod_curso = '%d'";

    $sql = sprintf($sql, $this->_getTableColumn('componenteCurricular'),
      $this->_getTableName(), $this->_getTableColumn('anoEscolar'), $cursoId);

    $this->getDbAdapter()->Consulta($sql);

    $list = array();
    while ($this->_getDbAdapter()->ProximoRegistro()) {
      $row = $this->_getDbAdapter()->Tupla();
      $list[] = $this->getComponenteDataMapper()->find(
        $row[$this->_getTableColumn('componenteCurricular')]
      );
    }

    return $list;
  }

  /**
   * Finder para componentes por s�rie (ano escolar).
   *
   * @param int $serieId
   * @return array
   */
  public function findComponentePorSerie($serieId)
  {
    $componentesAnoEscolar = $this->findAll(array(), array('anoEscolar' => $serieId));
    $list = array();

    foreach ($componentesAnoEscolar as $key => $componenteAnoEscolar) {
      $id = $componenteAnoEscolar->get('componenteCurricular');
      $list[$id] = $this->getComponenteDataMapper()->find(
        $componenteAnoEscolar->get('componenteCurricular')
      );
      $list[$id]->cargaHoraria = $componenteAnoEscolar->cargaHoraria;
    }

    ksort($list);
    return $list;
  }
}