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
 * @package     CoreExt_Locale
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Session.php';

/**
 * CoreExt_SessionTest class.
 *
 * Testa o componente CoreExt_Session, desabilitando o auto start (para evitar
 * erros "headers sent") e confiando na classe CoreExt_Session_Storage_Default.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Session
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_SessionTest extends UnitBaseTest
{
  protected $_session = NULL;

  protected function setUp()
  {
    $_SESSION = array();
    $this->_session = new CoreExt_Session(array('session_auto_start' => FALSE));
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_session->setOptions(array('foo' => 'bar'));
  }

  public function testInstanciaTemSessionInstanciaStorageDefaultPorPadrao()
  {
    $this->assertType('CoreExt_Session_Storage_Default', $this->_session->getSessionStorage());
  }

  public function testInstanciaESubclasseDeArrayAccess()
  {
    $this->assertType('ArrayAccess', $this->_session);
  }

  public function testInstanciaESubclasseDeCountable()
  {
    $this->assertType('Countable', $this->_session);
  }

  public function testInstanciaESubclasseDeIterator()
  {
    $this->assertType('Iterator', $this->_session);
  }

  /**
   * @backupGlobals disabled
   */
  public function testArrayAccess()
  {
    $this->assertNull($this->_session['foo'], '[foo] is not null');

    $this->_session['bar'] = 'foo';
    $this->assertEquals('foo', $this->_session['bar'], '[bar] != foo');

    //$this->_session->offsetUnset('bar');
    unset($this->_session['bar']);
    $this->assertNull($this->_session['bar'], '[bar] not unset');
  }

  /**
   * @backupGlobals disabled
   * @depends testArrayAccess
   */
  public function testCountable()
  {
    $this->assertEquals(0, count($this->_session));

    $this->_session['foo'] = 'bar';
    $this->assertEquals(1, count($this->_session));
  }

  /**
   * @backupGlobals enabled
   */
  public function testOverload()
  {
    $this->assertNull($this->_session->foo, '->foo is not null');

    $this->_session->bar = 'foo';
    $this->assertEquals('foo', $this->_session->bar, '->bar != foo');

    unset($this->_session->bar);
    $this->assertNull($this->_session->bar, '->bar not unset');
  }

  /**
   * Como CoreExt_Session_Abstract::offsetSet() converte a chave em string,
   * podemos acess�-los de forma din�mica na forma $session->$key em um
   * iterador foreach, por exemplo.
   */
  public function testIterator()
  {
    $expected = array(
      1 => 'bar1', 2 => 'bar2', 3 => 'bar3'
    );

    $this->_session[1] = 'bar1';
    $this->_session[2] = 'bar2';

    foreach ($this->_session as $key => $val) {
      $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
      $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
    }

    $this->_session[3] = 'bar3';
    foreach ($this->_session as $key => $val) {
      $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
      $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
    }
  }
}