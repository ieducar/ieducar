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

require_once 'CoreExt/Validate/ChoiceMultiple.php';

/**
 * CoreExt_Validate_ChoiceMultipleTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Validate_ChoiceMultipleTest extends UnitBaseTest
{
  protected $_validator = NULL;

  protected $_choices = array(
    'bit' => array(0, 1),
    'various' => array('sim', 'n�o', 'nda')
  );

  protected function setUp()
  {
    $this->_validator = new CoreExt_Validate_ChoiceMultiple();
  }

  public function testEscolhaMultiplaValida()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    $this->assertTrue($this->_validator->isValid(array(0, 1)));

    // Testa com valor igual, mas tipo de dado diferente
    $this->assertTrue($this->_validator->isValid(array('0', '1')));
  }

  public function testEscolhaMultiplaInvalidaLancaExcecao()
  {
    $this->_validator->setOptions(array('choices' => $this->_choices['bit']));
    try {
      $this->_validator->isValid(array(0, 2, 3));
      $this->fail("CoreExt_Validate_ChoiceMultiple deveria ter lan�ado exce��o.");
    }
    catch (Exception $e) {
      $this->assertEquals('As op��es "2, 3" n�o existem.', $e->getMessage());
    }

    // 'a' e '0a' normalmente seriam avaliados como '0' e '1' mas n�o queremos
    // esse tipo de comportamento.
    try {
      $this->_validator->isValid(array(0, 'a', '1a'));
      $this->fail("CoreExt_Validate_ChoiceMultiple deveria ter lan�ado exce��o.");
    }
    catch (Exception $e) {
      $this->assertEquals('As op��es "a, 1a" n�o existem.', $e->getMessage());
    }
  }
}