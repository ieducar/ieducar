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
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Controller/Request.php';

/**
 * CoreExt_Controller_RequestTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Controller_RequestTest extends UnitBaseTest
{
  protected $_request = NULL;

  protected function setUp()
  {
    $this->_request = new CoreExt_Controller_Request();
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_request->setOptions(array('foo' => 'bar'));
  }

  public function testRetornaNullCasoNaoEstejaSetadoNasSuperglobaisGetPostCookieEServer()
  {
    $this->assertNull($this->_request->get('foo'));
  }

  public function testVariavelEstaSetada()
  {
    $_GET['name'] = 'Foo';
    $this->assertTrue(isset($this->_request->name));
    unset($_GET['name']);
    $this->assertFalse(isset($this->_request->name));
  }

  public function testRecuperaParametroDeRequisicaoGet()
  {
    $_GET['name'] = 'Foo';
    $this->assertEquals($_GET['name'], $this->_request->get('name'));
  }

  public function testRecuperaParametroDeRequisicaoPost()
  {
    $_POST['name'] = 'Foo';
    $this->assertEquals($_POST['name'], $this->_request->get('name'));
  }

  public function testRecuperaParametroDoCookie()
  {
    $_COOKIE['name'] = 'Foo';
    $this->assertEquals($_COOKIE['name'], $this->_request->get('name'));
  }

  public function testRecuperaParametroDoServer() {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
    $this->assertEquals($_SERVER['REQUEST_URI'], $this->_request->get('REQUEST_URI'));
  }

  public function testConfiguraBaseurlComSchemeEHostPorPadrao() {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
    $this->assertEquals('http://www.example.com', $this->_request->getBaseurl());
  }
}