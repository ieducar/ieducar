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
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matricula Turma');
    $this->processoAp = 578;
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
class indice extends clsDetalhe
{
  var $titulo;

  var $ref_cod_matricula;
  var $ref_cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  var $ref_cod_serie;
  var $ref_cod_escola;
  var $ref_cod_turma_origem;
  var $ref_cod_curso;
  var $data_enturmacao;

  var $sequencial;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Matricula Turma - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    foreach ($_POST as $key =>$value) {
      $this->$key = $value;
    }

    if (! $this->ref_cod_matricula) {
      header('Location: educar_matricula_lst.php');
      die();
    }

    $obj_mat_turma = new clsPmieducarMatriculaTurma();
    $det_mat_turma = $obj_mat_turma->lista($this->ref_cod_matricula, NULL, NULL,
      NULL, NULL, NULL, NULL, NULL, 1);

    if ($det_mat_turma){
      $det_mat_turma  = array_shift($det_mat_turma);
      $obj_turma      = new clsPmieducarTurma($det_mat_turma['ref_cod_turma']);
      $det_turma      = $obj_turma->detalhe();
      $this->nm_turma = $det_turma['nm_turma'];

      $this->ref_cod_turma_origem = $det_turma['cod_turma'];
      $this->sequencial = $det_mat_turma['sequencial'];
    }

    // #TODO adicionar ano da matricula atual
    #$tmp_obj = new clsPmieducarMatriculaTurma( );
    #$lista = $tmp_obj->lista(NULL, $this->ref_cod_turma, NULL, NULL, NULL, NULL,
    #  NULL, NULL, 1);

    #$total_alunos = 0;
    #if ($lista) {
    #  $total_alunos = count($lista);
    #}

    $tmp_obj  = new clsPmieducarTurma();
    $lst_obj  = $tmp_obj->lista($this->ref_cod_turma);
    $registro = array_shift($lst_obj);

    $db = new clsBanco();

    $ano = $db->CampoUnico("select ano from pmieducar.matricula where cod_matricula = $this->ref_cod_matricula");
    $sql = "select count(cod_matricula) as qtd_matriculas from pmieducar.matricula, pmieducar.matricula_turma, pmieducar.aluno where aluno.cod_aluno = matricula.ref_cod_aluno and ano = {$ano} and aluno.ativo = 1 and matricula.ativo = 1 and matricula_turma.ativo = matricula.ativo and cod_matricula = ref_cod_matricula and ref_cod_turma = $this->ref_cod_turma";

    $total_alunos = $db->CampoUnico($sql);

    $this->ref_cod_curso = $registro['ref_cod_curso'];

    if (!$registro || !$_POST) {
      header('Location: educar_matricula_lst.php');
      die();
    }

    // Tipo da turma
    $obj_ref_cod_turma_tipo = new clsPmieducarTurmaTipo($registro['ref_cod_turma_tipo']);
    $det_ref_cod_turma_tipo = $obj_ref_cod_turma_tipo->detalhe();
    $registro['ref_cod_turma_tipo'] = $det_ref_cod_turma_tipo['nm_tipo'];

    // C�digo da institui��o
    $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

    // Nome da escola
    $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_ref_cod_escola']);
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $registro['ref_ref_cod_escola'] = $det_ref_cod_escola['nome'];

    // Nome do curso
    $obj_ref_cod_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
    $registro['ref_cod_curso'] = $det_ref_cod_curso['nm_curso'];
    $padrao_ano_escolar = $det_ref_cod_curso['padrao_ano_escolar'];

    // Nome da s�rie
    $obj_ser = new clsPmieducarSerie($registro['ref_ref_cod_serie']);
    $det_ser = $obj_ser->detalhe();
    $registro['ref_ref_cod_serie'] = $det_ser['nm_serie'];

