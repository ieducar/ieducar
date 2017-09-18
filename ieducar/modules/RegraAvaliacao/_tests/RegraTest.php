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

require_once 'RegraAvaliacao/Model/Regra.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'FormulaMedia/Model/FormulaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';

/**
 * RegraTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class RegraTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new RegraAvaliacao_Model_Regra();
  }

  public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('RegraAvaliacao_Model_RegraDataMapper', $this->_entity->getDataMapper());
  }

  public function testEntityValidators()
  {
    // Valores de retorno
    $returnFormulaValue = array(
      new FormulaMedia_Model_Formula(array('nome' => '1� ao 3� ano')),
      new FormulaMedia_Model_Formula(array('nome' => 'Recupera��o geral'))
    );

    $returnTabelaValue = array(
      new TabelaArredondamento_Model_Tabela(array(
        'instituicao' => 1,
        'nome'        => 'Tabela gen�rica de notas num�ricas',
        'tipoNota'    => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
      ))
    );

    $returnValue = array(array('cod_instituicao' => 1, 'nm_instituicao' => 'Institui��o'));

    // Mock para f�rmula de m�dia
    $mockFormula = $this->getCleanMock('FormulaMedia_Model_FormulaDataMapper');
    $mockFormula->expects($this->any())
                ->method('findAll')
                ->will($this->onConsecutiveCalls(
                    $returnFormulaValue[0], $returnFormulaValue[1])
                  );

    // Mock para tabela de arredondamento
    $mockTabela = $this->getCleanMock('TabelaArredondamento_Model_TabelaDataMapper');
    $mockTabela->expects($this->any())
               ->method('findAll')
               ->will($this->returnValue($returnTabelaValue));

    // Mock para institui��o
    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->any())
         ->method('lista')
         ->will($this->returnValue($returnValue));

    // Registra a inst�ncia no reposit�rio de classes de CoreExt_Entity
    $this->_entity->addClassToStorage('clsPmieducarInstituicao', $mock);

    // Substitui o data mapper de f�rmula padr�o pelo mock
    $this->_entity->getDataMapper()->setFormulaDataMapper($mockFormula);

    // Substitui o data mapper de tabela padr�o pelo mock
    $this->_entity->getDataMapper()->setTabelaDataMapper($mockTabela);

    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_String',  $validators['nome']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['instituicao']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['formulaMedia']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['formulaRecuperacao']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['media']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tabelaArredondamento']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['porcentagemPresenca']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tipoNota']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tipoProgressao']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['parecerDescritivo']);
    $this->assertType('CoreExt_Validate_Choice',  $validators['tipoPresenca']);
  }
}