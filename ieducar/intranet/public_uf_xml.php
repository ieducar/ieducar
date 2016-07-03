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
 * @author      Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  public
 * @subpackage  Enderecamento
 * @subpackage  Ajax
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

require_once 'include/pessoa/clsUf.inc.php';

// Id do pa�s na tabela public.pais
$id = isset($_GET['pais']) ? $_GET['pais'] : NULL;

header('Content-type: text/xml; charset=iso-8859-1');

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="iso-8859-1"?>' . PHP_EOL;
print '<query>' . PHP_EOL;

if ($id == strval(intval($id))) {
  $uf = new clsUf();
  $ufs = $uf->lista(NULL, NULL, $id, NULL, NULL, 'sigla_uf');

  foreach ($ufs as $uf) {
    print sprintf('  <estado sigla_uf="%s">%s</estado>' . PHP_EOL,
      $uf['sigla_uf'], $uf['nome']);
  }
}

print '</query>';