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
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'TabelaArredondamento/Model/Tabela.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'FormulaMedia/Model/Formula.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';

/**
 * TabelaTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class TabelaTest extends UnitBaseTest
{
  protected $_entity = NULL;
  protected $_tabelaValores = array();

  protected function setUp()
  {
    $this->_entity = new TabelaArredondamento_Model_Tabela();

    // Cria uma tabela de arredondamento num�rica
    $data = array(
      'tabelaArredondamento' => 1,
      'nome'                 => NULL,
      'descricao'            => NULL,
      'valorMinimo'          => -1,
      'valorMaximo'          => 0
    );

    $tabelaValores = array();

    $range = range(0, 10, 0.5);
    $minValue = 0.249;
    $maxValue = 0.250;

    foreach ($range as $offset) {
      $nome = $offset;

      $min = $nome - $minValue;
      $max = $nome + $maxValue;

      if ($offset == 0) {
        $min = 0;
      }
      elseif ($offset == 10) {
        $max = 10;
      }

      $data['nome'] = $nome;
      $data['valorMinimo'] = $min;
      $data['valorMaximo'] = $max;

      $tabelaValores[] = new TabelaArredondamento_Model_TabelaValor($data);
    }

    $this->_tabelaValores = $tabelaValores;
  }

  protected function _getMockTabelaValor()
  {
    // Configura um
    $mapperMock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');
    $mapperMock->expects($this->once())
               ->method('findAll')
               ->will($this->returnValue($this->_tabelaValores));

    return $mapperMock;
  }

  public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('TabelaArredondamento_Model_TabelaDataMapper', $this->_entity->getDataMapper());
  }

  public function testEntityValidators()
  {
    // Valores de retorno
    $returnValue = array(array('cod_instituicao' => 1, 'nm_instituicao' => 'Institui��o'));

    // Mock para institui��o
    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->any())
         ->method('lista')
         ->will($this->returnValue($returnValue));

    // Registra a inst�ncia no reposit�rio de classes de CoreExt_Entity
    $this->_entity->addClassToStorage('clsPmieducarInstituicao', $mock);

    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_String',  $validators['nome']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['instituicao']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tipoNota']);
  }

  public function testArredondamentoDeNota()
  {
    $this->_entity->getDataMapper()->setTabelaValorDataMapper($this->_getMockTabelaValor());
    $this->assertEquals(5, $this->_entity->round(5));
    $this->assertEquals(7, $this->_entity->round(7.250));

    try {
      $this->_entity->round(11);
      $this->fail('M�todo round() deveria ter lan�ado uma exce��o.');
    }
    catch (CoreExt_Exception_InvalidArgumentException $e) {
    }
  }

  public function testCalculoDeNotaNecessariaParaMedia()
  {
    $this->_entity->getDataMapper()->setTabelaValorDataMapper($this->_getMockTabelaValor());

    $formula = new FormulaMedia_Model_Formula(array(
      'formulaMedia' => '(Se / Et * 0.6) + (Rc * 0.4)',
      'tipoFormula'  => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO
    ));

    $expected = new TabelaArredondamento_Model_TabelaValor(array(
      'nome'                 => 10,
      'valorMinimo'          => 9.751,
      'valorMaximo'          => 10
    ));

    $data = array(
      'formulaValues' => array(
        'Se' => 13.334,
        'Et' => 4,
        'Rc' => NULL
      ),
      'expected' => array(
        'var'   => 'Rc',
        'value' => 6
      )
    );

    $ret = $this->_entity->predictValue($formula, $data);
    $this->assertEquals(array($expected->nome, $expected->valorMinimo, $expected->valorMaximo),
                        array($ret->nome, $ret->valorMinimo, $ret->valorMaximo));

    $expected = new TabelaArredondamento_Model_TabelaValor(array(
      'nome'        => 9,
      'valorMinimo' => 8.751,
      'valorMaximo' => 9.250
    ));

    $data = array(
      'formulaValues' => array(
        'Se' => 16,
        'Et' => 4,
        'Rc' => NULL
      ),
      'expected' => array(
        'var'   => 'Rc',
        'value' => 6
      )
    );

    $ret = $this->_entity->predictValue($formula, $data);
    $this->assertEquals(array($expected->nome, $expected->valorMinimo, $expected->valorMaximo),
                        array($ret->nome, $ret->valorMinimo, $ret->valorMaximo));

    $formula = new FormulaMedia_Model_Formula(array(
      'formulaMedia' => '((E1 + E2 + E3 + E4) / 4 * 0.6) + (Rc * 0.4)',
      'tipoFormula'  => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO
    ));

    $expected = new TabelaArredondamento_Model_TabelaValor(array(
      'nome'        => 9,
      'valorMinimo' => 8.751,
      'valorMaximo' => 9.250
    ));

    $data = array(
      'formulaValues' => array(
        'Se' => NULL,
        'Et' => NULL,
        'E1' => 4,
        'E2' => 4,
        'E3' => 4,
        'E4' => 4,
        'Rc' => NULL
      ),
      'expected' => array(
        'var'   => 'Rc',
        'value' => 6
      )
    );

    $ret = $this->_entity->predictValue($formula, $data);
    $this->assertEquals(array($expected->nome, $expected->valorMinimo, $expected->valorMaximo),
                        array($ret->nome, $ret->valorMinimo, $ret->valorMaximo));
  }

  /**
   * @group CoreExt_Locale
   */
  public function testArredondamentoDeNotaComLocaleDiferenteDoPadrao()
  {
    $this->_entity->getDataMapper()->setTabelaValorDataMapper($this->_getMockTabelaValor());
    $this->assertEquals(5, $this->_entity->round('5,005'));

    $locale = CoreExt_Locale::getInstance();
    $locale->setLocale('pt_BR');
    $this->assertEquals(8, $this->_entity->round('8,250'));
  }
}