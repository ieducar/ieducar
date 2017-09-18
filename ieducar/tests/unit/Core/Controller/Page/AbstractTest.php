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
 * @package     Core_Controller
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/_stub/Page/Abstract.php';
require_once 'CoreExt/_stub/EntityDataMapper.php';

/**
 * Core_Controller_Page_AbstractTest class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class Core_Controller_Page_AbstractTest extends UnitBaseTest
{
  protected $_pageController = NULL;

  protected function setUp()
  {
    $this->_pageController = new Core_Controller_Page_AbstractStub();
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_pageController->setOptions(array('foo' => 'bar'));
  }

  public function testClasseDataMapperEGeradaAPartirDaDefinicaoString()
  {
    $this->_pageController->_dataMapper = 'CoreExt_EntityDataMapperStub';
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asser��o a partir da instancia��o de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(array('datamapper' => 'CoreExt_EntityDataMapperStub'));
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asser��o a partir de configura��o via "setOptions()".');

    $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asser��o a partir de configura��o via "setDataMapper()" com nome de classe "CoreExt_DataMapper".');

    $this->_pageController->setDataMapper(new CoreExt_EntityDataMapperStub());
    $this->assertType('CoreExt_DataMapper', $this->_pageController->getDataMapper(), 'Falhou na asser��o a partir de configura��o via "setDataMapper()" com objeto "CoreExt_DataMapper".');
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testClasseDataMapperNaoExistenteLancaExcecao()
  {
    $this->_pageController->setDataMapper('FooDataMapper');
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testMetodoLancaExcecaoQuandoNaoRecebeTipoSuportado()
  {
    $this->_pageController->setDataMapper(0);
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testClasseDataMapperNaoInformadaEMetodoNaoSubclassificadoLancaExcecao()
  {
    $this->_pageController->getDataMapper();
  }

  public function testAtribuicaoDeInstanciaEntity()
  {
    $this->_pageController->setEntity(new CoreExt_EntityStub());
    $this->assertType('CoreExt_Entity', $this->_pageController->getEntity());
  }

  /**
   * Ao usar o typehinting do PHP, � verificado se o par�metro � do tipo
   * correto. Se n�o for, um fatal error � lan�ado. O PHPUnit converte esse
   * erro em Exception para tornar o teste mais f�cil.
   *
   * @expectedException PHPUnit_Framework_Error
   */
  public function testAtribuicaoDeInstanciaEntityLancaExcecaoParaTipoNaoSuportado()
  {
    $this->_pageController->setEntity(NULL);
  }

  public function testInstanciaUmEntityCasoNenhumaInstanciaTenhaSidoAtribuidaExplicitamente()
  {
    $this->_pageController->setDataMapper('CoreExt_EntityDataMapperStub');
    $this->assertType('CoreExt_Entity', $this->_pageController->getEntity());
  }

  public function testNumeroDoProcessoConfigurado()
  {
    $this->_pageController->_processoAp = 1;
    $this->assertType('int', $this->_pageController->getBaseProcessoAp(), 'Falhou na asser��o por tipo a partir da instancia��o de "Core_Page_Controller_Abstract".');
    $this->assertEquals(1, $this->_pageController->getBaseProcessoAp(), 'Falhou na asser��o por valor a partir da instancia��o de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(array('processoAp' => 2));
    $this->assertEquals(2, $this->_pageController->getBaseProcessoAp(), 'Falhou na asser��o a partir de configura��o via "setOptions()".');

    $this->_pageController->setBaseProcessoAp(3);
    $this->assertEquals(3, $this->_pageController->getBaseProcessoAp(), 'Falhou na asser��o a partir de configura��o via "setBaseProcessoAp()".');
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testNumeroDoProcessoNaoInformadoEMetodoNaoSubclassificadoLancaExcecao()
  {
    $this->_pageController->getBaseProcessoAp();
  }

  public function testTituloConfigurado()
  {
    $this->_pageController->_titulo = 'foo';
    $this->assertType('string', $this->_pageController->getBaseTitulo(), 'Falhou na asser��o por tipo a partir da instancia��o de "Core_Page_Controller_Abstract".');
    $this->assertEquals('foo', $this->_pageController->getBaseTitulo(), 'Falhou na asser��o por valor a partir da instancia��o de "Core_Page_Controller_Abstract".');

    $this->_pageController->setOptions(array('titulo' => 'bar'));
    $this->assertEquals('bar', $this->_pageController->getBaseTitulo(), 'Falhou na asser��o a partir de configura��o via "setOptions()".');

    $this->_pageController->setBaseTitulo('zoo');
    $this->assertEquals('zoo', $this->_pageController->getBaseTitulo(), 'Falhou na asser��o a partir de configura��o via "setBaseTitulo()".');
  }

  /**
   * @expectedException Core_Controller_Page_Exception
   */
  public function testTituloNaoInformadoEMetodoNaoSubclassificadoLancaExcecao()
  {
    $this->_pageController->getBaseTitulo();
  }

  public function testAppendOutput()
  {
    $this->_pageController->appendOutput('string 1')
                          ->appendOutput('string 2');

    $this->assertEquals(
      'string 1' . PHP_EOL . 'string 2',
      $this->_pageController->getAppendedOutput(),
      '->getAppendedOutput() retorna o conte�do a ser adicionado como uma string separada por quebra de linha'
    );
  }

  public function testGetApendedOutputRetornaNullQuandoNaoExisteConteudoASerAdicionado()
  {
    $this->assertNull($this->_pageController->getAppendedOutput());
  }

  public function testPrependOutput()
  {
    $this->_pageController->prependOutput('string 1')
                          ->prependOutput('string 2');

    $this->assertEquals(
      'string 1' . PHP_EOL . 'string 2',
      $this->_pageController->getPrependedOutput(),
      '->getPrependedOutput() retorna o conte�do a ser adicionado como uma string separada por quebra de linha'
    );
  }

  public function testGetPrependedOutputRetornaNullQuandoNaoExisteConteudoASerAdicionado()
  {
    $this->assertNull($this->_pageController->getPrependedOutput());
  }
}
