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

require_once 'CoreExt/Controller/Front.php';
require_once 'CoreExt/Controller/Dispatcher/Strategy/FrontStrategy.php';

/**
 * CoreExt_Controller_Dispatcher_Strategy_FrontStrategyTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Controller_Dispatcher_Strategy_FrontStrategyTest extends UnitBaseTest
{
  protected $_frontController = NULL;
  protected $_pageStrategy = NULL;

  public function __construct()
  {
    $this->_path = realpath(dirname(__FILE__) . '/../../_stub');
  }

  protected function setUp()
  {
    $this->_frontController = CoreExt_Controller_Front::getInstance();
    $this->_frontController->setOptions(array('basepath' => $this->_path, 'controller_type' => CoreExt_Controller_Front::CONTROLLER_FRONT));
    $this->_pageStrategy = new CoreExt_Controller_Dispatcher_Strategy_FrontStrategy($this->_frontController);
  }

  /**
   * @expectedException CoreExt_Controller_Dispatcher_Exception
   */
  public function testRequisicaoAControllerNaoExistenteLancaExcecao()
  {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/PageController/view';
    $this->_pageStrategy->dispatch();
  }

  public function testControllerConfiguradoCorretamente()
  {
    $this->assertSame($this->_frontController, $this->_pageStrategy->getController());
  }
}