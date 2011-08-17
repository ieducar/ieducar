<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/clsPermissoes.inc.php';
require_once 'include/pmieducar/clsPmieducarEscolaAnoLetivo.inc.php';
require_once 'CoreExt/View/Helper/TableHelper.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar');
    $this->processoAp = 624;
  }
}

class indice
{
  function RenderHTML()
  {
    return "
      <table width='100%' style='height: 100%;'>
        <tr align=center valign='top'>
          <td>
            <img src='imagens/i-educar/splashscreen.jpg' alt='i-educar' style='padding-top: 50px'>
          </td>
        </tr>
      </table>
    ";
  }
}

class detalhe extends clsDetalhe
{
  function Gerar()
  {
    global $id_pessoa;

    $this->titulo = 'Escola - Visão geral';

    // Escola em que o servidor está alocado
    $servidorAlocacao = new clsPmieducarServidorAlocacao();
    $servidores = $servidorAlocacao->lista(NULL, 1, NULL, NULL, NULL, $id_pessoa);
    if (1 == count($servidores)) {
      $codEscola = $servidores[0]['ref_cod_escola'];
    }

    $matriculas = array();

    // Ano letivo em andamento
    $escolaAnoLetivo = new clsPmieducarEscolaAnoLetivo();
    $escolaAnoLetivo->setOrderby('ano DESC');
    $anosLetivos = $escolaAnoLetivo->lista($codEscola);

    // Ano em andamento
    $anoAndamento = NULL;

    foreach ($anosLetivos as $anoLetivo) {
      $ano = $anoLetivo['ano'];
      if ($anoLetivo['andamento'] == 1) {
        $anoAndamento = $ano;
        continue;
      }
      $anosAnteriores[$ano] = $ano;
    }

    if (is_null($anoAndamento)) {
      $anoAndamento = date('Y');
      $anoAnterior  = $anoAndamento - 1;
      $anosAnteriores = array(
        $anoAnterior => $anoAnterior
      );
    }
    elseif (!isset($anosAnteriores)) {
      $anoAnterior  = $anoAndamento - 1;
      $anosAnteriores = array(
        $anoAnterior => $anoAnterior
      );
    }

    $anosAnteriores = implode(', ', $anosAnteriores);

    // SELECT para retornar todas a quantidade de matrículas agrupadas por ano/turma
    $sql = sprintf("
      SELECT
        pe.cod_escola AS cod_escola,
        pec.nm_escola AS nome,
        pt.nm_turma AS nome_turma,
        pm.ano AS ano,
        MAX(pt.max_aluno) AS vagas,
        COUNT(pmt.ref_cod_matricula) AS total_matricula,
        (MAX(pt.max_aluno) - COUNT(pmt.ref_cod_matricula)) AS vagas_restantes
      FROM
        pmieducar.matricula pm,
        pmieducar.matricula_turma pmt,
        pmieducar.turma pt,
        pmieducar.escola pe,
        pmieducar.escola_complemento pec
      WHERE
        pe.cod_escola = pec.ref_cod_escola AND
        pe.cod_escola = pm.ref_ref_cod_escola AND
        pm.cod_matricula = pmt.ref_cod_matricula AND
        pt.cod_turma = pmt.ref_cod_turma AND
        pmt.data_exclusao IS NULL AND
        pm.ano IN (%s) AND
        pe.cod_escola = %d AND
        pt.visivel = TRUE
      GROUP BY
        pe.cod_escola,
        pec.nm_escola,
        pm.ano,
        pt.nm_turma
      UNION
      SELECT
        pe.cod_escola AS cod_escola,
        pec.nm_escola AS nome,
        pt.nm_turma AS nome_turma,
        pm.ano AS ano,
        MAX(pt.max_aluno) AS vagas,
        COUNT(pmt.ref_cod_matricula) AS total_matricula,
        (MAX(pt.max_aluno) - COUNT(pmt.ref_cod_matricula)) AS vagas_restantes
      FROM
        pmieducar.matricula pm,
        pmieducar.matricula_turma pmt,
        pmieducar.turma pt,
        pmieducar.escola pe,
        pmieducar.escola_complemento pec
      WHERE
        pe.cod_escola = pec.ref_cod_escola AND
        pe.cod_escola = pm.ref_ref_cod_escola AND
        pm.cod_matricula = pmt.ref_cod_matricula AND
        pt.cod_turma = pmt.ref_cod_turma AND
        pmt.ativo = 1 AND
        pm.ativo = 1 AND
        pm.ano NOT IN (%s) AND
        pe.cod_escola = %d AND
        pt.visivel = TRUE
      GROUP BY
        pe.cod_escola,
        pec.nm_escola,
        pm.ano,
        pt.nm_turma
      ORDER BY
        nome ASC,
        nome_turma ASC,
        ano ASC,
        total_matricula DESC
    ", $anosAnteriores, $codEscola, $anosAnteriores, $codEscola);

    $db = new clsBanco();
    $db->Consulta($sql);

    // Matrículas agrupadas por ano/turma
    $matriculas = array();

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $escola = $tupla['nome'];
      $matriculas[$tupla['ano']][$tupla['nome_turma']] = array(
        'vagas' => $tupla['vagas'],
        'total' => $tupla['total_matricula'],
        'vagas_restantes' => $tupla['vagas_restantes']
      );
    }

    $sql = sprintf('
      SELECT
        pe.cod_escola AS cod_escola,
        pec.nm_escola AS nome,
        pt.nm_turma AS nome_turma,
        pt.cod_turma AS cod_turma,
        MAX(pt.max_aluno) AS vagas
      FROM
        pmieducar.turma pt,
        pmieducar.escola pe,
        pmieducar.escola_complemento pec
      WHERE
        pe.cod_escola = pec.ref_cod_escola AND
        pt.ref_ref_cod_escola = pe.cod_escola AND
        pe.cod_escola = %d AND
        pt.visivel = TRUE
      GROUP BY
        pe.cod_escola,
        pec.nm_escola,
        pt.nm_turma,
        pt.cod_turma
      ORDER BY
        nome ASC,
        nome_turma ASC', $codEscola);

    $db->Consulta($sql);
    $codigoTurma = array();

    while ($db->ProximoRegistro()) {
      $tupla  = $db->Tupla();
      $escola = trim($tupla['nome']);
      $codEscola = $tupla['cod_escola'];

      $codigoTurma[$tupla['nome_turma']] = $tupla['cod_turma'];

      if (!isset($matriculas[date('Y', time())][$tupla['nome_turma']])) {
        $matriculas[date('Y', time())][$tupla['nome_turma']] = array(
          'total'           => 0,
          'vagas'           => $tupla['vagas'],
          'vagas_restantes' => $tupla['vagas']
        );
      }
    }

    $this->addDetalhe(array('Escola', $escola));
    $this->addDetalhe(array('Ano letivo', $anoAndamento));

    // Quantidade de matrículas por turma/ano
    $data = array();

    // Anos com matrículas registradas
    $anos = array();

    $zebra = array(
      0 => array('style' => 'background-color: #E4E9ED'),
      1 => array('style' => 'background-color: #FFFFFF')
    );

    $attrs = array('style' => 'padding: 5px');

    // Usa helper de tabela para criar a tabela de notas/faltas
    $table = CoreExt_View_Helper_TableHelper::getInstance();

    // Helper para urls
    $url = CoreExt_View_Helper_UrlHelper::getInstance();

    // Labels para o cabeçalho
    $labels = array(array('data' => 'Turma', 'attributes' => $attrs));

    foreach ($matriculas as $ano => $matriculados) {
      $anos[$ano] = $ano;

      foreach ($matriculados as $turma => $total) {
        $data[$turma][$ano] = $total;
      }

      $labels[] = array('data' => $ano, 'attributes' => $attrs);

      // Inclui informações adicionais
      if ($ano == $anoAndamento) {
        $labels[] = array('data' => 'Vagas', 'attributes' => $attrs);
        $labels[] = array('data' => 'Vagas restantes', 'attributes' => $attrs);
        $labels[] = array('data' => '&nbsp;', 'attributes' => $attrs);
      }
    }

    // Cabeçalho da tabela
    $table->addHeaderRow($labels, array('style' => 'font-weight: bold; background-color: #A1B3BD'));

    // Conteúdo da tabela
    $i = 0;
    $totalAno = array();

    // intranet/educar_matriculas_turma_cad.php?ref_cod_turma=7

    foreach ($data as $turma => $matriculas) {
      $row = array();
      $row[] = array('data' => $turma, 'attributes' => $attrs);

      foreach ($anos as $ano) {
        $row[] = array(
          'data' => isset($matriculas[$ano]['total']) ? $matriculas[$ano]['total'] : '-',
          'attributes' => $attrs
        );

        if ($ano == $anoAndamento) {
          $detalhes = $url->l('ver lista de alunos', '/intranet/educar_matriculas_turma_cad.php', array('query' => array('ref_cod_turma' => $codigoTurma[$turma])));

          $row[] = array('data' => $matriculas[$ano]['vagas'], 'attributes' => $attrs);
          $row[] = array('data' => $matriculas[$ano]['vagas_restantes'], 'attributes' => $attrs);
          $row[] = array('data' => $detalhes, 'attributes' => $attrs);

          $totalVagas    += (isset($matriculas[$ano]['vagas']) ? $matriculas[$ano]['vagas'] : 0);
          $totalRestante += (isset($matriculas[$ano]['vagas_restantes']) ? $matriculas[$ano]['vagas_restantes'] : 0);
        }

        $totalAno[$ano] += (isset($matriculas[$ano]['total']) ? $matriculas[$ano]['total'] : 0);
      }

      $table->addBodyRow($row, $zebra[$i++ % 2]);
    }

    $row = array();
    $row[] = array('data' => '<b>Total</b>', 'attributes' => $attrs);
    foreach ($totalAno as $ano => $total) {
      $row[] = array('data' => '<b>' . $total . '</b>', 'attributes' => $attrs);

      if ($ano == $anoAndamento) {
        $row[] = array('data' => $totalVagas);
        $row[] = array('data' => $totalRestante);
        $row[] = array('data' => '&nbsp;');
      }
    }
    $table->addFooterRow($row, $zebra[$i++ % 2]);

    $this->addDetalhe(array('Turmas', $table));

    $this->largura = "100%";
  }
}

@session_start();
$id_pessoa = $_SESSION['id_pessoa'];
@session_write_close();

$permissoes = new clsPermissoes();
if (4 == $permissoes->nivel_acesso($id_pessoa)) {
  $miolo = new detalhe();
}
else {
  $miolo = new indice();
}

$pagina = new clsIndex();

$pagina->addForm($miolo);

$pagina->MakeAll();