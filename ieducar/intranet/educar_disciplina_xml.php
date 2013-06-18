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
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

header('Content-type: text/xml; charset=ISO-8859-1');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

$componentes = array();

// Seleciona os componentes de um curso ou s�rie
// cur = E serve para pegar todas as materias 
if (is_numeric($_GET['cur']) || $_GET['cur'] == 'E' || is_numeric($_GET['ser'])) {
  require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
  $mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();

  if (is_numeric($_GET['cur'])) {
    $componentes = $mapper->findComponentePorCurso($_GET['cur']);
  }
  else if ($_GET['cur'] == 'E') {
    $componentes = $mapper->findComponentePorCurso(0);
  }
  elseif(is_numeric($_GET['ser'])) {
    $componentes = $mapper->findComponentePorSerie($_GET['ser']);
  }
}

// Seleciona os componentes de uma escola-s�rie
if (is_numeric($_GET['esc']) && is_numeric($_GET['ser'])) {
  require_once 'App/Model/IedFinder.php';

  $componentes = App_Model_IedFinder::getEscolaSerieDisciplina($_GET['ser'],
    $_GET['esc']);
}

foreach ($componentes as $componente) {
  print sprintf(' <disciplina cod_disciplina="%d" carga_horaria="%d">%s</disciplina>%s',
    $componente->id, $componente->cargaHoraria, $componente, PHP_EOL);
}

echo "</query>";
