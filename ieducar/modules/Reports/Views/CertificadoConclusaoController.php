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
 * @author     Bruno Fritzen <bruno.fritzen@serpro.gov.br>
 * @category   i-Educar
 * @license    @@license@@
 * @package    Reports
 * @subpackage Modules
 * @since      Arquivo disponível desde a versão 1.1.0
 * @version    $Id$
 */
/**
 * CertificadoConclusaoController class.
 *
 * @author Bruno Fritzen <bruno.fritzen@serpro.gov.br>
 * @category i-Educar
 * @license @@license@@
 * @package Reports
 * @subpackage Modules
 * @since Classe disponível desde a versão 1.1.0
 * @version @@package_version@@
 *
 */

require_once "lib/Portabilis/Controller/ReportCoreController.php";
require_once "Reports/Reports/CertificadoConclusaoReport.php";

class CertificadoConclusaoController extends Portabilis_Controller_ReportCoreController {

	protected $_titulo = 'Certificado de Conclusão';
	
	function form() {
		$this->inputsHelper()->input('ano');
		$this->inputsHelper()->dynamic(array('instituicao','escola'));
		$this->inputsHelper()->simpleSearchMatricula();
		$this->inputsHelper()->date('data_conclusao', array('required' => true,'label' => 'Data de conclusão'));
	}
	
	function report() {
		return new CertificadoConclusaoReport();
	}
	
	function beforeValidation() {
		$this->report->addArg('ano', (int)$this->getRequest()->ano);
		$this->report->addArg('instituicao', (int)$this->getRequest()->ref_cod_instituicao);
		$this->report->addArg('escola', (int)$this->getRequest()->ref_cod_escola);
		$this->report->addArg('matricula', (int)$this->getRequest()->matricula_id);
		$this->report->addArg('data_conclusao', $this->getRequest()->data_conclusao);
		$this->report->addArg('logo_instituicao', $this->getInstitutionLogoPath());
	}
	
	function getInstitutionLogoPath() {
		
 		if (!$GLOBALS['coreExt']['Config']->report->institution_logo_file_name)
			throw new Exception("No report.institution_logo_file_name defined in configurations!");
		
		$rootPath = dirname(dirname(dirname(dirname(__FILE__))));
		$filePath = $rootPath . "/modules/Reports/ReportLogos/". $GLOBALS['coreExt']['Config']->report->institution_logo_file_name;
		
		return $filePath;
	}
}
?>