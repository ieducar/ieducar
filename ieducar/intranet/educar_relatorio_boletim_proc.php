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
 * @package   Ied_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsPDF.inc.php';

require_once 'RegraAvaliacao/Model/RegraDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Boletim');
    $this->processoAp         = 664;
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

  var $em_branco;
  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;

  var $pdf;
  var $pagina_atual  = 1;
  var $total_paginas = 1;

  var $page_y = 135;

  var $array_modulos = array();
  var $nm_curso;
  var $get_link = false;

  var $total;

  var $inicio_y;

  function renderHTML()
  {
    if ($_POST) {
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    // Instancia o objeto clsPDF
    $this->pdf = new clsPDF('Boletim', 'BolTit', 'A4', '');

    $this->pdf->largura  = 842.0;
    $this->pdf->altura   = 595.0;

    $this->pdf->topmargin     = 5;
    $this->pdf->bottommargirn = 5;

    $altura_linha = 13;

    // Institui��o
    $instituicao = App_Model_IedFinder::getInstituicoes();
    $instituicao = $instituicao[$this->ref_cod_instituicao];

    // Escola
    $escola = new clsPmieducarEscola();
    $escola->cod_escola = $this->ref_cod_escola;
    $escola = $escola->detalhe();
    $escola = $escola['nome'];

    // Carrega as informa��es do curso
    $serie = new clsPmieducarSerie($this->ref_ref_cod_serie, NULL, NULL,
      $this->ref_cod_curso);

    // Dados da s�rie
    $serie = $serie->detalhe();

    // Recupera a regra da s�rie
    $regraMapper = new RegraAvaliacao_Model_RegraDataMapper();
    $regra = $regraMapper->find($serie['regra_avaliacao_id']);

    // Carrega alunos matriculados
    $matriculaTurma = new clsPmieducarMatriculaTurma();
    $matriculaTurma->setOrderby('nome_aluno');

    $matriculados = $matriculaTurma->lista($this->ref_cod_matricula,
      $this->ref_cod_turma, NULL, NULL, NULL, NULL, NULL, NULL, 1,
      $this->ref_cod_serie, $this->ref_cod_curso, $this->ref_cod_escola,
      $this->ref_cod_instituicao, NULL, NULL, NULL, NULL, NULL, $this->ano,
      NULL, TRUE);

    foreach ($matriculados as $matriculado) {
      $this->pdf->OpenPage();
      $this->page_y = 10;

      $codMatricula = $matriculado['ref_cod_matricula'];
      $nomeAluno    = $matriculado['nome_aluno'];
      $turma        = $matriculado['nm_turma'];

      $boletim = new Avaliacao_Service_Boletim(array(
        'matricula' => $codMatricula
      ));

      $matriculaData = $boletim->getOption('matriculaData');
      $curso         = $matriculaData['curso_nome'];
      $serie         = $matriculaData['serie_nome'];

      $this->addCabecalho($instituicao, $escola, $codMatricula, $nomeAluno, $curso, $turma, $serie);
      $this->inicio_y = $this->page_y - 25;

      $this->_notasFaltasComponentes($boletim);

      $situacao = $boletim->getSituacaoAluno();

      $this->page_y += 25;
      $this->rodape($codMatricula, $nomeAluno, $matriculaData['aprovado']);

      $this->pdf->ClosePage();
    }

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

  protected function _notasFaltasComponentes(Avaliacao_Service_Boletim $boletim)
  {
    $etapas = $boletim->getOption('etapas');
    $notas  = $boletim->getNotasComponentes();

    if ($faltaPorComponente = ($boletim->getRegra()->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE)) {
      $faltas = $boletim->getFaltasComponentes();
    }
    else {
      $faltas = $boletim->getFaltasGerais();
    }

    $componentes = $boletim->getComponentes();
    $medias      = $boletim->getMediasComponentes();
    $situacao    = $boletim->getSituacaoAluno();

    $etapas = range(0, $etapas - 1);

    $altura_linha = 15;

    // Calcula o espa�o dispon�vel para as notas e faltas
    $extraColumns = 2 + ($situacao->recuperacao == TRUE ? 1: 0);
    $total = 782 - (80 + (60 * $extraColumns) + 120);

    $this->pdf->quadrado_relativo(30, $this->page_y, 782, $altura_linha, 0.5);
    $this->pdf->escreve_relativo_center('M�dulos', 30, $this->page_y + 2, 80, 13);
    $this->pdf->linha_relativa(30, $this->page_y, 0, $altura_linha, 0.1);
    $this->pdf->linha_relativa(80 + 30, $this->page_y, 0, $altura_linha, 0.1);

    $matriculaSituacao = App_Model_MatriculaSituacao::getInstance();

    // Escreve as etapas
    $x = 80 + 30;
    $largura   = $total / count($etapas);

    if ($faltaPorComponente) {
      $larguraDv = $largura / 2;
    }
    else {
      $larguraDv = $largura;
    }

    foreach ($etapas as $etapa) {
      $this->pdf->escreve_relativo_center($etapa + 1, $x, $this->page_y + 2, $largura, 13);
      $x += $largura;
      $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
    }

    // Escreve os campos para M�dia final, Falta e Situa��o
    $labels = array();
    $labels[] = 'M�dia final';

    if ($situacao->recuperacao) {
      $labels[] = 'Exame';
    }

    $labels[] = '% Presen�a';
    $labels[] = 'Situa��o';

    foreach ($labels as $label) {
      $largura = $label == 'Situa��o' ? 120 : 60;

      $this->pdf->escreve_relativo_center($label, $x, $this->page_y + 2, $largura, 13);
      $x += $largura;
      $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
    }

    // Escreve os r�tulos para notas e faltas
    $x = 80 + 30;

    // Nova linha
    $this->page_y += 15;

    $this->pdf->quadrado_relativo(30, $this->page_y, 782, $altura_linha, 0.5);
    $this->pdf->escreve_relativo_center('Componentes', 30, $this->page_y + 2, 80, 13);
    $this->pdf->linha_relativa(30, $this->page_y, 0, $altura_linha, 0.1);
    $this->pdf->linha_relativa(110, $this->page_y, 0, $altura_linha, 0.1);

    foreach ($etapas as $etapa) {
      $this->pdf->escreve_relativo_center('Nota', $x, $this->page_y + 2, $larguraDv, 13);
      $x += $larguraDv;
      $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);

      if ($faltaPorComponente) {
        $this->pdf->escreve_relativo_center('Falta', $x, $this->page_y + 2, $larguraDv, 13);
        $x += $larguraDv;
        $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
      }
    }

    for ($i = 0; $i < 3; $i++) {
      $x += 60;
      $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
    }

    $yNotas = $this->page_y;

    // Imprime as notas dos componentes
    foreach ($componentes as $id => $componente) {
      $this->page_y += 15;
      $this->pdf->quadrado_relativo(30, $this->page_y, 782, $altura_linha, 0.5);
      $this->pdf->escreve_relativo_center($componente, 30, $this->page_y + 2, 80, 13);
      $this->pdf->linha_relativa(110, $this->page_y, 0, $altura_linha, 0.1);

      $x = 110;

      foreach ($etapas as $etapa) {
        if (!$this->em_branco) {
          $this->pdf->escreve_relativo_center($notas[$id][$etapa]->notaArredondada, $x, $this->page_y + 2, $larguraDv, 13);
        }
        $x += $larguraDv;
        $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);

        if ($faltaPorComponente) {
          if (!$this->em_branco) {
            $this->pdf->escreve_relativo_center($faltas[$id][$etapa]->quantidade, $x, $this->page_y + 2, $larguraDv, 13);
          }
          $x += $larguraDv;
          $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
        }
      }

      if ($faltaPorComponente) {
        $porcentagemPresenca = sprintf('%.2f', $situacao->falta->componentesCurriculares[$id]->porcentagemPresenca);
      }
      else {
        $porcentagemPresenca = '-';
      }

      // M�dia, presen��o e situa��o
      $data   = array();
      $data['media'] = $medias[$id][0]->mediaArredondada;

      if ($situacao->recuperacao) {
        $notaExame  = $notas[$id][$etapa + 1];
        $data['rc'] = $notaExame->etapa == 'Rc' ? $notaExame->notaArredondada : '-';
      }

      $data['presenca'] = $porcentagemPresenca;
      $data['situacao'] = $matriculaSituacao->getValue($situacao->nota->componentesCurriculares[$id]->situacao);

      foreach ($data as $key => $value) {
        $largura = ($key == 'situacao') ? 120 : 60;

        if (!$this->em_branco) {
          $this->pdf->escreve_relativo_center($value, $x, $this->page_y + 2, $largura, 13);
        }
        $x += $largura;
        $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
      }
    }

    // Imprime a porcentagem total de presen�a e a situa��o
    $this->page_y += 15;
    $this->pdf->quadrado_relativo(30, $this->page_y, 782, $altura_linha, 0.5);
    $this->pdf->escreve_relativo_center('Faltas', 30, $this->page_y + 2, 80, 13);
    $this->pdf->linha_relativa(110, $this->page_y, 0, $altura_linha, 0.1);

    if (!$faltaPorComponente) {
      $x = 80 + 30;

      foreach ($etapas as $etapa) {
        $this->pdf->escreve_relativo_center($faltas[$etapa + 1]->quantidade, $x, $this->page_y + 2, $larguraDv, 13);
        $x += $larguraDv;
        $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
      }

      $x = $x + 60 * ($situacao->recuperacao ? 2 : 1);
    }
    else {
      $x = $x - (120 + 60);
    }

    if (!$this->em_branco) {
      $this->pdf->escreve_relativo_center(sprintf('%.2f', $situacao->falta->porcentagemPresenca),
        $x, $this->page_y + 2, 60, 13);

      $this->pdf->escreve_relativo_center($matriculaSituacao->getValue($situacao->falta->situacao),
        $x + 60, $this->page_y + 2, 120, 13);
    }

    for ($i = 0; $i < 3; $i++) {
      $this->pdf->linha_relativa($x, $this->page_y, 0, $altura_linha, 0.1);
      $x += 60;
    }
  }

  function addCabecalho($instituicao, $escola, $codMatricula, $nomeAluno, $curso, $turma, $serie)
  {
    /**
     * Vari�vel global com objetos do CoreExt.
     * @see includes/bootstrap.php
     */
    global $coreExt;

    // Namespace de configura��o do template PDF
    $config = $coreExt['Config']->app->template->pdf;

    // Vari�vel que controla a altura atual das caixas
    $altura          = 10;
    $fonte           = 'arial';
    $corTexto        = '#000000';
    $espessura_linha = 0.5;

    // Cabe�alho
    $logo = $config->get($config->logo, 'imagens/brasao.gif');

    $this->pdf->quadrado_relativo(30, $this->page_y, 782, 65, $espessura_linha);
    $this->pdf->insertImageScaled('gif', $logo, 50, $this->page_y + 52, 41);

    // T�tulo principal
    $titulo = $config->get($config->titulo, 'i-Educar');
    $this->pdf->escreve_relativo($titulo, 30,
      $this->page_y + 2, 782, 80, $fonte, 18, $corTexto, 'center');

    // Dados escola
    $this->pdf->escreve_relativo('Institui��o: ' .  $instituicao, 110,
      $this->page_y + 27, 400, 80, $fonte, 10, $corTexto, 'left');

    $this->pdf->escreve_relativo(
      'Escola: ' . $escola, 127, $this->page_y + 43, 300, 80, $fonte,
      10, $corTexto, 'left');
    $dif = 0;

    if ($this->nm_professor) {
      $this->pdf->escreve_relativo('Prof. Regente: ' . $this->nm_professor,
        111, $this->page_y + 36, 300, 80, $fonte, 7, $corTexto, 'left');
    }
    else {
      $dif = 15;
    }

    $this->pdf->quadrado_relativo(30, $this->page_y + 68, 782, 12, $espessura_linha);

    $this->pdf->quadrado_relativo(30, $this->page_y + 83, 782, 12, $espessura_linha);

    $this->pdf->escreve_relativo('Aluno: ' . $nomeAluno, 37, $this->page_y + 70,
      200, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Matricula: ' . $codMatricula,
      222, $this->page_y + 70, 300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Turma: ' . $turma, 300,
      $this->page_y + 70, 300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Curso: ' . $curso, 37, $this->page_y + 85,
      300, 80, $fonte, 7, $corTexto, 'left');

    $this->pdf->escreve_relativo('Ano/S�rie/Etapa: ' .
      ($serie ? $serie : $this->ano),
      200, $this->page_y + 85, 300, 80, $fonte, 7, $corTexto, 'left');

    // T�tulo
    $this->pdf->escreve_relativo('Boletim Escolar - ' . $this->ano, 30,
      $this->page_y + 30, 782, 80, $fonte, 12, $corTexto, 'center');

    // Data
    $this->pdf->escreve_relativo('Data de Emiss�o: ' . date('d/m/Y'), 700,
      $this->page_y + 50, 535, 80, $fonte, 8, $corTexto, 'left');

    $this->page_y += 100;
  }

  function rodape($codMatricula, $nomeAluno, $situacao)
  {
    $corTexto  = '#000000';
    $fonte     = 'arial';
    $dataAtual = date('d/m/Y');

    $this->pdf->escreve_relativo('Data: ' . $dataAtual, 36, $this->page_y + 2,
      100, 50, $fonte, 7, $corTexto, 'left');

    if (!$this->em_branco) {
      $this->pdf->escreve_relativo('Estou ciente do aproveitamento de ' .
        $nomeAluno . ', matr�cula n�: ' . $codMatricula . '.',
        68, $this->page_y + 12, 600, 50, $fonte, 9, $corTexto, 'left');

     /* if ($situacao->aprovado) {
        $situacao = App_Model_MatriculaSituacao::APROVADO;
      }
      elseif ($situacao->andamento) {
        $situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
      }
      elseif ($situacao->recuperacao) {
        $situacao = App_Model_MatriculaSituacao::EM_EXAME;
      }
      elseif ($situacao->retidoFalta) {
        $situacao = App_Model_MatriculaSituacao::RETIDO_FALTA;
      }
      else {
        $situacao = App_Model_MatriculaSituacao::EM_ANDAMENTO;
      }*/

      $situacao = strtoupper(App_Model_MatriculaSituacao::getInstance()->getValue($situacao));

      $this->pdf->escreve_relativo('Aluno ' . $situacao,
        68, $this->page_y + 62, 600, 50, $fonte, 9, $corTexto, 'center');
    }

    $this->pdf->escreve_relativo('Assinatura do Respons�vel(a)', 677,
      $this->page_y + 18, 200, 50, $fonte, 7, $corTexto, 'left');

    $this->pdf->linha_relativa(660, $this->page_y + 18, 130, 0, 0.4);
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

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �  p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();