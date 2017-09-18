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

require_once 'CoreExt/Controller/Abstract.php';

/**
 * CoreExt_Controller_Front class.
 *
 * Essa � uma implementa��o simples do design pattern {@link http://martinfowler.com/eaaCatalog/frontController.html front controller},
 * que tem como objetivo manusear e encaminhar a requisi��o para uma classe
 * que se responsabilize pelo processamento do recurso solicitado.
 *
 * Apesar de ser um front controller, o encaminhamento para uma classe
 * {@link http://en.wikipedia.org/wiki/Command_pattern command} n�o est�
 * implementado.
 *
 * Entretanto, est� dispon�vel o encaminhamento para uma classe que implemente
 * o pattern {@link http://martinfowler.com/eaaCatalog/pageController.html page controller},
 * ou seja, qualquer classe que implemente a interface
 * CoreExt_Controller_Page_Interface.
 *
 * O processo de encaminhamento (dispatching), � definido por uma classe
 * {@link http://en.wikipedia.org/wiki/Strategy_pattern strategy}.
 *
 * Algumas op��es afetam o comportamento dessa classe. As op��es dispon�veis
 * para configurar uma inst�ncia da classe s�o:
 * - basepath: diret�rio em que os implementadores de command e page controller
 *   ser�o procurados
 * - controller_dir: determina o nome do diret�rio em que os controllers dever�o
 *   estar salvos
 * - controller_type: tipo de controller a ser instanciado. Uma inst�ncia de
 *   CoreExt_Controller_Front pode usar apenas um tipo por processo de
 *   dispatch() e o valor dessa op��o determina qual strategy de dispatch ser�
 *   utilizada (CoreExt_Controller_Strategy).
 *
 * Por padr�o, os valores de controller_dir e controller_type s�o definidos para
 * 'Views' e 2, respectivamente. Isso significa que a estrat�gia de page
 * controller ser� utilizada durante a chamada ao m�todo dispatch().
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class CoreExt_Controller_Front extends CoreExt_Controller_Abstract
{
  /**
   * Op��es para defini��o de qual tipo de controller utilizar durante a
   * execu��o de dispatch().
   * @var int
   */
  const CONTROLLER_FRONT = 1;
  const CONTROLLER_PAGE  = 2;

  /**
   * A inst�ncia singleton de CoreExt_Controller_Interface.
   * @var CoreExt_Controller_Interface|NULL
   */
  protected static $_instance = NULL;

  /**
   * Op��es de configura��o geral da classe.
   * @var array
   */
  protected $_options = array(
    'basepath'        => NULL,
    'controller_type' => self::CONTROLLER_PAGE,
    'controller_dir'  => 'Views'
  );

  /**
   * Cont�m os valores padr�o da configura��o.
   * @var array
   */
  protected $_defaultOptions = array();

  /**
   * Uma inst�ncia de CoreExt_View_Abstract
   * @var CoreExt_View_Abstract
   */
  protected $_view = NULL;

  /**
   * Construtor singleton.
   */
  protected function __construct()
  {
    $this->_defaultOptions = $this->getOptions();
  }

  /**
   * Retorna a inst�ncia singleton.
   * @return CoreExt_Controller_Front
   */
  public static function getInstance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Recupera os valores de configura��o original da inst�ncia.
   * @return CoreExt_Configurable Prov� interface flu�da
   */
  public function resetOptions()
  {
    $this->setOptions($this->_defaultOptions);
    return $this;
  }

  /**
   * Encaminha a execu��o para o objeto CoreExt_Dispatcher_Interface apropriado.
   * @return CoreExt_Controller_Interface Prov� interface flu�da
   * @see CoreExt_Controller_Interface#dispatch()
   */
  public function dispatch()
  {
    $this->_getControllerStrategy()->dispatch();
    return $this;
  }

  /**
   * Retorna o conte�do gerado pelo controller.
   * @return string
   */
  public function getViewContents()
  {
    return $this->getView()->getContents();
  }

  /**
   * Setter.
   * @param CoreExt_View_Abstract $view
   * @return CoreExt_Controller_Interface Prov� interface flu�da
   */
  public function setView(CoreExt_View_Abstract $view)
  {
    $this->_view = $view;
    return $this;
  }

  /**
   * Getter para uma inst�ncia de CoreExt_View_Abstract.
   *
   * Inst�ncia via lazy initialization uma inst�ncia de CoreExt_View caso
   * nenhuma seja explicitamente atribu�da a inst�ncia atual.
   *
   * @return CoreExt_View_Abstract
   */
  public function getView()
  {
    if (is_null($this->_view)) {
      require_once 'CoreExt/View.php';
      $this->setView(new CoreExt_View());
    }
    return $this->_view;
  }

  /**
   * Getter para uma inst�ncia de CoreExt_Controller_Dispatcher_Interface.
   *
   * Inst�ncia via lazy initialization uma inst�ncia de
   * CoreExt_Controller_Dispatcher caso nenhuma seja explicitamente
   * atribu�da a inst�ncia atual.
   *
   * @return CoreExt_Controller_Dispatcher_Interface
   */
  public function getDispatcher()
  {
    if (is_null($this->_dispatcher)) {
      $this->setDispatcher($this->_getControllerStrategy());
    }
    return $this->_dispatcher;
  }

  /**
   * Getter para a estrat�gia de controller, definida em runtime.
   * @return CoreExt_Controller_Strategy
   */
  protected function _getControllerStrategy()
  {
    switch($this->getOption('controller_type')) {
      case 1:
        require_once 'CoreExt/Controller/Dispatcher/Strategy/FrontStrategy.php';
        $strategy = 'CoreExt_Controller_Dispatcher_Strategy_FrontStrategy';
        break;
      case 2:
        require_once 'CoreExt/Controller/Dispatcher/Strategy/PageStrategy.php';
        $strategy = 'CoreExt_Controller_Dispatcher_Strategy_PageStrategy';
        break;
    }
    return new $strategy($this);
  }
}