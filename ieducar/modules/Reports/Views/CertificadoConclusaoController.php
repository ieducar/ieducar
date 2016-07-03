<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);
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
 * @author     Bruno Fritzen <bruno.fritzen@serpro.gov.br>
 * @category   i-Educar
 * @license    @@license@@
 * @package    Reports
 * @subpackage Modules
 * @since      Arquivo dispon�vel desde a vers�o 1.1.0
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
 * @since Classe dispon�vel desde a vers�o 1.1.0
 * @version @@package_version@@
 *
 */

require_once "lib/Portabilis/Controller/ReportCoreController.php";
require_once "Reports/Reports/CertificadoConclusaoReport.php";

class CertificadoConclusaoController extends Portabilis_Controller_ReportCoreController {

	protected $_titulo = 'Certificado de Conclus�o';
	
	function form() {
		$this->inputsHelper()->input('ano');
		$this->inputsHelper()->dynamic(array('instituicao','escola'));
		$this->inputsHelper()->simpleSearchMatricula();
		$this->inputsHelper()->date('data_conclusao', array('required' => true,'label' => 'Data de conclus�o'));
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