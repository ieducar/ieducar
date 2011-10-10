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

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsPDF.inc.php';
require_once 'Avaliacao/Service/Boletim.php';
require_once 'App/Model/MatriculaSituacao.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Resultado Final');
    $this->processoAp         = 823;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;

  var $ano;

  var $nm_escola;
  var $nm_instituicao;
  var $ref_cod_curso;
  var $pdf;

  var $nm_turma;
  var $nm_serie;
  var $nm_cidade;

  var $is_padrao;
  var $semestre;

  var $get_link;

  function renderHTML()
  {
    if ($_POST) {
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (empty($this->ref_cod_turma)) {
       echo '
         <script>
           alert("Erro ao gerar relat�rio!\nNenhuma turma selecionada!");
           window.parent.fechaExpansivel(\'div_dinamico_\'+(window.parent.DOM_divs.length-1));
         </script>';
       return TRUE;
    }

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
    $det_escola = $obj_escola->detalhe();
    $this->nm_escola = $det_escola['nome'];

    $obj_instituicao = new clsPmieducarInstituicao($det_escola['ref_cod_instituicao']);
    $det_instituicao = $obj_instituicao->detalhe();
    $this->nm_instituicao = $det_instituicao['nm_instituicao'];

    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $obj_turma->detalhe();
    $this->nm_turma = $det_turma['nm_turma'];

    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $det_serie = $obj_serie->detalhe();
    $this->nm_serie = $det_serie['nm_serie'];

    $eh_multi_seriado = FALSE;

    if (is_numeric($det_turma['ref_ref_cod_serie_mult'])) {
      $series = array();
      $series[$det_serie['cod_serie']] = $det_serie['nm_serie'];

      $obj_serie = new clsPmieducarSerie($det_turma['ref_ref_cod_serie_mult']);
      $det_serie = $obj_serie->detalhe();

      $this->nm_serie .= ' / ' . $det_serie['nm_serie'];

      $series[$det_serie['cod_serie']] = $det_serie['nm_serie'];
      $eh_multi_seriado = TRUE;
    }

    $this->pdf = new clsPDF('Resultado Final', 'Resultado Final', 'A4', '', FALSE, FALSE);

    $this->pdf->OpenPage();

    $this->addCabecalho();

    $this->pdf->linha_relativa(30, 140, 540, 0);
    $this->pdf->linha_relativa(30, 140, 0, 30);
    $this->pdf->linha_relativa(570, 140, 0, 30);
    $this->pdf->linha_relativa(30, 170, 540, 0);

    $this->pdf->linha_relativa(60, 140, 0, 30);
    $this->pdf->linha_relativa(320, 140, 0, 30);

    $this->pdf->linha_relativa(380, 140, 0, 30);
    $this->pdf->linha_relativa(490, 140, 0, 30);

    $this->pdf->linha_relativa(380, 155, 190, 0);
    $this->pdf->linha_relativa(530, 155, 0, 15);

    $this->pdf->linha_relativa(450, 155, 0, 15);

    $this->pdf->escreve_relativo('Ord', 35, 150, 20, 20, NULL, 10);
    $this->pdf->escreve_relativo('Nome do aluno', 70, 150, 160, 20, NULL, 10);
    $this->pdf->escreve_relativo('Aprovado', 325, 150, 160, 20, NULL, 10);
    $this->pdf->escreve_relativo('Reprovado', 410, 142, 160, 20, NULL, 10);
    $this->pdf->escreve_relativo('Desempenho', 384, 156, 160, 20, NULL, 10);
    $this->pdf->escreve_relativo('Faltas', 455, 156, 160, 20, NULL, 10);
    $this->pdf->escreve_relativo('Alf.', 500, 156, 160, 20, NULL, 10);
    $this->pdf->escreve_relativo('N. Alf.', 535, 156, 160, 20, NULL, 10);

    $obj_matricula = new clsPmieducarMatriculaTurma();
    $obj_matricula->setOrderby('m.ref_ref_cod_serie, nome_ascii');

    if ($this->is_padrao) {
      $this->semestre = NULL;
    }

    $lst_matricula = $obj_matricula->lista(NULL, $this->ref_cod_turma, NULL,
      NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie, $this->ref_cod_curso,
      $this->ref_cod_escola, $this->ref_cod_instituicao, NULL, NULL, array(1, 2, 3),
      NULL, NULL, $this->ano, NULL, TRUE, NULL, NULL,TRUE, NULL,NULL, NULL,
      $det_turma['ref_ref_cod_serie_mult'], $this->semestre);

    $qtd_quebra = 43;
    $base       = 155;
    $linha      = 1;

    $total_aprovados             = 0;
    $total_reprovados_desempenho = 0;
    $total_reprovados_nota       = 0;
    $total_analfabetos           = 0;
    $total_nao_analfabetos       = 0;
    $ordem_mostra                = 0;

    if (is_array($lst_matricula)) {
      foreach ($lst_matricula as $ordem => $matricula) {
        $obj_matricula = new clsPmieducarMatricula($matricula['ref_cod_matricula']);
        $det_matricula = $obj_matricula->detalhe();

        // Verifica se o aluno est� aprovado ou reprovado
        $situacoes = array(
          App_Model_MatriculaSituacao::APROVADO,
          App_Model_MatriculaSituacao::REPROVADO
        );

        if (in_array($det_matricula['aprovado'], $situacoes)) {
          $ordem_mostra++;
          $ordem_mostra = sprintf('%02d', $ordem_mostra);

          if ($linha % $qtd_quebra == 0) {
            //nova pagina
            $this->pdf->ClosePage();
            $this->pdf->OpenPage();

            $base  = 30;
            $linha = 0;

            $this->pdf->linha_relativa(30, 30, 540, 0);
            $qtd_quebra = 51;
          }

          $this->pdf->linha_relativa(30, $base + ($linha * 15), 0, 15);
          $this->pdf->linha_relativa(60, $base + ($linha * 15), 0, 15);
          $this->pdf->linha_relativa(30, ($base + 15) + ($linha * 15), 540, 0);
          $this->pdf->linha_relativa(570, $base + ($linha * 15), 0, 15);      // fim

          $this->pdf->escreve_relativo($ordem_mostra, 40, ($base + 3) + ($linha * 15),
            15, 15, NULL, 8);

          if ($eh_multi_seriado) {
            $this->pdf->escreve_relativo($matricula['nome'] . ' (' . $series[$det_matricula['ref_ref_cod_serie']] . ')',
              65, ($base + 3) + ($linha * 15), 250, 15, NULL, 8);
          }
          else {
            $this->pdf->escreve_relativo($matricula['nome'], 65, ($base + 3) + ($linha * 15),
              250, 15, NULL, 8);
          }

          /**
           * Instancia o service de boletim e requisita os dados da situa��o
           * do aluno. Gra�as ao service, s�o "apenas" 147 linhas de c�digo
           * mal-escrito a menos.
           */
          $boletim = new Avaliacao_Service_Boletim(array(
            'matricula' => $matricula['ref_cod_matricula']
          ));

          $situacao = $boletim->getSituacaoAluno();

          if (TRUE == $situacao->aprovado) {
            $this->pdf->escreve_relativo('X', 345, ($base + 3) + ($linha * 15),
              250, 15, NULL, 8);
            $total_aprovados++;
          }
          elseif (TRUE == $situacao->retidoFalta) {
            $total_reprovados_desempenho++;
            $this->pdf->escreve_relativo('X', 465, ($base + 3) + ($linha * 15),
              250, 15, NULL, 8);
          }
          else {
            $total_reprovados_nota++;
            $this->pdf->escreve_relativo('X', 410, ($base + 3) + ($linha * 15),
              250, 15, NULL, 8);
          }

          // analfabeto
          $obj_aluno = new clsPmieducarAluno($det_matricula['ref_cod_aluno']);
          $obj_aluno->setCamposLista('analfabeto');
          $det_aluno = $obj_aluno->detalhe();

          if ($det_aluno['analfabeto'] == 0) {
            $this->pdf->escreve_relativo('X', 507, ($base + 3) + ($linha * 15),
              250, 15, NULL, 8); // n�o alfabetizado

            $total_analfabetos++;
          }
          else {
            $this->pdf->escreve_relativo('X', 545, ($base + 3) + ($linha * 15),
              250, 15, NULL, 8); // alfabetizado

            $total_nao_analfabetos++;
          }

          $this->pdf->linha_relativa(320, $base + ($linha * 15), 0, 15);
          $this->pdf->linha_relativa(380, $base + ($linha * 15), 0, 15);
          $this->pdf->linha_relativa(490, $base + ($linha * 15), 0, 15);
          $this->pdf->linha_relativa(530, $base + ($linha * 15), 0, 15);
          $this->pdf->linha_relativa(450, $base + ($linha * 15), 0, 15);

          $linha++;
        }
      }
    }

    // Escrever total
    $this->pdf->linha_relativa(30, $base + ($linha * 15), 0, 15);

    $this->pdf->escreve_relativo("Total", 35, ($base + 3) + ($linha * 15), 20,
      15, NULL, 8);

    $this->pdf->escreve_relativo($total_aprovados, 345, ($base + 3) + ($linha * 15),
      250, 15, null, 8); // aprovado

    $this->pdf->escreve_relativo($total_reprovados_desempenho, 465,
      ($base + 3) + ($linha * 15), 250, 15, NULL, 8); // desempenho

    $this->pdf->escreve_relativo($total_reprovados_nota, 410,
      ($base + 3) + ($linha * 15), 250, 15, NULL, 8); // faltas

    $this->pdf->escreve_relativo($total_analfabetos, 507,
      ($base + 3) + ($linha * 15), 250, 15, NULL, 8); // n�o alfabetizado

    $this->pdf->escreve_relativo($total_nao_analfabetos, 545,
      ($base + 3) + ($linha * 15), 250, 15, NULL, 8); // alfabetizado

    $this->pdf->linha_relativa(60, $base + ($linha * 15), 0, 15);
    $this->pdf->linha_relativa(320, $base + ($linha * 15), 0, 15);
    $this->pdf->linha_relativa(380, $base + ($linha * 15), 0, 15);
    $this->pdf->linha_relativa(490, $base + ($linha * 15), 0, 15);
    $this->pdf->linha_relativa(530, $base + ($linha * 15), 0, 15);
    $this->pdf->linha_relativa(450, $base + ($linha * 15), 0, 15);

    $this->pdf->linha_relativa(570, $base + ($linha * 15), 0, 15);

    $this->pdf->linha_relativa(30, $base + (($linha + 1) * 15), 540, 0);

    $this->pdf->ClosePage();
    $this->pdf->CloseFile();
    $this->get_link = $this->pdf->GetLink();

    echo sprintf('
      <script>
        window.onload = function()
        {
          parent.EscondeDiv("LoadImprimir");
          window.location="download.php?filename=%s"
        }
      </script>', $this->get_link);

    echo sprintf('
      <html>
        <center>
          Se o download n�o iniciar automaticamente <br /><a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br><br>
          <span style="font-size: 10px;">Para visualizar os arquivos PDF, � necess�rio instalar o Adobe Acrobat Reader.<br>
            Clique na Imagem para Baixar o instalador<br><br>
            <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
          </span>
        </center>
      </html>', $this->get_link);
  }

  function addCabecalho()
  {
    /**
     * Vari�vel global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configura��o do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Vari�vel que controla a altura atual das caixas
    $altura   = 30;
    $fonte    = 'arial';
    $corTexto = '#000000';

    // Cabe�alho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $altura, 535, 85);
    $this->pdf->insertImageScaled('gif', $logo, 50, 95, 41);

    // T�tulo principal
    $titulo = $config->get($config->titulo, 'i-Educar');

    $this->pdf->escreve_relativo($titulo, 30, 30, 535, 80, $fonte, 18,
      $corTexto, 'center');

    $this->pdf->escreve_relativo(date("d/m/Y"), 500, 30, 100, 80, $fonte, 12,
      $corTexto, 'left');

    // Dados escola
    $this->pdf->escreve_relativo('Institui��o: ' . $this->nm_instituicao, 120,
      58, 300, 80, $fonte, 10, $corTexto, 'left');

      $this->pdf->escreve_relativo('Escola: ' . $this->nm_escola,138, 70, 300, 80,
      $fonte, 10, $corTexto, 'left');

    $this->pdf->escreve_relativo('Turma/S�rie: ' . $this->nm_turma . ' - ' . $this->nm_serie,
      112, 82, 300, 80, $fonte, 10, $corTexto, 'left');

    // T�tulo
    $this->pdf->escreve_relativo('RESULTADO FINAL I', 30, 95, 535, 80, $fonte,
      14, $corTexto, 'center');

    $this->pdf->escreve_relativo('Ano Refer�ncia: ' . $this->ano, 45, 100, 535,
      80, $fonte, 10, $corTexto, 'left');
  }

  function Editar()
  {
    return FALSE;
  }

  function Excluir()
  {
    return FALSE;
  }
}

function cmp($a, $b)
{
  return $a['modulo'] > $b['modulo'];
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �  p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();