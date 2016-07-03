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
 * Retorna um XML com todas as regras de avalia��o para uma determinada
 * institui��o.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @todo      Refatorar para um design pattern como Service Layer em conjunto
 *   com um controller que permita respostas em JSON/XML.
 * @version   $Id$
 */

header('Content-type: text/xml; charset=ISO-8859-1');

require_once 'include/clsBanco.inc.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

if (isset($_GET['ins']) && is_numeric($_GET['ins'])) {
  $mapper = new RegraAvaliacao_Model_RegraDataMapper();

  $regras = $mapper->findAll(
    array('id', 'nome'),
    array('instituicao' => $_GET['ins'])
  );

  foreach ($regras as $regra) {
    print sprintf('  <regra id="%d">%s</regra>%s', $regra->id, $regra->nome, PHP_EOL);
  }
}
print '</query>';