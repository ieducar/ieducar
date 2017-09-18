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

require_once 'include/clsBase.inc.php';

/**
 * Core_View class.
 *
 * Prov� m�todos getters/setters e alguns m�todos sobrescritos para facilitar
 * a gera��o de p�ginas usando CoreExt_Controller_Page_Interface.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_View
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class Core_View extends clsBase
{
  /**
   * Uma inst�ncia de CoreExt_Controller_Page_Interface.
   * @var CoreExt_Controller_Page_Interface
   */
  protected $_pageController = NULL;

  /**
   * Construtor.
   * @param Core_Controller_Page_Interface $instance
   */
  public function __construct(Core_Controller_Page_Interface $instance)
  {
    parent::__construct();
    $this->_setPageController($instance);
  }

  /**
   * Setter.
   * @param Core_Controller_Page_Interface $instance
   * @return Core_View Prov� interface flu�da
   */
  protected function _setPageController(Core_Controller_Page_Interface $instance)
  {
    $this->_pageController = $instance;
    return $this;
  }

  /**
   * Getter.
   * @return CoreExt_Controller_Page_Interface
   */
  protected function _getPageController()
  {
    return $this->_pageController;
  }

  /**
   * Setter
   * @param string $titulo
   * @return Core_View Prov� interface flu�da
   */
  public function setTitulo($titulo)
  {
    parent::SetTitulo($titulo);
    return $this;
  }

  /**
   * Getter.
   * @return string
   */
  public function getTitulo()
  {
    return $this->titulo;
  }

  /**
   * Setter.
   * @param int $processo
   * @return Core_View Prov� interface flu�da
   */
  public function setProcessoAp($processo)
  {
    $this->processoAp = (int) $processo;
    return $this;
  }

  /**
   * Getter.
   * @return int
   */
  public function getProcessoAp()
  {
    return $this->processoAp;
  }

  /**
   * Configura algumas vari�veis de inst�ncia usando o container global
   * $coreExt.
   *
   * @global $coreExt
   * @see clsBase#Formular()
   */
  public function Formular()
  {
    global $coreExt;
    $instituicao = $coreExt['Config']->app->template->vars->instituicao;

    $this->setTitulo($instituicao . ' | ' . $this->_getPageController()->getBaseTitulo())
         ->setProcessoAp($this->_getPageController()->getBaseProcessoAp());
  }

  /**
   * Executa o m�todo de gera��o de HTML para a classe.
   * @param Core_View $instance
   */
  public static function generate($instance)
  {
    $viewBase = new self($instance);
    $viewBase->addForm($instance);
    $viewBase->MakeAll();
  }
}