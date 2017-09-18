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
 * @package     FormulaMedia
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/modules/AreaConhecimento/_tests/AreaTest.php 862 2009-12-04T18:55:17.468486Z eriksen  $
 */

require_once 'FormulaMedia/Model/Formula.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';

/**
 * FormulaTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class FormulaTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected $_values = array(
      'E1' => 5,
      'E2' => 5,
      'E3' => 5,
      'E4' => 5,
      'Et' => 4,
      'Se' => 20,
      'Rc' => 0
    );

  protected function setUp()
  {
    $this->_entity = new FormulaMedia_Model_Formula();
  }

  public function testSubstituiCorretamenteAsTokens()
  {
    $formula = $this->_entity->replaceTokens('Se / Et', $this->_values);
    $this->assertEquals('20 / 4', $formula);
  }

  public function testFormulaDeMediaRetornaValorNumerico()
  {
    $this->_entity->formulaMedia = '(E1 + E2 + E3 + E4) / Et';
    $this->assertEquals(5, $this->_entity->execFormulaMedia($this->_values));
  }

  public function testFormulaDeRecuperacaoRetornaValorNumerico()
  {
    $this->_entity->formulaMedia = '((Se / Et * 0.6) + (Rc * 0.4))';
    $values = $this->_values;
    $values['Rc'] = 7;
    $nota = $this->_entity->execFormulaMedia($values);
    $this->assertEquals(5.8, $nota, '', 0.3);
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

    $this->_entity->addClassToStorage('clsPmieducarInstituicao', $mock);

    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_Choice', $validators['instituicao']);
    $this->assertType('CoreExt_Validate_String', $validators['nome']);
    $this->assertType('FormulaMedia_Validate_Formula', $validators['formulaMedia']);
    $this->assertType('CoreExt_Validate_Choice', $validators['tipoFormula']);

    // Se o tipo da f�rmula for de m�dia final, o validador ir� lan�ar uma
    // exce��o com a token Rc (Recupera��o)
    try {
      $validators['formulaMedia']->isValid('Se + Rc / 4');
      $this->fail('F�rmula deveria ter lan�ado exce��o (Se + Rc / 4) pois o '
                  . 'validador est� com a configura��o padr�o');
    }
    catch (Exception $e) {
    }

    // Configura a inst�ncia de FormulaMedia_Model_Formula para ser do tipo
    // "m�dia recupera��o", para verificar o validador.
    // Refer�ncias podem ter seus valores atribu�dos apenas na instancia��o
    // sendo assim imut�veis. Por isso, um novo objeto.
    $this->_entity = new FormulaMedia_Model_Formula(array('tipoFormula' => 2));
    $validators = $this->_entity->getDefaultValidatorCollection();

    try {
      $validators['formulaMedia']->isValid('Se + Rc / 4');
    }
    catch (Exception $e) {
      $this->fail('F�rmula n�o deveria ter lan�ado exce��o.');
    }
  }
}