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
 * @since       Arquivo dispon�vel desde a vers�o 1.2.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'ComponenteCurricular/Model/Turma.php';

/**
 * ComponenteCurricular_Model_TurmaDataMapper class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.2.0
 * @version     @@package_version@@
 */
class ComponenteCurricular_Model_TurmaDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'ComponenteCurricular_Model_Turma';
  protected $_tableName   = 'componente_curricular_turma';
  protected $_tableSchema = 'modules';

  /**
   * Os atributos anoEscolar e escola est�o presentes apenas para
   * fins de desnormaliza��o.
   * @var array
   */
  protected $_attributeMap = array(
    'componenteCurricular' => 'componente_curricular_id',
    'anoEscolar'           => 'ano_escolar_id',
    'escola'               => 'escola_id',
    'turma'                => 'turma_id',
    'cargaHoraria'         => 'carga_horaria'
  );

  protected $_primaryKey = array(
    'componenteCurricular', 'turma'
  );

  /**
   * Realiza uma opera��o de atualiza��o em todas as inst�ncias persistidas de
   * ComponenteCurricular_Model_Turma. A atualiza��o envolve criar, atualizar
   * e/ou apagar inst�ncias persistidas.
   *
   * No exemplo de c�digo a seguir, se uma inst�ncia de
   * ComponenteCurricular_Model_Turma com uma refer�ncia a componenteCurricular
   * "1" existisse, esta teria seus atributos atualizados e persistidos
   * novamente. Se a refer�ncia n�o existisse, uma nova inst�ncia de
   * ComponenteCurricular_Model_Turma seria criada e persistida. Caso uma
   * refer�ncia a "2" existisse, esta seria apagada por n�o estar referenciada
   * no array $componentes.
   *
   * <code>
   * <?php
   * $componentes = array(
   *   array('id' => 1, 'cargaHoraria' => 100)
   * );
   * $mapper->bulkUpdate(1, 1, 1, $componentes);
   * </code>
   *
   *
   *
   * @param  int   $anoEscolar  O c�digo do ano escolar/s�rie.
   * @param  int   $escola      O c�digo da escola.
   * @param  int   $turma       O c�digo da turma.
   * @param  array $componentes (id => integer, cargaHoraria => float|null)
   * @throws Exception
   */
  public function bulkUpdate($anoEscolar, $escola, $turma, array $componentes)
  {
    $update = $insert = $delete = array();

    $componentesTurma = $this->findAll(array(), array('turma'  => $turma));

    $objects = array();
    foreach ($componentesTurma as $componenteTurma) {
      $objects[$componenteTurma->get('componenteCurricular')] = $componenteTurma;
    }

    foreach ($componentes as $componente) {
      $id = $componente['id'];

      if (isset($objects[$id])) {
        $insert[$id] = $objects[$id];
        $insert[$id]->cargaHoraria = $componente['cargaHoraria'];
        continue;
      }

      $insert[$id] = new ComponenteCurricular_Model_Turma(array(
        'componenteCurricular' => $id,
        'anoEscolar'           => $anoEscolar,
        'escola'               => $escola,
        'turma'                => $turma,
        'cargaHoraria'         => $componente['cargaHoraria']
      ));
    }

    $delete = array_diff(array_keys($objects), array_keys($insert));

    foreach ($delete as $id) {
      $this->delete($objects[$id]);
    }

    foreach ($insert as $entry) {
      $this->save($entry);
    }
  }
}