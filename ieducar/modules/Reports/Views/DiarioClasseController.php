<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright 
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
 * @author      Carlos M. <carlos-morais.santos@serpro.gov.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Reports
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 
 * @version     $Id$
 */

require_once "lib/Portabilis/Controller/ReportCoreController.php";
require_once "Reports/Reports/DiarioClasseReport.php";

/**
 * DiarioClasseController class.
 *
 * @author      
 * @category    i-Educar
 * @license     @@license@@
 * @package     Reports
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 
 * @version     @@package_version@@
 */
class DiarioClasseController extends Portabilis_Controller_ReportCoreController
{

	protected $_titulo = 'Di&aacute;rio de Classe';

  function form() {
		$this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola', 'curso', 'serie', 'turma'));
 	}

  function report() {
		return new DiarioClasseReport();
	}

  function beforeValidation() {
		$this->report->addArg('cod_turma', (int)$this->getRequest()->ref_cod_turma);
	}
}

?>
