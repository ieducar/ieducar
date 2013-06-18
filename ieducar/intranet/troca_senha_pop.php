<?php

/*
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
 */

/**
 * Pop-up de troca de senha.
 *
 * @author   Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo dispon�vel desde a vers�o 1.0.0
 * @version  $Id$
 */

$desvio_diretorio = "";
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';


class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . 'Usu&aacute;rios');
    $this->processoAp   = "0";
    $this->renderBanner = FALSE;
    $this->renderMenu   = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}

class indice extends clsCadastro
{

  public $p_cod_pessoa_fj;
  public $f_senha;
  public $f_senha2;


  public function Inicializar()
  {
    $retorno = "Novo";

    @session_start();
    $this->p_cod_pessoa_fj = @$_SESSION['id_pessoa'];
    @session_write_close();

    $objPessoa = new clsPessoaFj();

    $db = new clsBanco();
    $db->Consulta("SELECT f.senha FROM funcionario f WHERE f.ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}");

    if ($db->ProximoRegistro()) {
      list($this->f_senha) = $db->Tupla();
    }

    $this->acao_enviar = "acao2()";
    return $retorno;
  }


  public function null2empityStr( $vars )
  {
    foreach ($vars as $key => $valor) {
      $valor .= "";
      if ($valor == "NULL") {
        $vars[$key] = "";
      }
    }

    return $vars;
  }


  public function Gerar()
  {
    @session_start();
    $this->campoOculto("p_cod_pessoa_fj", $this->p_cod_pessoa_fj);
    $this->cod_pessoa_fj = $this->p_cod_pessoa_fj;

    if (empty($_SESSION['convidado'])) {
      $this->campoRotulo("", "<strong>Informa��es</strong>", "<strong>Sua senha expirar� em alguns dias, "
              . "por favor cadastre uma nova senha com no m�nimo 8 caracteres e diferente da senha anterior</strong>");
      $this->campoSenha("f_senha", "Senha", "", TRUE, "A sua nova senha dever� conter pelo menos oito caracteres");
      $this->campoSenha("f_senha2", "Redigite a Senha", $this->f_senha2, TRUE);
      echo "<script></script>";
    }
  }


  public function Novo()
  {
    @session_start();
    $pessoaFj = $_SESSION['id_pessoa'];
    @session_write_close();

    $sql = "SELECT ref_cod_pessoa_fj FROM funcionario WHERE md5('{$this->f_senha}') = senha AND "
         . "ref_cod_pessoa_fj = {$this->p_cod_pessoa_fj}";
    $db = new clsBanco();
    $senha_igual = $db->CampoUnico($sql);

    if ($this->f_senha && !$senha_igual) {
      $sql_funcionario = "UPDATE funcionario SET senha=md5('{$this->f_senha}'), "
      . "data_troca_senha = NOW(), tempo_expira_senha = 30 WHERE ref_cod_pessoa_fj={$this->p_cod_pessoa_fj}";
      $db->Consulta( $sql_funcionario );
      echo "
        <script>
          window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length-1));
          window.parent.location = 'index.php';
        </script>";

      return TRUE;
    }

    $this->mensagem .= "A sua nova senha dever� ser diferente da anterior";

    return FALSE;
  }


  public function Editar() {}
}

$pagina = new clsIndex();
$miolo  = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
?>

<script type="text/javascript">
function acao2()
{
  if ($F('f_senha').length > 7) {
    if ($F('f_senha') == $F('f_senha2')) {
      acao();
    }
    else {
      alert('As senhas devem ser iguais');
    }
  }
  else {
    alert('A sua nova senha dever� conter pelo menos oito caracteres');
  }
}
</script>