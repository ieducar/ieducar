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

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

header('Content-type: text/xml');

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
print '<query xmlns="sugestoes">' . "\n";

if (is_numeric($_GET['curso'])) {
  $cod_curso = $_GET['curso'];

  $db = new clsBanco();
  $consulta  = sprintf('SELECT padrao_ano_escolar FROM pmieducar.curso WHERE cod_curso = \'%d\'', $cod_curso);

  $padrao_ano_escolar = $db->CampoUnico($consulta);

  if ($padrao_ano_escolar == 1) {
    $ano = is_numeric($_GET['ano']) ? sprintf(' AND ref_ano = \'%d\'', $_GET['ano']) : '';

    $db->Consulta(sprintf("
      SELECT
        cod_modulo,
        sequencial || '� ' || nm_tipo || ' - de ' || to_char(data_inicio,'dd/mm/yyyy') || ' at� ' || to_char(data_fim,'dd/mm/yyyy'),
        ref_ano,
        sequencial
      FROM
        pmieducar.ano_letivo_modulo,
        pmieducar.modulo
      WHERE
        modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
        AND modulo.ativo = 1
        %s
        AND ref_ref_cod_escola = '%s'
      ORDER BY
        data_inicio,
        data_fim ASC
    ", $ano, $_GET['esc']));

    if ($db->numLinhas()) {
      while ($db->ProximoRegistro()) {
        list($cod, $nome, $ano, $sequencial) = $db->Tupla();
        print sprintf('  <ano_letivo_modulo sequencial="%d" ano="%d" cod_modulo="%d">%s</ano_letivo_modulo>%s',
          $sequencial, $ano, $cod, $nome, "\n");
      }
    }
  }
  else {
    $ano       = $_GET['ano'];
    $cod_turma = $_GET['turma'];

    if (is_numeric($cod_turma)) {
      $db->Consulta(sprintf("
        SELECT
          ref_cod_modulo,
          nm_tipo || ' - de ' || to_char(data_inicio,'dd/mm/yyyy') || ' at� ' || to_char(data_fim,'dd/mm/yyyy'),
          sequencial
        FROM
          pmieducar.turma_modulo,
          pmieducar.modulo
        WHERE
          modulo.cod_modulo = turma_modulo.ref_cod_modulo
          AND ref_cod_turma = '%d'
          AND to_char(data_inicio,'yyyy') = %d
        ORDER BY
          data_inicio,
          data_fim ASC
      ", $cod_turma, $ano));
    }
    if ($db->numLinhas()) {
      while ($db->ProximoRegistro()) {
        list($cod, $nome,$sequencial) = $db->Tupla();
        print sprintf('  <ano_letivo_modulo sequencial="%d" ano="{%d}" cod_modulo="%d">%s</ano_letivo_modulo>%s',
          $sequencial, $ano, $cod, $nome, "\n");
      }
    }
  }
}

print '</query>';