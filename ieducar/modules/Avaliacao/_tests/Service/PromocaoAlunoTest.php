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
 * Avaliacao_Service_PromocaoAlunoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_PromocaoAlunoTest extends Avaliacao_Service_TestCommon
{
  protected function _setUpRegraAvaliacaoMock($tipoProgressao)
  {
    $mock = $this->getCleanMock('RegraAvaliacao_Model_Regra');
    $mock->expects($this->at(0))
         ->method('get')
         ->with('tipoProgressao')
         ->will($this->returnValue($tipoProgressao));

    return $mock;
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testPromoverAlunoLancaExcecaoCasoSituacaoEstejaEmAndamento()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE;
    $situacao->andamento   = TRUE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->promover();
  }

  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testPromoverAlunoLancaExcecaoCasoMatriculaDoAlunoJaEstejaAprovadaOuReprovada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::APROVADO));

    $service->promover();
  }

  public function testPromoverAlunoAutomaticamenteProgressaoContinuada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::CONTINUADA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testPromoverAlunoAutomaticamenteProgressaoNaoContinuadaAutoMediaPresenca()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_MEDIA_PRESENCA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testReprovarAlunoAutomaticamenteProgressaoNaoContinuadaAutoMediaPresenca()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = TRUE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_MEDIA_PRESENCA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, FALSE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testPromoverAlunoAutomaticamenteProgressaoNaoContinuadaAutoMedia()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = TRUE;  // N�o considera reten��o por falta

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_AUTO_SOMENTE_MEDIA);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover());
  }

  public function testPromoverAlunoManualmenteProgressaoNaoContinuadaLancaExcecaoSeNaoConfirmada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE; // Reprovado por nota
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    try {
      $service->promover();
      $this->fail('Invocar o m�todo "->promover()" sem uma confirma��o booleana '
                  . 'expl�cita (TRUE ou FALSE) em uma progress�o "NAO_CONTINUADA_MANUAL" '
                  . 'causa exce��o.');
    }
    catch (CoreExt_Service_Exception $e)
    {
    }
  }

  public function testPromoverAlunoManualmenteProgressaoNaoContinuada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE; // Reprovado por nota
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, TRUE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover(TRUE));
  }

  public function testReprovarAlunoManualmenteProgressaoNaoContinuada()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = FALSE; // Reprovado por nota
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::NAO_CONTINUADA_MANUAL);

    $service = $this->setExcludedMethods(array('promover'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    $service->expects($this->at(5))
            ->method('_updateMatricula')
            ->with($codMatricula, $codUsuario, FALSE)
            ->will($this->returnValue(TRUE));

    $this->assertTrue($service->promover(FALSE));
  }

  public function testSaveBoletim()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    $service = $this->setExcludedMethods(array('save'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('saveNotas')
            ->will($this->returnValue($service));

    $service->expects($this->at(1))
            ->method('saveFaltas')
            ->will($this->returnValue($service));

    $service->expects($this->at(2))
            ->method('savePareceres')
            ->will($this->returnValue($service));

    $service->expects($this->at(3))
            ->method('promover')
            ->will($this->returnValue(TRUE));

    try {
      $service->save();
    }
    catch (Exception $e) {
      $this->fail('O m�todo "->save()" n�o deveria ter lan�ado exce��o com o '
                  . 'cen�rio de teste configurado.');
    }
  }

  public function testIntegracaoMatriculaPromoverAluno()
  {
    $situacao = new stdClass();
    $situacao->aprovado    = TRUE;
    $situacao->andamento   = FALSE;
    $situacao->recuperacao = FALSE;
    $situacao->retidoFalta = FALSE;

    $codMatricula = $this->_getConfigOption('matricula', 'cod_matricula');
    $codUsuario   = $this->_getConfigOption('usuario', 'cod_usuario');

    // Mock para RegraAvaliacao_Model_Regra
    $regra = $this->_setUpRegraAvaliacaoMock(RegraAvaliacao_Model_TipoProgressao::CONTINUADA);

    $service = $this->setExcludedMethods(array('promover', '_updateMatricula'))
                    ->getCleanMock('Avaliacao_Service_Boletim');

    $service->expects($this->at(0))
            ->method('getSituacaoAluno')
            ->will($this->returnValue($situacao));

    $service->expects($this->at(1))
            ->method('getOption')
            ->with('aprovado')
            ->will($this->returnValue(App_Model_MatriculaSituacao::EM_ANDAMENTO));

    $service->expects($this->at(2))
            ->method('getRegra')
            ->will($this->returnValue($regra));

    $service->expects($this->at(3))
            ->method('getOption')
            ->with('matricula')
            ->will($this->returnValue($codMatricula));

    $service->expects($this->at(4))
            ->method('getOption')
            ->with('usuario')
            ->will($this->returnValue($codUsuario));

    // Configura mock de inst�ncia de classe legada
    $matricula = $this->getCleanMock('clsPmieducarMatricula');

    $matricula->expects($this->at(0))
              ->method('edita')
              ->will($this->returnValue(TRUE));

    CoreExt_Entity::addClassToStorage('clsPmieducarMatricula', $matricula,
      'include/pmieducar/clsPmieducarMatricula.inc.php', TRUE);

    $this->assertTrue($service->promover());
  }
}