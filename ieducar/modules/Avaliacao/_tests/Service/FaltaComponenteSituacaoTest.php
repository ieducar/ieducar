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
 * Avaliacao_Service_FaltaComponenteSituacaoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaComponenteSituacaoTest extends Avaliacao_Service_FaltaSituacaoCommon
{
  protected function setUp()
  {
    $this->_setRegraOption('tipoPresenca', RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
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

  public function testSituacaoFaltasEmAndamentoUmComponenteAprovadoDeQuatroTotais()
  {
    $faltaAluno  = $this->_getConfigOption('faltaAluno', 'instance');
    $componentes = $this->_getConfigOptions('escolaSerieDisciplina');

    $faltas = array(
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 1,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 2,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 3,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 4,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 4
      )),
    );

    $this->_setUpFaltaAbstractDataMapperMock($faltaAluno, $faltas);

    $expected = $this->_getExpectedSituacaoFaltas();

    // Configura a expectativa
    $expected->situacao            = App_Model_MatriculaSituacao::EM_ANDAMENTO;

    $expected->totalFaltas         = array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade'));
    $expected->horasFaltas         = $expected->totalFaltas * $this->_getConfigOption('curso', 'hora_falta');
    $expected->porcentagemFalta    = ($expected->horasFaltas / $this->_getConfigOption('serie', 'carga_horaria') * 100);
    $expected->porcentagemPresenca = 100 - $expected->porcentagemFalta;

    // Configura expectativa para o componente de id '1'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr($faltas, 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[0]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[1]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[1]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[1]->porcentagemPresenca = $componentePorcentagemPresenca;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
  }

  public function testSituacaoFaltasAprovado()
  {
    $faltaAluno  = $this->_getConfigOption('faltaAluno', 'instance');
    $componentes = $this->_getConfigOptions('escolaSerieDisciplina');

    $faltas = array(
      // Matem�tica
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 1,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 2,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 3,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 4,
        'componenteCurricular' => 1,
        'quantidade'           => 5,
        'etapa'                => 4
      )),
      // Portugu�s
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 5,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 6,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 7,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 8,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 4
      )),
      // Ci�ncias
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 9,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 10,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 11,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 12,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 4
      )),
      // Fisica
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 13,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 14,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 15,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 16,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 4
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

    // Configura expectativa para o componente de id '1'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 0, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[0]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[1]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[1]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[1]->porcentagemPresenca = $componentePorcentagemPresenca;

    // Configura expectativa para o componente de id '2'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 4, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[1]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[2]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[2]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[2]->porcentagemPresenca = $componentePorcentagemPresenca;

    // Configura expectativa para o componente de id '3'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 8, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[2]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[3]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[3]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[3]->porcentagemPresenca = $componentePorcentagemPresenca;

    // Configura expectativa para o componente de id '4'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 12, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[3]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[4]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[4]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[4]->porcentagemPresenca = $componentePorcentagemPresenca;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
  }

  /**
   * Faltas para componentes funcionam usam os mesmos crit�rios das faltas
   * gerais para a defini��o de aprovado ou reprovado: presen�a geral.
   */
  public function testSituacaoFaltasReprovado()
  {
    $faltaAluno  = $this->_getConfigOption('faltaAluno', 'instance');
    $componentes = $this->_getConfigOptions('escolaSerieDisciplina');

    $faltas = array(
      // Matem�tica
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 1,
        'componenteCurricular' => 1,
        'quantidade'           => 60,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 2,
        'componenteCurricular' => 1,
        'quantidade'           => 60,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 3,
        'componenteCurricular' => 1,
        'quantidade'           => 60,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 4,
        'componenteCurricular' => 1,
        'quantidade'           => 55,
        'etapa'                => 4
      )),
      // Portugu�s
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 5,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 6,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 7,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 8,
        'componenteCurricular' => 2,
        'quantidade'           => 5,
        'etapa'                => 4
      )),
      // Ci�ncias
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 9,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 10,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 11,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 12,
        'componenteCurricular' => 3,
        'quantidade'           => 5,
        'etapa'                => 4
      )),
      // Fisica
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 13,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 14,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 15,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_FaltaComponente(array(
        'id'                   => 16,
        'componenteCurricular' => 4,
        'quantidade'           => 5,
        'etapa'                => 4
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

    // Configura expectativa para o componente de id '1'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 0, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[0]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao            = App_Model_MatriculaSituacao::REPROVADO;
    $expected->componentesCurriculares[1]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[1]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[1]->porcentagemPresenca = $componentePorcentagemPresenca;

    // Configura expectativa para o componente de id '2'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 4, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[1]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[2]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[2]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[2]->porcentagemPresenca = $componentePorcentagemPresenca;

    // Configura expectativa para o componente de id '3'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 8, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[2]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[3]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[3]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[3]->porcentagemPresenca = $componentePorcentagemPresenca;

    // Configura expectativa para o componente de id '4'
    $componenteHoraFalta =
      array_sum(CoreExt_Entity::entityFilterAttr(array_slice($faltas, 12, 4), 'id', 'quantidade')) *
      $this->_getConfigOption('curso', 'hora_falta');

    $componentePorcentagemFalta =
      ($componenteHoraFalta / $componentes[3]['carga_horaria']) * 100;

    $componentePorcentagemPresenca = 100 - $componentePorcentagemFalta;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao            = App_Model_MatriculaSituacao::APROVADO;
    $expected->componentesCurriculares[4]->horasFaltas         = $componenteHoraFalta;
    $expected->componentesCurriculares[4]->porcentagemFalta    = $componentePorcentagemFalta;
    $expected->componentesCurriculares[4]->porcentagemPresenca = $componentePorcentagemPresenca;

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoFaltas());
  }
}