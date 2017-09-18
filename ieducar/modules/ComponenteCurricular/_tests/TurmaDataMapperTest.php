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
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.2.0
 * @version     $Id$
 */

require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';

/**
 * TurmDataMapperTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.2.0
 * @version     @@package_version@@
 */
class TurmaDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  public function testBulkUpdate()
  {
    $returnValue = array(
      array(
        'componente_curricular_id' => 1,
        'ano_escolar_id'           => 1,
        'escola_id'                => 1,
        'turma_id'                 => 1,
        'carga_horaria'            => NULL
      ),
      array(
        'componente_curricular_id' => 3,
        'ano_escolar_id'           => 1,
        'escola_id'                => 1,
        'turma_id'                 => 1,
        'carga_horaria'            => 100
      )
    );

    $componentes = array(
      array(
        'id' => 1,
        'cargaHoraria' => 100
      ),
      array(
        'id' => 2,
        'cargaHoraria' => NULL
      )
    );

    $mock = $this->getDbMock();

    // 1 SELECT, 1 DELETE, 1 INSERT e 1 UPDATE
    $mock->expects($this->exactly(4))
         ->method('Consulta');

    $mock->expects($this->exactly(3))
         ->method('ProximoRegistro')
         ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $mock->expects($this->exactly(2))
         ->method('Tupla')
         ->will($this->onConsecutiveCalls($returnValue[0], $returnValue[1]));

    $mapper = new ComponenteCurricular_Model_TurmaDataMapper($mock);
    $mapper->bulkUpdate(1, 1, 1, $componentes);
  }
}