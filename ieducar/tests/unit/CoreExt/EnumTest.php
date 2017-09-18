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
 * @package     CoreExt_Enum
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/_stub/Enum1.php';
require_once 'CoreExt/_stub/Enum2.php';
require_once 'CoreExt/_stub/EnumCoffee.php';
require_once 'CoreExt/_stub/EnumString.php';

/**
 * CoreExt_EnumTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Enum
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_EnumTest extends UnitBaseTest
{
  public function testRetornaTodosOsValoresDoEnum()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(array(1), $enum->getKeys());
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(array(2), $enum->getKeys());
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals(array(0, 1, 2), $enum->getKeys());
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals(array('red'), $enum->getKeys());
  }

  public function testItemDeEnumRetornaDescricao()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(1, $enum->getValue(CoreExt_Enum1Stub::ONE));
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(2, $enum->getValue(CoreExt_Enum2Stub::TWO));
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals('Mocha', $enum->getValue(CoreExt_EnumCoffeeStub::MOCHA));
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals('#FF0000', $enum->getValue(CoreExt_EnumStringStub::RED));
  }

  public function testEnumAcessadosComoArray()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertEquals(1, $enum[CoreExt_Enum1Stub::ONE]);
    $enum = CoreExt_Enum2Stub::getInstance();
    $this->assertEquals(2, $enum[CoreExt_Enum2Stub::TWO]);
    $enum = CoreExt_EnumCoffeeStub::getInstance();
    $this->assertEquals('Mocha', $enum[CoreExt_EnumCoffeeStub::MOCHA]);
    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertEquals('#FF0000', $enum[CoreExt_EnumStringStub::RED]);
  }

  public function testEnumAcessosDiversosComoArray()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $this->assertTrue(isset($enum[CoreExt_Enum1Stub::ONE]));

    $this->assertEquals(array(1), $enum->getValues());
    $this->assertEquals(array(1), $enum->getKeys());
    $this->assertEquals(array(1 => 1), $enum->getEnums());
    $this->assertEquals(1, $enum->getKey(CoreExt_Enum1Stub::ONE));

    $enum = CoreExt_EnumStringStub::getInstance();
    $this->assertTrue(isset($enum[CoreExt_EnumStringStub::RED]));

    $this->assertEquals(array('#FF0000'), $enum->getValues());
    $this->assertEquals(array('red'), $enum->getKeys());
    $this->assertEquals(array('red' => '#FF0000'), $enum->getEnums());
    $this->assertEquals('red', $enum->getKey('#FF0000'));
  }

  /**
   * @expectedException CoreExt_Exception
   */
  public function testEnumEApenasLeitura()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    $enum['foo'] = 'bar';
  }

  /**
   * @expectedException CoreExt_Exception
   */
  public function testEnumNaoPermiteRemoverEntrada()
  {
    $enum = CoreExt_Enum1Stub::getInstance();
    unset($enum['foo']);
  }
}