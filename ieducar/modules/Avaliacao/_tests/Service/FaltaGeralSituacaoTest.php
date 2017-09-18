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
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Avaliacao/_tests/Service/FaltaSituacaoCommon.php';

/**
 * Avaliacao_Service_FaltaGeralSituacaoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaGeralSituacaoTest extends Avaliacao_Service_FaltaSituacaoCommon
{
  protected function setUp()
  {
    $this->_setRegraOption('tipoPresenca', RegraAvaliacao_Model_TipoPresenca::GERAL);
    parent::setUp();
  }

  public function testSituacaoFaltasEmAndamento()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');
    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, array());

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao            = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->porcentagemPresenca = 100;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
  }

  public function testSituacaoFaltasAprovado()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 1,
        'quantidade' => 5,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'quantidade' => 5,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 3,
        'quantidade' => 5,
        'etapa'      => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 4,
        'quantidade' => 5,
        'etapa'      => 4
      )),
    );

    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao            = App_Model_MatriculaSituacao::APROVADO;

    $expected->totalFaltas         = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
    $expected->horasFaltas         = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
    $expected->porcentagemFalta    = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
    $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
  }

  public function testSituacaoFaltasReprovado()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 1,
        'quantidade' => 180,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'quantidade' => 180,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 3,
        'quantidade' => 180,
        'etapa'      => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 4,
        'quantidade' => 180,
        'etapa'      => 4
      )),
    );

    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao            = App_Model_MatriculaSituacao::REPROVADO;

    $expected->totalFaltas         = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
    $expected->horasFaltas         = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
    $expected->porcentagemFalta    = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
    $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
  }
}