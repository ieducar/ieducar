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
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
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

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
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
    $this->SetTitulo( "{$this->_instituicao} i-Educar - Quadro de Hor&aacute;rios" );
    $this->processoAp = "641";
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_turma;
  var $ref_ref_cod_serie;
  var $ref_cod_curso;
  var $ref_cod_escola;
  var $ref_cod_instituicao;
  var $cod_quadro_horario;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastra;
  var $data_exclusao;
  var $ativo;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_turma       = $_GET['ref_cod_turma'];
    $this->ref_ref_cod_serie   = $_GET['ref_cod_serie'];
    $this->ref_cod_curso       = $_GET['ref_cod_curso'];
    $this->ref_cod_escola      = $_GET['ref_cod_escola'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];
    $this->cod_quadro_horario  = $_GET['ref_cod_quadro_horario'];

    if (is_numeric($this->cod_quadro_horario)) {
      $obj_quadro_horario = new clsPmieducarQuadroHorario($this->cod_quadro_horario);
      $det_quadro_horario = $obj_quadro_horario->detalhe();
      if ($det_quadro_horario) {
         // Passa todos os valores obtidos no registro para atributos do objeto
        foreach ($det_quadro_horario as $campo => $val) {
          $this->$campo = $val;
        }

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7)) {
          $this->fexcluir = TRUE;
        }

        $retorno = 'Editar';
      }
    }

    $obj_permissoes = new clsPermissoes();

    $obj_permissoes->permissao_cadastra(641, $this->pessoa_logada, 7,
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" );

    $this->url_cancelar = $retorno == 'Editar' ?
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" :
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}";

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    if ($this->retorno == 'Editar') {
      $this->Excluir();
    }

    // primary keys
    $this->campoOculto('cod_quadro_horario', $this->cod_quadro_horario);

    $obrigatorio            = TRUE;
    $get_escola             = TRUE;
    $get_curso              = TRUE;
    $get_escola_curso_serie = TRUE;
    $get_turma              = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    $this->url_cancelar = $retorno == 'Editar' ?
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}" :
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}";
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(641, $this->pessoa_logada, 7,
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}");

    $obj = new clsPmieducarQuadroHorario();
    $lista = $obj->lista( NULL, NULL, $this->pessoa_logada, $this->ref_cod_turma, NULL, NULL, NULL, NULL, 1 );
    if ($lista) {
      echo "<script>alert('Quadro de Hor�rio j� cadastrado para esta turma');</script>";
      return FALSE;
    }

    $obj = new clsPmieducarQuadroHorario(NULL, NULL, $this->pessoa_logada,
      $this->ref_cod_turma, NULL, NULL, 1);

    $cadastrou = $obj->cadastra();

    if ($cadastrou) {
      $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
      header("Location: educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&busca=S");
      die();
    }

    $this->mensagem = 'Cadastro n�o realizado.<br>';
    return FALSE;
  }

  function Editar()
  {
  }

  function Excluir()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7,
      "educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}");

    if (is_numeric($this->cod_quadro_horario)) {
      $obj_horarios = new clsPmieducarQuadroHorarioHorarios(
        $this->cod_quadro_horario, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, 1);

      if ($obj_horarios->excluirTodos()) {
        $obj_quadro = new clsPmieducarQuadroHorario($this->cod_quadro_horario,
          $this->pessoa_logada);

        if ($obj_quadro->excluir()) {
          $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
          header("Location: educar_quadro_horario_lst.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}");
          die();
        }
      }
    }

    $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
    return FALSE;
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
  getTurma();
}
</script>