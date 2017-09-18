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

require_once 'CoreExt/Controller/_stub/Dispatcher.php';
require_once 'CoreExt/Controller/Request.php';

/**
 * CoreExt_Controller_DispatcherTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Controller_DispatcherTest extends UnitBaseTest
{
  protected $_dispatcher = NULL;

  protected $_uris = array(
    0 => array('uri' => 'http://www.example.com/'),
    1 => array('uri' => 'http://www.example.com/index.php'),
    2 => array('uri' => 'http://www.example.com/controller/action'),
    3 => array('uri' => 'http://www.example.com/index.php/controller/action'),
    4 => array(
      'uri' => 'http://www.example.com/module/controller/action',
      'baseurl' => 'http://www.example.com/module'
    ),
    5 => array(
      'uri' => 'http://www.example.com/module/index.php/controller/action',
      'baseurl' => 'http://www.example.com/module'
    ),
    6 => array(
      'uri' => 'http://www.example.com/module/controller',
      'baseurl' => 'http://www.example.com/module'
    )
  );

  /**
   * Configura SCRIPT_FILENAME como forma de assegurar que o nome do script
   * ser� desconsiderado na defini��o do controller e da action.
   */
  protected function setUp()
  {
    $_SERVER['REQUEST_URI'] = $this->_uris[0]['uri'];
    $_SERVER['SCRIPT_FILENAME'] = '/var/www/ieducar/index.php';
    $this->_dispatcher = new CoreExt_Controller_Dispatcher_AbstractStub();
  }

  protected function _setRequestUri($index = 0)
  {
    $_SERVER['REQUEST_URI'] = array_key_exists($index, $this->_uris) ?
      $this->_uris[$index]['uri'] : $this->_uris[$index = 0]['uri'];

    // Configura a baseurl
    if (isset($this->_uris[$index]['baseurl'])) {
      $this->_dispatcher->getRequest()->setOptions(array('baseurl' => $this->_uris[$index]['baseurl']));
    }
  }

  protected function _getRequestUri($index = 0)
  {
    return array_key_exists($index, $this->_uris) ?
      $this->_uris[$index]['uri'] : $this->_uris[0]['uri'];
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_dispatcher->setOptions(array('foo' => 'bar'));
  }

  public function testDispatcherEstabeleceControllerDefault()
  {
    $this->assertEquals('index', $this->_dispatcher->getControllerName(), $this->_getRequestUri(0));
    $this->_setRequestUri(1);
    $this->assertEquals('index', $this->_dispatcher->getControllerName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceControllerDefaultConfigurado()
  {
    $this->_dispatcher->setOptions(array('controller_default_name' => 'controller'));
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceActionDefault()
  {
    $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(0));
    $this->_setRequestUri(1);
    $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceActionDefaultConfigurada()
  {
    $this->_dispatcher->setOptions(array('action_default_name' => 'action'));
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceController()
  {
    $this->_setRequestUri(2);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(2));
    $this->_setRequestUri(3);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(3));
    $this->_setRequestUri(4);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(4));
    $this->_setRequestUri(5);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(5));
    $this->_setRequestUri(6);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(6));
  }

  public function testDispatcherEstabeleceAction()
  {
    $this->_setRequestUri(2);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(2));
    $this->_setRequestUri(3);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(3));
    $this->_setRequestUri(4);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(4));
    $this->_setRequestUri(5);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(5));
    $this->_setRequestUri(6);
    $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(6));
  }
}