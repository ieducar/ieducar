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
 * Cria e configura um front controller para encaminhar as requisi��es para
 * page controllers especializados no diret�rio modules/.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Modules
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once '../includes/bootstrap.php';
require_once 'include/clsBanco.inc.php';
require_once 'App/Model/IedFinder.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/Controller/Request.php';
require_once 'CoreExt/Controller/Front.php';
require_once 'CoreExt/DataMapper.php';

// Objeto de requisi��o
$request = new CoreExt_Controller_Request();

// Helper de URL. Auxilia para criar uma URL no formato http://www.example.org/module
$url = CoreExt_View_Helper_UrlHelper::getInstance();
$url = $url->url($request->get('REQUEST_URI'), array('components' => CoreExt_View_Helper_UrlHelper::URL_HOST));

// Configura o baseurl da request
$request->setBaseurl(sprintf('%s/module', $url));

// Configura o DataMapper para usar uma inst�ncia de clsBanco com fetch de resultados
// usando o tipo FETCH_ASSOC
CoreExt_DataMapper::setDefaultDbAdapter(new clsBanco(array('fetchMode' => clsBanco::FETCH_ASSOC)));

// Inicia o Front Controller
$frontController = CoreExt_Controller_Front::getInstance();
$frontController->setRequest($request);

// Configura o caminho aonde os m�dulos est�o instalados
$frontController->setOptions(
  array('basepath' => PROJECT_ROOT . DS . 'modules')
);
$frontController->dispatch();

// Resultado
print $frontController->getViewContents();