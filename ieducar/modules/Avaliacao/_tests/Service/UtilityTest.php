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
 * Avaliacao_Service_UtilityTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_UtilityTest extends Avaliacao_Service_TestCommon
{
  public function testArredondaNotaLancaExcecaoSeParametroNaoForInstanciaDeAvaliacaomodelnotacomponenteOuNumerico()
  {
    $service = $this->_getServiceInstance();

    try {
      $service->arredondaNota(new Avaliacao_Model_NotaComponente());
      $this->fail('O valor "inst�ncia Avaliacao_Model_NotaComponente()" deveria '
                  . 'ter causado um exce��o pois o atributo "nota" � NULL por padr�o.');
    }
    catch (CoreExt_Exception_InvalidArgumentException $e) {
    }

    try {
      $service->arredondaNota('abc 7.5');
      $this->fail('O valor "abc 7.5" deveria ter causado um exce��o.');
    }
    catch (CoreExt_Exception_InvalidArgumentException $e) {
    }
  }

  public function testArredondaNotaNumerica()
  {
    $service = $this->_getServiceInstance();
    $this->assertEquals(5, $service->arredondaNota(5.5));
  }

  public function testArredondaNotaConceitual()
  {
    // Valores padr�o dos atributos de TabelaArredondamento_Model_TabelaValor
    $data = array(
      'tabelaArredondamento' => 1,
      'nome'                 => NULL,
      'descricao'            => NULL,
      'valorMinimo'          => -1,
      'valorMaximo'          => 0
    );

    $tabelaValores = array();

    // I
    $tabelaValores[0] = new TabelaArredondamento_Model_TabelaValor($data);
    $tabelaValores[0]->nome        = 'I';
    $tabelaValores[0]->descricao   = 'Incompleto';
    $tabelaValores[0]->valorMinimo = 0;
    $tabelaValores[0]->valorMaximo = 5.50;

    // S
    $tabelaValores[1] = new TabelaArredondamento_Model_TabelaValor($data);
    $tabelaValores[1]->nome        = 'S';
    $tabelaValores[1]->descricao   = 'Suficiente';
    $tabelaValores[1]->valorMinimo = 5.51;
    $tabelaValores[1]->valorMaximo = 8;

    // O
    $tabelaValores[2] = new TabelaArredondamento_Model_TabelaValor($data);
    $tabelaValores[2]->nome        = 'O';
    $tabelaValores[2]->descricao   = '�timo';
    $tabelaValores[2]->valorMinimo = 8.01;
    $tabelaValores[2]->valorMaximo = 10.0;

    $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');
    $mock->expects($this->any())
         ->method('findAll')
         ->will($this->returnValue($tabelaValores));

    $tabelaDataMapper = new TabelaArredondamento_Model_TabelaDataMapper();
    $tabelaDataMapper->setTabelaValorDataMapper($mock);

    $tabela = new TabelaArredondamento_Model_Tabela(array('nome' => 'Conceituais'));
    $tabela->setDataMapper($tabelaDataMapper);

    $this->_setRegraOption('tabelaArredondamento', $tabela);

    $service = $this->_getServiceInstance();
    $this->assertEquals('I', $service->arredondaNota(5.49));
    $this->assertEquals('S', $service->arredondaNota(6.50));
    $this->assertEquals('O', $service->arredondaNota(9.15));
  }

  public function testPreverNotaParaRecuperacao()
  {
    // Define as notas do aluno
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = array(
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 1
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 2
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 3
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 4,
        'etapa'                => 4
      )),
    );

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($notas));

    $this->_setNotaComponenteDataMapperMock($mock);

    $service = $this->_getServiceInstance();

    $expected = new TabelaArredondamento_Model_TabelaValor(array(
      'nome'        => 10,
      'valorMinimo' => 9,
      'valorMaximo' => 10
    ));

    $ret = $service->preverNotaRecuperacao(1);
    $this->assertEquals(array($expected->nome, $expected->valorMinimo, $expected->valorMaximo),
                        array($ret->nome, $ret->valorMinimo, $ret->valorMaximo));
  }
}