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
 * @package     RegraAvaliacao
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';

/**
 * RegraDataMapperTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class RegraDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new RegraAvaliacao_Model_RegraDataMapper();
  }

  public function testGetterDeFormulaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('FormulaMedia_Model_FormulaDataMapper', $this->_mapper->getFormulaDataMapper());
  }

  public function testGetterDeTabelaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('TabelaArredondamento_Model_TabelaDataMapper', $this->_mapper->getTabelaDataMapper());
  }

  public function testFindFormulaMediaFinalDataMapper()
  {
    // Valores de retorno
    $returnValue = array(new FormulaMedia_Model_Formula(
      array(
        'id' => 1,
        'nome' => '1� ao 3� ano',
        'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_FINAL
      )
    ));

    // Mock para �rea de conhecimento
    $mock = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padr�o pelo mock
    $this->_mapper->setFormulaDataMapper($mock);
    $formulas = $this->_mapper->findFormulaMediaFinal();

    $this->assertEquals($returnValue, $formulas);
  }

  public function testFindFormulaMediaRecuperacaoDataMapper()
  {
    // Valores de retorno
    $returnValue = array(new FormulaMedia_Model_Formula(
      array(
        'id' => 1,
        'nome' => '1� ao 3� ano',
        'tipoFormula' => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO
      )
    ));

    // Mock para �rea de conhecimento
    $mock = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padr�o pelo mock
    $this->_mapper->setFormulaDataMapper($mock);
    $formulas = $this->_mapper->findFormulaMediaRecuperacao();

    $this->assertEquals($returnValue, $formulas);
  }

  public function testFindTabelaArredondamento()
  {
    // Inst�ncia de RegraAvaliacao_Model_Regra
    $instance = new RegraAvaliacao_Model_Regra(array('instituicao' => 1));

    // Valores de retorno
    $returnValue = array(new TabelaArredondamento_Model_Tabela(
      array(
        'id' => 1,
        'instituicao' => 1,
        'nome' => 'Tabela geral de notas num�ricas',
        'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
      )
    ));

    // Mock para tabela de arredondamento
    $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->with(array(), array(
            'instituicao' => 1,
            'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA))
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padr�o pelo mock
    $this->_mapper->setTabelaDataMapper($mock);
    $tabelas = $this->_mapper->findTabelaArredondamento($instance);
  }
}