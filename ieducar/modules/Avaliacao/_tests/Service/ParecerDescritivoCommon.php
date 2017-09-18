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
 * Avaliacao_Service_ParecerDescritivoCommon abstract class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
abstract class Avaliacao_Service_ParecerDescritivoCommon extends Avaliacao_Service_TestCommon
{
  /**
   * Retorna as etapas poss�veis para a inst�ncia do parecer.
   * @return array
   */
  protected function _getEtapasPossiveisParecer()
  {
    $parecerDescritivo = $this->_getRegraOption('parecerDescritivo');

    $anuais = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE
    );

    $etapas = array(
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
      RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE
    );

    if (in_array($parecerDescritivo, $anuais)) {
      return array('An');
    }

    return $this->_getEtapasPossiveis();
  }

  /**
   * Retorna o nome da classe CoreExt_DataMapper correta de acordo com a
   * configura��o da regra. M�todo auxiliar para cria��o de mocks.
   *
   * @return string
   */
  protected function _getParecerDescritivoDataMapper()
  {
    $parecerDescritivo = $this->_getRegraOption('parecerDescritivo');

    switch($parecerDescritivo) {
      case RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL:
      case RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL:
        $mapper = 'Avaliacao_Model_ParecerDescritivoGeralDataMapper';
        break;
      case RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE:
      case RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE:
        $mapper = 'Avaliacao_Model_ParecerDescritivoComponenteDataMapper';
        break;
    }

    return $mapper;
  }

  /**
   * @return Avaliacao_Model_ParecerDescritivoAbstract
   * @see Avaliacao_Service_ParecerDescritivoCommon#testInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
   */
  protected abstract function _getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim();

  /**
   * @return Avaliacao_Model_ParecerDescritivoAbstract
   * @see Avaliacao_Service_ParecerDescritivoCommon#testAdicionaParecerNoBoletim()
   */
  protected abstract function _getTestAdicionaParecerNoBoletim();

  /**
   * @param Avaliacao_Model_ParecerDescritivoAbstract $parecer
   * @see Avaliacao_Service_ParecerDescritivoCommon#testAdicionaParecerNoBoletim()
   */
  protected abstract function _testAdicionaParecerNoBoletimVerificaValidadores(Avaliacao_Model_ParecerDescritivoAbstract $parecer);

  /**
   * @return array
   * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvarPareceresNoBoletim()
   */
  protected abstract function _getTestSalvarPareceresNoBoletimInstanciasDePareceres();

  /**
   * @return array
   * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvarPareceresNoBoletimComEtapasJaLancadas()
   */
  protected abstract function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias();

  /**
   * @return array
   * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvarPareceresNoBoletimComEtapasJaLancadas()
   */
  protected abstract function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas();

  /**
   * @return array
   * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
   */
  protected abstract function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias();

  /**
   * @return array
   * @see Avaliacao_Service_ParecerDescritivoCommon#testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
   */
  protected abstract function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas();

  /**
   * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
   */
  public function testInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
  {
    $service = $this->_getServiceInstance();

    $parecer = $this->_getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim();

    $service->addParecer($parecer)
            ->addParecer($parecer);

    $this->assertEquals(1, count($service->getPareceres()));

    $parecer = clone $parecer;
    $service->addPareceres(array($parecer, $parecer, $parecer));

    $this->assertEquals(2, count($service->getPareceres()));
  }

  /**
   * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestAdicionaParecerNoBoletim()
   * @see Avaliacao_Service_ParecerDescritivoCommon#_testAdicionaParecerNoBoletimVerificaValidadores()
   */
  public function testAdicionaParecerNoBoletim()
  {
    $service = $this->_getServiceInstance();

    $parecer = $this->_getTestAdicionaParecerNoBoletim();

    $parecerOriginal = clone $parecer;
    $service->addParecer($parecer);

    $pareceres = $service->getPareceres();
    $serviceParecer = array_shift($pareceres);

    $this->_testAdicionaParecerNoBoletimVerificaValidadores($serviceParecer);
  }

  /**
   * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvarPareceresNoBoletimInstanciasDePareceres()
   */
  public function testSalvarPareceresNoBoletim()
  {
    $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

    $pareceres = $this->_getTestSalvarPareceresNoBoletimInstanciasDePareceres();

    // Configura mock para Avaliacao_Model_Parecer
    $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('parecerDescritivoAluno' => $parecerAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue(array()));

    foreach ($pareceres as $i => $parecer) {
      $mock->expects($this->at($i + 1))
           ->method('save')
           ->with($parecer)
           ->will($this->returnValue(TRUE));
    }

    $this->_setParecerDescritivoAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addPareceres($pareceres);
    $service->savePareceres();
  }

  /**
   * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias()
   * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()
   */
  public function testSalvarPareceresNoBoletimComEtapasJaLancadas()
  {
    $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

    $pareceres = $this->_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias();

    // Configura mock para Avaliacao_Model_Parecer
    $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('parecerDescritivoAluno' => $parecerAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($this->_getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()));

    foreach ($pareceres as $i => $parecer) {
      $mock->expects($this->at($i + 1))
           ->method('save')
           ->with($parecer)
           ->will($this->returnValue(TRUE));
    }

    $this->_setParecerDescritivoAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addPareceres($pareceres);
    $service->savePareceres();
  }

  /**
   * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias()
   * @see Avaliacao_Service_ParecerDescritivoCommon#_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()
   */
  public function testSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
  {
    $parecerAluno = $this->_getConfigOption('parecerDescritivoAluno', 'instance');

    $pareceres = $this->_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias();

    // Configura mock para Avaliacao_Model_Parecer
    $mock = $this->getCleanMock($this->_getParecerDescritivoDataMapper());

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('parecerDescritivoAluno' => $parecerAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($this->_getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()));

    foreach ($pareceres as $i => $parecer) {
      $mock->expects($this->at($i + 1))
           ->method('save')
           ->with($parecer)
           ->will($this->returnValue(TRUE));
    }

    $this->_setParecerDescritivoAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addPareceres($pareceres);
    $service->savePareceres();
  }
}