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
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_View
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'Core/_stub/View.php';
require_once 'Core/Controller/_stub/Page/Abstract.php';

/**
 * Core_ViewTest class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_View
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class Core_ViewTest extends UnitBaseTest
{
  protected $_pageController = NULL;
  protected $_view = NULL;

  public function __construct()
  {
    $this->_pageController = new Core_Controller_Page_AbstractStub();
    $this->_pageController->setOptions(array('processoAp' => 1, 'titulo' => 'foo'));
  }

  protected function setUp()
  {
    $this->_view = new Core_ViewStub($this->_pageController);
  }

  public function testTituloConfiguradoComValorDeConfiguracaoGlobal()
  {
    global $coreExt;
    $instituicao = $coreExt['Config']->app->template->vars->instituicao;

    $this->_view->MakeAll();
    $this->assertEquals($instituicao . ' | foo', $this->_view->getTitulo());
  }

  public function testProcessoApConfiguradoPeloValorDePageController()
  {
    $this->_view->MakeAll();
    $this->assertEquals(1, $this->_view->getProcessoAp());
  }
}