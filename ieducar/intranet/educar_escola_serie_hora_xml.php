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
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

header('Content-type: text/xml; encoding=ISO-8859-1');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

if (is_numeric($_GET["esc"]) && is_numeric($_GET["ser"])) {
  $db = new clsBanco();
  $db->Consulta("SELECT to_char(hora_inicial,'hh24:mm'), to_char(hora_final,'hh24:mm'), "
      . "to_char(hora_inicio_intervalo,'hh24:mm'), to_char(hora_fim_intervalo,'hh24:mm') "
      . "FROM pmieducar.escola_serie WHERE ref_cod_escola = '{$_GET["esc"]}' AND "
      . "ref_cod_serie = '{$_GET["ser"]}' AND ativo = 1");

  while ($db->ProximoRegistro()) {
    list($hora_inicial, $hora_final, $hora_inicio_intervalo,
      $hora_fim_intervalo) = $db->Tupla();

    echo "  <item>{$hora_inicial}</item>\n";
    echo "  <item>{$hora_final}</item>\n";
    echo "  <item>{$hora_inicio_intervalo}</item>\n";
    echo "  <item>{$hora_fim_intervalo}</item>\n";
  }
}
echo "</query>";