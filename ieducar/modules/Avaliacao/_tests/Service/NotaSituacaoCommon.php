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

require_once 'Avaliacao/_tests/Service/TestCommon.php';

/**
 * Avaliacao_Service_NotaSituacaoCommon abstract class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
abstract class Avaliacao_Service_NotaSituacaoCommon extends Avaliacao_Service_TestCommon
{
  protected function _setUpNotaComponenteMediaDataMapperMock(
    Avaliacao_Model_NotaAluno $notaAluno, array $medias)
  {
    // Configura mock para notas
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');

    $mock->expects($this->any())
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id))
         ->will($this->returnValue($medias));

    $this->_setNotaComponenteMediaDataMapperMock($mock);
  }

  /**
   * Nenhuma m�dia lan�ada, �bvio que est� em andamento.
   */
  public function testSituacaoComponentesCurricularesEmAndamento()
  {
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares = array();

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma m�dia lan�ada
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, array());

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  /**
   * Um componente em exame, j� que por padr�o a regra de avalia��o define uma
   * f�rmula de recupera��o.
   */
  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotais()
  {
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares = array();

    // Matem�tica estar� em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma m�dia lan�ada
    $medias = array(
      1 => new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  /**
   * Um componente em exame, j� que por padr�o a regra de avalia��o define uma
   * f�rmula de recupera��o. Quatro m�dias lan�adas, 3 aprovadas.
   */
  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosAprovados()
  {
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares = array();

    // Matem�tica estar� em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma m�dia lan�ada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosDoisAprovadosUmAndamento()
  {
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
    $expected->componentesCurriculares = array();

    // Matem�tica estar� em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma m�dia lan�ada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 5.75,
        'mediaArredondada'     => 5,
        'etapa'                => 3
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosUmAprovadoAposExameEDoisAprovados()
  {
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares = array();

    // Matem�tica estar� em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma m�dia lan�ada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6.5,
        'mediaArredondada'     => 6,
        'etapa'                => 'Rc'
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

  public function testSituacaoComponentesCurricularesUmComponenteLancadoEmExameDeQuatroComponentesTotaisLancadosUmAprovadoAposExameUmReprovadoEOutroAprovado()
  {
    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::EM_EXAME;
    $expected->componentesCurriculares = array();

    // Matem�tica estar� em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::EM_EXAME;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO_APOS_EXAME;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::REPROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma m�dia lan�ada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6.5,
        'mediaArredondada'     => 6,
        'etapa'                => 'Rc'
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 'Rc'
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }

/**
   * Um componente reprovado, com uma regra sem recupera��o. Quatro m�dias
   * lan�adas, 3 aprovadas.
   */
  public function testSituacaoComponentesCurricularesUmComponenteLancadoReprovadoUmComponenteAbaixoDaMedia()
  {
    $this->_setRegraOption('formulaRecuperacao', NULL);

    // Expectativa
    $expected = new stdClass();
    $expected->situacao = App_Model_MatriculaSituacao::REPROVADO;
    $expected->componentesCurriculares = array();

    // Matem�tica estar� em exame
    $expected->componentesCurriculares[1] = new stdClass();
    $expected->componentesCurriculares[1]->situacao = App_Model_MatriculaSituacao::REPROVADO;

    $expected->componentesCurriculares[2] = new stdClass();
    $expected->componentesCurriculares[2]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[3] = new stdClass();
    $expected->componentesCurriculares[3]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $expected->componentesCurriculares[4] = new stdClass();
    $expected->componentesCurriculares[4]->situacao = App_Model_MatriculaSituacao::APROVADO;

    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    // Nenhuma m�dia lan�ada
    $medias = array(
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'media'                => 5,
        'mediaArredondada'     => 5,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 2,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 3,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponenteMedia(array(
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 4,
        'media'                => 6,
        'mediaArredondada'     => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para notas
    $this->_setUpNotaComponenteMediaDataMapperMock($notaAluno, $medias);

    $service = $this->_getServiceInstance();

    $this->assertEquals($expected, $service->getSituacaoComponentesCurriculares());
  }
}