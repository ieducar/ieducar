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

require_once 'Avaliacao/_tests/Service/FaltaCommon.php';

/**
 * Avaliacao_Service_FaltaGeralTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaGeralTest extends Avaliacao_Service_FaltaCommon
{
  protected function setUp()
  {
    $this->_setRegraOption('tipoPresenca', RegraAvaliacao_Model_TipoPresenca::GERAL);
    parent::setUp();
  }

  protected function _getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
  {
    return new Avaliacao_Model_FaltaGeral(array(
      'quantidade' => 10
    ));
  }

  protected function _getFaltaTestAdicionaFaltaNoBoletim()
  {
    return new Avaliacao_Model_FaltaComponente(array(
      'quantidade'           => 10
    ));
  }

  protected function _testAdicionaFaltaNoBoletimVerificaValidadores(Avaliacao_Model_FaltaAbstract $falta)
  {
    $this->assertEquals(1, $falta->etapa);
    $this->assertEquals(10, $falta->quantidade);

    $validators = $falta->getValidatorCollection();
    $this->assertType('CoreExt_Validate_Choice', $validators['etapa']);
    $this->assertFalse(isset($validators['componenteCurricular']));

    // Op��es dos validadores

    // Etapas poss�veis para o lan�amento de nota
    $this->assertEquals(
      array_merge(range(1, count($this->_getConfigOptions('anoLetivoModulo'))), array('Rc')),
      $validators['etapa']->getOption('choices')
    );
  }

  /**
   * Testa o service adicionando faltas de apenas um componente curricular,
   * para todas as etapas regulares (1 a 4).
   */
  public function testSalvarFaltasNoBoletim()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 7,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 11,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 8,
        'etapa'      => 3
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 8,
        'etapa'      => 4
      )),
    );

    // Configura mock para Avaliacao_Model_FaltaGeralDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($faltas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($faltas[1])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(3))
         ->method('save')
         ->with($faltas[2])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(4))
         ->method('save')
         ->with($faltas[3])
         ->will($this->returnValue(TRUE));

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();
  }

  /**
   * Testa o service adicionando novas faltas para um componente curricular,
   * que inclusive j� tem a falta lan�ada para a segunda etapa.
   */
  public function testSalvasFaltasNoBoletimComEtapasLancadas()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 7,
        'etapa'      => 2
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 9,
        'etapa'      => 3
      ))
    );

    $faltasPersistidas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 1,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 8,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 11,
        'etapa'      => 2
      ))
    );

    // Configura mock para Avaliacao_Model_FaltaGeralDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($faltasPersistidas));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($faltas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($faltas[1])
         ->will($this->returnValue(TRUE));

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();
  }

  public function testSalvasFaltasAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadas()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 7,
        'etapa'      => 2
      )),
      // Etapa omitida, ser� atribu�da a etapa '3'
      new Avaliacao_Model_FaltaGeral(array(
        'quantidade' => 9
      ))
    );

    $faltasPersistidas = array(
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 1,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 8,
        'etapa'      => 1
      )),
      new Avaliacao_Model_FaltaGeral(array(
        'id'         => 2,
        'faltaAluno' => $faltaAluno->id,
        'quantidade' => 11,
        'etapa'      => 2
      ))
    );

    // Configura mock para Avaliacao_Model_FaltaGeralDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaGeralDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($faltasPersistidas));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($faltas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($faltas[1])
         ->will($this->returnValue(TRUE));

    $this->_setFaltaAbstractDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addFaltas($faltas);
    $service->saveFaltas();

    $faltas = $service->getFaltas();

    $falta = array_shift($faltas);
    $this->assertEquals(2, $falta->etapa);

    // Etapa atribu�da automaticamente
    $falta = array_shift($faltas);
    $this->assertEquals(3, $falta->etapa);
  }
}