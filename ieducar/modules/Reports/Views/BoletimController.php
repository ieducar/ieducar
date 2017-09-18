<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Reports
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once "lib/Portabilis/Controller/ReportCoreController.php";
require_once "Reports/Reports/BoletimReport.php";

/**
 * BoletimController class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Reports
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class BoletimController extends Portabilis_Controller_ReportCoreController
{

  protected $_titulo = 'Boletim Escolar';

  protected function _preRender(){

    parent::_preRender();

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""                                  => "Emiss&atilde;o de boletim escolar"             
    ));
    $this->enviaLocalizacao($localizacao->montar());     
  }    

	function form() {
    # TODO implementar / usar novo helper "matriculaSimpleSearch"

    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola', 'curso', 'serie', 'turma'));
    $this->inputsHelper()->dynamic('matricula', array('required' => false));
    $this->inputsHelper()->checkbox('manual', array('label' => 'Preenchimento manual?'));

    // carrega javascript Boletim.js
    $this->loadResourceAssets($this->getDispatcher());
  }


	function report() {
	  return new BoletimReport();
	}


  function beforeValidation() {
    $this->report->addArg('ano',         (int)$this->getRequest()->ano);
    $this->report->addArg('instituicao', (int)$this->getRequest()->ref_cod_instituicao);
    $this->report->addArg('escola',      (int)$this->getRequest()->ref_cod_escola);
    $this->report->addArg('curso',       (int)$this->getRequest()->ref_cod_curso);
    $this->report->addArg('serie',       (int)$this->getRequest()->ref_cod_serie);
    $this->report->addArg('turma',       (int)$this->getRequest()->ref_cod_turma);

    if (is_null($this->getRequest()->ref_cod_matricula))
      $this->report->addArg('matricula',0);
    else
      $this->report->addArg('matricula', (int)$this->getRequest()->ref_cod_matricula);

    $this->report->addArg('manual', $this->getRequest()->manual ? 1 : 0);
}
}

?>