    // Matr�cula
    $obj_ref_cod_matricula = new clsPmieducarMatricula();
    $detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));

    $obj_aluno = new clsPmieducarAluno();
    $det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1));

    $obj_escola = new clsPmieducarEscola($this->ref_cod_escola, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, 1);
    $det_escola = $obj_escola->detalhe();

    $this->addDetalhe(array('Nome do Aluno', $det_aluno['nome_aluno']));

    $objTemp = new clsPmieducarTurma($this->ref_cod_turma);
    $det_turma = $objTemp->detalhe();

    if ($registro['ref_ref_cod_escola']) {
      $this->addDetalhe(array('Escola', $registro['ref_ref_cod_escola']));
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($registro['ref_ref_cod_serie']) {
      $this->addDetalhe(array('S&eacute;rie', $registro['ref_ref_cod_serie']));
    }

    //(enturma��es) turma atual
    $enturmacoes = new clsPmieducarMatriculaTurma();
    $enturmacoes = $enturmacoes->lista($this->ref_cod_matricula, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

    $this->possuiEnturmacao = ! empty($enturmacoes);
    $this->possuiEnturmacaoTurmaDestino = false;
    $this->turmaOrigemMesmaDestino = false;

    $this->addDetalhe(array('<b>Turma selecionada</b>' , '<b>' . $registro['nm_turma'] . '</b>'));

    $this->addDetalhe(array('Total de vagas', $registro['max_aluno']));

    if (is_numeric($total_alunos)) {
      $this->addDetalhe(array('Alunos enturmados', $total_alunos));
      $this->addDetalhe(array('Vagas dispon�veis', $registro['max_aluno'] - $total_alunos));
    }

    if ($this->possuiEnturmacao) {
      //se possui uma enturmacao mostra o nome, se mais de uma mostra select para selecionar
      if (count($enturmacoes) > 1) {
        $selectEnturmacoes = "<select id='ref_cod_turma_origem' class='obrigatorio'>";
        $selectEnturmacoes .= "<option value=''>Selecione</option>";

        foreach ($enturmacoes as $enturmacao) {
          if($enturmacao['ref_cod_turma'] != $this->ref_cod_turma)
            $selectEnturmacoes .= "<option value='{$enturmacao['ref_cod_turma']}'>{$enturmacao['nm_turma']}</option>";
          elseif (! $this->possuiEnturmacaoTurmaDestino)
            $this->possuiEnturmacaoTurmaDestino = true;
        }
        $selectEnturmacoes .= "</select>";
      }
      else {
        if ($enturmacoes[0]['ref_cod_turma'] == $this->ref_cod_turma) {
          $this->possuiEnturmacaoTurmaDestino = true;
          $this->turmaOrigemMesmaDestino = true;
        }

        $selectEnturmacoes = "<input id='ref_cod_turma_origem' type='hidden' value = '{$enturmacoes[0]['ref_cod_turma']}'/>{$enturmacoes[0]['nm_turma']}";
      }

      $this->addDetalhe(array('<b>Enturma��o</b>', $selectEnturmacoes));
    }

    if(!$this->possuiEnturmacaoTurmaDestino)
      $this->addDetalhe(array('Data da enturma��o', '<input onkeypress="formataData(this,event);" value="'.date('d/m/Y').'" class="geral" type="text" name="data_enturmacao" id="data_enturmacao" size="9" maxlength="10"/>'));    

    $this->addDetalhe(array(
      '-',
      sprintf('
        <form name="formcadastro" method="post" action="educar_matricula_turma_cad.php">
          <input type="hidden" name="ref_cod_matricula" value="">
          <input type="hidden" name="ref_cod_serie" value="">
          <input type="hidden" name="ref_cod_escola" value="">
          <input type="hidden" name="ref_cod_turma_origem" value="%d">
          <input type="hidden" name="ref_cod_turma_destino" value="">
          <input type="hidden" name="data_enturmacao" value="">
          <input type="hidden" name="sequencial" value="%d">
        </form>
      ', $this->ref_cod_turma_origem, $this->sequencial)
    ));

    if ($registro['max_aluno'] - $total_alunos <= 0) {

      $escolaSerie = $this->getEscolaSerie($det_ref_cod_escola['cod_escola'], $det_ser['cod_serie']);

      if($escolaSerie['bloquear_enturmacao_sem_vagas'] != 1) {
        $msg = sprintf('Aten��o! Turma sem vagas! Deseja continuar com a enturma��o mesmo assim?');
        $jsEnturmacao = sprintf('if (!confirm("%s")) return false;', $msg);
      }
      else {
        $msg = sprintf('Enturma��o n�o pode ser realizada,\n\no limite de vagas da turma j� foi atingido e para esta s�rie e escola foi definido bloqueio de enturma��o ap�s atingir tal limite.');
        $jsEnturmacao = sprintf('alert("%s"); return false;', $msg);
      }
    }
    else
      $jsEnturmacao = 'if (!confirm("Confirma a enturma��o?")) return false;';

    $script = sprintf('
      <script type="text/javascript">

        function enturmar(ref_cod_matricula, ref_cod_turma_destino, tipo){
          document.formcadastro.ref_cod_turma_origem.value = "";

          if(tipo == "transferir") {
            var turmaOrigemId = document.getElementById("ref_cod_turma_origem");
            if (turmaOrigemId && turmaOrigemId.value)
              document.formcadastro.ref_cod_turma_origem.value = turmaOrigemId.value;
            else {
              alert("Por favor, selecione a enturma��o a ser transferida.");
              return false;
            }
          }

          %s

          document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
          document.formcadastro.ref_cod_turma_destino.value = ref_cod_turma_destino;
          document.formcadastro.data_enturmacao.value = document.getElementById("data_enturmacao").value;
          document.formcadastro.submit();
        }

        function removerEnturmacao(ref_cod_matricula, ref_cod_turma_destino) {

          if (! confirm("Confirma remo��o da enturma��o?"))
            return false;

          document.formcadastro.ref_cod_turma_origem.value = "remover-enturmacao-destino";
          document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
          document.formcadastro.ref_cod_turma_destino.value = ref_cod_turma_destino;
          document.formcadastro.submit();
        }

      </script>', $jsEnturmacao);

    print $script;

    $canCreate = new clsPermissoes();
    $canCreate = $canCreate->permissao_cadastra(578, $this->pessoa_logada, 7);

    if ($this->possuiEnturmacaoTurmaDestino && $canCreate){
      $this->array_botao            = array('Remover (enturma��o) da turma selecionada');
      $this->array_botao_url_script = array("removerEnturmacao({$this->ref_cod_matricula}, {$this->ref_cod_turma})");
    }

    if (! $this->turmaOrigemMesmaDestino && $canCreate) {
      //mover enturma��o
      if ($this->possuiEnturmacao) {
        $this->array_botao[]            = 'Transferir para turma selecionada';
        $this->array_botao_url_script[] = "enturmar({$this->ref_cod_matricula}, {$this->ref_cod_turma}, \"transferir\")";
      }

      //nova enturma��o
      if (! $this->possuiEnturmacaoTurmaDestino && $canCreate) {
        $this->array_botao[]            = 'Enturmar na turma selecionada';
        $this->array_botao_url_script[] = "enturmar({$this->ref_cod_matricula}, {$this->ref_cod_turma}, \"nova\")";
      }
    }

    $this->array_botao[] = 'Voltar';
    $this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$this->ref_cod_matricula}\");";

    $this->largura = '100%';
  }

  protected function getEscolaSerie($escolaId, $serieId) {
    $escolaSerie = new clsPmieducarEscolaSerie();
    $escolaSerie->ref_cod_escola = $escolaId;
    $escolaSerie->ref_cod_serie  = $serieId;

    return $escolaSerie->detalhe();
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
