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
 * @package   iEd
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

header('Content-type: text/xml');

require_once 'include/pmidrh/geral.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo '<?xml version="1.0" encoding="ISO-8859-15"?>' . "\n";
echo '<query xmlns="sugestoes">' . "\n";

if (isset($_GET['setor_pai'])) {
  $obj = new clsSetor();
  $lista = $obj->lista($_GET['setor_pai']);

  if ($lista) {
    foreach ($lista as $linha)  {
      echo '  <item>' . $linha['sgl_setor'] . '</item>' . "\n";
      echo '  <item>' . $linha['cod_setor'] . '</item>' . "\n";
    }
  }
}

echo '</query>';