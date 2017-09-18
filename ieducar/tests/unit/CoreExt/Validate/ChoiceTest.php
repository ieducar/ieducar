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
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Validate/Choice.php';

/**
 * CoreExt_Validate_ChoiceTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Validate_ChoiceTest extends UnitBaseTest
{
  protected $_validator = NULL;

  protected $_choices = array(
    'bit' => array(0, 1),
    'various' => array('sim', 'n�o', 'nda')
  );

  protected function setUp()
  {
    $this->_validator = new CoreExt_Validate_Choice();
  }

  public function testValidaSeNenhumaOpcaoPadraoForInformada()
  {
    $this->assertTrue($this->_validator->isValid(0));
  }

  public function testEscolhaValida()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    $this->assertTrue($this->_validator->isValid(0), 'Falhou na asser��o "0" num�rico.');
    $this->assertTrue($this->_validator->isValid(1), 'Falhou na asser��o "1" num�rico.');

    // Teste para verificar como reage a tipos diferentes
    $this->assertTrue($this->_validator->isValid('0'), 'Falhou na asser��o "0" string.');
    $this->assertTrue($this->_validator->isValid('1'), 'Falhou na asser��o "1" string.');

    $this->_validator->setOptions(array('choices' => $this->_choices['various']));
    $this->assertTrue($this->_validator->isValid('sim'));
    $this->assertTrue($this->_validator->isValid('n�o'));
    $this->assertTrue($this->_validator->isValid('nda'));
  }

  public function testEscolhaInvalidaLancaExcecao()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    try {
      $this->_validator->isValid(2);
      $this->fail("CoreExt_Validate_Choice deveria ter lan�ado exce��o.");
    }
    catch (Exception $e) {
      $this->assertEquals('A op��o "2" n�o existe.', $e->getMessage());
    }

    // 'a' normalmente seria avaliado como 0, mas queremos garantir que isso
    // n�o ocorra, por isso transformamos tudo em string em _validate().
    try {
      $this->_validator->isValid('a');
      $this->fail("CoreExt_Validate_Choice deveria ter lan�ado exce��o.");
    }
    catch (Exception $e) {
      $this->assertEquals('A op��o "a" n�o existe.', $e->getMessage());
    }

    try {
      $this->_validator->isValid('0a');
      $this->fail("CoreExt_Validate_Choice deveria ter lan�ado exce��o.");
    }
    catch (Exception $e) {
      $this->assertEquals('A op��o "0a" n�o existe.', $e->getMessage());
    }
  }
}