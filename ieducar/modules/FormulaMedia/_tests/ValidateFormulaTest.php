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
 * @version     $Id$
 */

require_once 'FormulaMedia/Validate/Formula.php';

/**
 * ValidateFormulaTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     FormulaMedia
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class ValidateFormulaTest extends UnitBaseTest
{
  public function testFormulaValida()
  {
    $formula = 'Se / Et';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  public function testFormulaValidaUsandoAliasDeMultiplicacao()
  {
    $formula = 'Se x 0.99 / Et';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  public function testFormulaValidaComNumericos()
  {
    $formula = 'Se * 0.5 / Et';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  /**
   * @expectedException Exception
   */
  public function testFormulaInvalidaQuandoUtilizaTokenNaoPermitido()
  {
    $formula = 'Rc * 0.4 + Se * 0.6';
    $validator = new FormulaMedia_Validate_Formula();
    $this->assertTrue($validator->isValid($formula));
  }

  public function testFormulaValidaUsandoParenteses()
  {
    $formula = '(Rc * 0.4) + (Se * 0.6)';
    $validator = new FormulaMedia_Validate_Formula(array('excludeToken' => NULL));
    $this->assertTrue($validator->isValid($formula));
  }

  /**
   * @expectedException FormulaMedia_Validate_Exception
   */
  public function testFormulaInvalidaPorErroDeSintaxe()
  {
    $formula = '(Rc * 0.4) + (Se * 0.6) ()';
    $validator = new FormulaMedia_Validate_Formula(array('excludeToken' => NULL));
    $this->assertTrue($validator->isValid($formula));
  }
}