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

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/relatorio.inc.php';

require_once 'Avaliacao/Service/Boletim.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   arapiraca-r733
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Alunos em Exame');
    $this->processoAp = 807;
    $this->renderMenu = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   arapiraca-r733
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_cod_curso;

  var $ano;

  var $get_link;

  function renderHTML()
  {
    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    if ($this->ref_ref_cod_serie) {
      $this->ref_cod_serie = $this->ref_ref_cod_serie;
    }

    $fonte    = 'arial';
    $corTexto = '#000000';

    if (!is_numeric($this->ref_cod_escola) || !is_numeric($this->ref_cod_curso) ||
        !is_numeric($this->ref_cod_serie) || !is_numeric($this->ref_cod_turma) ||
        !is_numeric($this->ano)
    ) {
      print $this->getError();
      return;
    }

    // Institui��o
    $obj_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
    $nm_instituicao  = $obj_instituicao->detalhe();
    $nm_instituicao  = $nm_instituicao['nm_instituicao'];

    // Escola
    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
    $nm_escola  = $obj_escola->detalhe();
    $nm_escola  = $nm_escola['nome'];

    // Curso
    $obj_curso = new clsPmieducarCurso($this->ref_cod_curso);
    $obj_curso->setCamposLista('media, media_exame, nm_curso');
    $det_curso = $obj_curso->detalhe();
    $nm_curso  = $det_curso['nm_curso'];

    // S�rie
    $obj_serie = new clsPmieducarSerie($this->ref_cod_serie);
    $obj_serie->setCamposLista('nm_serie');
    $det_serie = $obj_serie->detalhe();
    $nm_serie  = $det_serie['nm_serie'];
    $regraId   = $det_serie['regra_avaliacao_id'];

    // Turma
    $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
    $obj_turma->setCamposLista('nm_turma');
    $det_turma = $obj_turma->detalhe();
    $nm_turma  = $det_turma['nm_turma'];

    // Situa��o da matr�cula do aluno (aprovado)
    $situacao = $this->ano == date('Y') ?
      App_Model_MatriculaSituacao::EM_ANDAMENTO :
      implode(', ', array(
        App_Model_MatriculaSituacao::APROVADO,
        App_Model_MatriculaSituacao::REPROVADO,
        App_Model_MatriculaSituacao::EM_ANDAMENTO
      ));

    $sql = sprintf('
      SELECT
        m.cod_matricula,
        (
        SELECT
          nome
        FROM
          pmieducar.aluno al,
          cadastro.pessoa
        WHERE
          al.cod_aluno = m.ref_cod_aluno
          AND al.ref_idpes = pessoa.idpes
        ) AS nome
      FROM
        pmieducar.matricula m,
        pmieducar.matricula_turma mt
      WHERE
        mt.ref_cod_turma = %d
        AND mt.ref_cod_matricula = m.cod_matricula
        AND m.aprovado IN (%s)
        AND mt.ativo = 1
        AND m.ativo = 1
        AND m.ano = %d
      ORDER BY
        nome', $this->ref_cod_turma, $situacao, $this->ano);

    $db = new clsBanco();
    $db->Consulta($sql);

    // Mappers
    $regraMapper      = new RegraAvaliacao_Model_RegraDataMapper();
    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    $regra = $regraMapper->find($regraId);
    if (is_null($regra->formulaRecuperacao)) {
      $regra = 'A regra de avalia��o dessa s�rie n�o possui uma f�rmula de c�lculo de recupera��o.';
    }
    else {
      $regra = sprintf('Recupera��o: %s; f�rmula: %s.', $regra->formulaRecuperacao, $regra->formulaRecuperacao->formulaMedia);
    }

    if ($db->Num_Linhas()) {
      $alunos = array();

      // Instancia objeto de relat�rio padr�o
      $detalhes = sprintf('%s%s%s%s%s%s%s - Turma: %s         %s', $nm_instituicao,
        "\n", $nm_escola, "\n", $nm_curso, "\n", $nm_serie,
        $nm_turma, date('d/m/Y'));

      $relatorio = new relatorios('Rela��o de alunos em exame', 210,
        FALSE, 'Rela��o de alunos em exame', 'A4', $detalhes);

      $relatorio->exibe_produzido_por = FALSE;
      $relatorio->setMargem(20, 20, 20, 20);

      $relatorio->novalinha(array(sprintf('Nome Escola: %s    Ano: %d', $nm_escola, $this->ano)),
        0, 12, TRUE, 'arial', FALSE, '#000000', '#d3d3d3', '#FFFFFF', FALSE, TRUE);

      $relatorio->novalinha(array(sprintf('Curso: %s    Ano/S�rie: %s    Turma: %s', $nm_curso, $nm_serie, $nm_turma)),
        0, 12, TRUE, 'arial', FALSE, '#000000', '#d3d3d3', '#FFFFFF', FALSE, TRUE);

      $relatorio->novalinha(array(sprintf('%s    Data: %s', $regra, date('d/m/Y'))),
        0, 12, TRUE, 'arial', FALSE, '#000000', '#d3d3d3', '#FFFFFF', FALSE, TRUE);

      $relatorio->novalinha(array('Mat.', 'Nome Aluno', 'Componentes', 'M�dia', 'Nota necess�ria (m�n.)'),
        0, 12, TRUE, 'arial', array(30, 180, 150, 60), '#515151', '#d3d3d3', '#FFFFFF', FALSE, TRUE);

      while ($db->ProximoRegistro()) {
        list($cod_matricula, $nome_aluno) = $db->Tupla();

        $boletim = new Avaliacao_Service_Boletim(array(
          'matricula'            => $cod_matricula,
          'RegraDataMapper'      => $regraMapper,
          'ComponenteDataMapper' => $componenteMapper
        ));

        $componentes = $boletim->getComponentes();
        $medias      = $boletim->getMediasComponentes();
        $situacao    = $boletim->getSituacaoComponentesCurriculares();

        if ($situacao->situacao != App_Model_MatriculaSituacao::EM_EXAME) {
          continue;
        }

        foreach ($situacao->componentesCurriculares as $id => $situacaoComponente) {
          if ($situacaoComponente->situacao != App_Model_MatriculaSituacao::EM_EXAME) {
            continue;
          }

          $mediaRecuperacao = $boletim->preverNotaRecuperacao($id);

          if (!is_null($mediaRecuperacao)) {
            $previsao = sprintf('%s (%.2f)', $mediaRecuperacao->nome, $mediaRecuperacao->valorMinimo);
          }
          else {
            $previsao = 'Nenhuma nota poss�vel.';
          }

          $data = array(
            $cod_matricula,
            $nome_aluno,
            $componentes[$id],
            $medias[$id][0]->mediaArredondada,
            $previsao
          );

          $relatorio->novalinha($data, 0, 12, FALSE, 'arial',
            array(30, 180, 150, 60), '#515151', '#d3d3d3', '#FFFFFF', FALSE, TRUE);
        }
      }
    }
    else {
      print $this->getError();
      return;
    }

    $this->get_link = $relatorio->fechaPdf();

    echo sprintf('
      <script>
        window.onload=function()
        {
          parent.EscondeDiv("LoadImprimir");
          window.location="download.php?filename=%s"
        }
      </script>', $this->get_link);

    echo sprintf('
      <html>
        <center>
          Se o download n�o iniciar automaticamente <br>
          <a target="blank" href="%s" style="font-size: 16px; color: #000000; text-decoration: underline;">clique aqui!</a><br><br>
          <span style="font-size: 10px;">
            Para visualizar os arquivos PDF, � necess�rio instalar o Adobe Acrobat Reader.<br>
            Clique na Imagem para Baixar o instalador<br><br>
            <a href="http://www.adobe.com.br/products/acrobat/readstep2.html" target="new"><br><img src="imagens/acrobat.gif" width="88" height="31" border="0"></a>
          </span>
        </center>
      </html>', $this->get_link);
  }

  function getError()
  {
    return '
      <script>
        window.onload=function()
        {
          parent.EscondeDiv("LoadImprimir");
        }
      </script>' .
      'Nenhum aluno est� em exame';
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