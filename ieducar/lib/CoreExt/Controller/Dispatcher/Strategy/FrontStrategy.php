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
 * @package   CoreExt_Controller
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Controller/Dispatcher/Abstract.php';
require_once 'CoreExt/Controller/Dispatcher/Strategy/Interface.php';

/**
 * CoreExt_Controller_Dispatcher_Strategy_FrontStrategy class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Controller_Dispatcher_Strategy_FrontStrategy
  extends CoreExt_Controller_Dispatcher_Abstract
  implements CoreExt_Controller_Dispatcher_Strategy_Interface
{
  /**
   * Inst�ncia de CoreExt_Controller_Interface.
   * @var CoreExt_Controller_Interface
   */
  protected $_controller = NULL;

  /**
   * Construtor.
   * @see CoreExt_Controller_Strategy_Interface#__construct($controller)
   */
  public function __construct(CoreExt_Controller_Interface $controller)
  {
    $this->setController($controller);
  }

  /**
   * @see CoreExt_Controller_Strategy_Interface#setController($controller)
   */
  public function setController(CoreExt_Controller_Interface $controller)
  {
    $this->_controller = $controller;
    return $this;
  }

  /**
   * @see CoreExt_Controller_Strategy_Interface#getController()
   */
  public function getController()
  {
    return $this->_controller;
  }

  /**
   * N�o implementado.
   * @see CoreExt_Controller_Strategy_Interface#dispatch()
   */
  public function dispatch()
  {
    require_once 'CoreExt/Controller/Dispatcher/Exception.php';
    throw new CoreExt_Controller_Dispatcher_Exception('M�todo CoreExt_Controller_Strategy_FrontStrategy::dispatch() n�o implementado.');
  }
}