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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor Substitui��o');
    $this->processoAp = 635;
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

  var $cod_servidor_alocacao;
  var $ref_ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_escola;
  var $ref_cod_servidor;
  var $dia_semana;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  var $todos;

  var $alocacao_array = array();
  var $professor;

  function Inicializar()
  {
    $retorno = 'Novo';
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->ref_cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 3,
      'educar_servidor_lst.php');

    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao)) {
      $retorno = 'Novo';

      $obj_servidor = new clsPmieducarServidor($this->ref_cod_servidor,
        NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_ref_cod_instituicao);
      $det_servidor = $obj_servidor->detalhe();

      // Nenhum servidor com o c�digo de servidor e institui��o
      if (!$det_servidor) {
        header('Location: educar_servidor_lst.php');
        die;
      }

      $this->professor = $obj_servidor->isProfessor() == TRUE ? 'true' : 'false';

      $obj = new clsPmieducarServidorAlocacao();
      $lista  = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL,
        NULL, NULL, $this->ref_cod_servidor, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, 1);

      if ($lista) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($lista as $campo => $val){
          $temp = array();
          $temp['carga_horaria']  = $val['carga_horaria'];
          $temp['periodo']        = $val['periodo'];
          $temp['ref_cod_escola'] = $val['ref_cod_escola'];

          $this->alocacao_array[] = $temp;
        }

        $retorno = 'Novo';
      }

      $this->carga_horaria = $det_servidor['carga_horaria'];
    }
    else {
      header('Location: educar_servidor_lst.php');
      die;
    }

    $this->url_cancelar = sprintf(
      'educar_servidor_det.php?cod_servidor=%d&ref_cod_instituicao=%d',
      $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);
    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
    $inst_det = $obj_inst->detalhe();

    $this->campoRotulo('nm_instituicao', 'Institui��o', $inst_det['nm_instituicao']);
    $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);

    $opcoes = array('' => 'Selecione');
    if (class_exists('clsPmieducarServidor')) {
      $objTemp = new clsPmieducarServidor($this->ref_cod_servidor);
      $det = $objTemp->detalhe();
      if ($det) {
        foreach ($det as $key => $registro) {
          $this->$key =  $registro;
        }
      }

      if ($this->ref_cod_servidor) {
        $objTemp = new clsFuncionario($this->ref_cod_servidor);
        $detalhe = $objTemp->detalhe();
        $detalhe = $detalhe['idpes']->detalhe();
        $nm_servidor = $detalhe['nome'];
      }
    }

    $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

    $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);
    $this->campoOculto('professor',$this->professor);

    $url = sprintf(
      'educar_pesquisa_servidor_lst.php?campo1=ref_cod_servidor_todos&campo2=ref_cod_servidor_todos_&ref_cod_instituicao=%d&ref_cod_servidor=%d&tipo=livre&professor=%d',
      $this->ref_ref_cod_instituicao, $this->ref_cod_servidor, $this->professor
    );

    $img = sprintf(
      '<img border="0" onclick="pesquisa_valores_popless(\'%s\', \'nome\')" src="imagens/lupa.png">',
      $url
    );

    $this->campoTextoInv('ref_cod_servidor_todos_', 'Substituir por:', '',
      30, 255, TRUE, FALSE, FALSE, '', $img,
      '', '', '');
    $this->campoOculto('ref_cod_servidor_todos', '');

    $this->campoOculto('alocacao_array', serialize($this->alocacao_array));
    $this->acao_enviar = 'acao2()';
  }

  function Novo()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $professor  = isset($_POST['professor']) ? strtolower($_POST['professor']) : 'FALSE';
    $substituto = isset($_POST['ref_cod_servidor_todos']) ? $_POST['ref_cod_servidor_todos'] : NULL;

    $permissoes = new clsPermissoes();
    $permissoes->permissao_cadastra(635, $this->pessoa_logada, 3,
      'educar_servidor_alocacao_lst.php');

    $this->alocacao_array = array();
    if ($_POST['alocacao_array']) {
      $this->alocacao_array = unserialize(urldecode($_POST['alocacao_array']));
    }

    if ($this->alocacao_array) {
      // Substitui todas as aloca��es
      foreach ($this->alocacao_array as $key => $alocacao) {
        $obj = new clsPmieducarServidorAlocacao(NULL, $this->ref_ref_cod_instituicao,
          $this->pessoa_logada, $this->pessoa_logada, $alocacao['ref_cod_escola'],
          $this->ref_cod_servidor, NULL, NULL, NULL, $alocacao['carga_horaria'],
          $alocacao['periodo']);

        $return = $obj->lista(NULL, $this->ref_ref_cod_instituicao, NULL, NULL,
          $alocacao['ref_cod_escola'], $this->ref_cod_servidor, NULL, NULL, NULL,
          NULL, 1, $alocacao['carga_horaria']);

        if (FALSE !== $return) {
          $substituiu = $obj->substituir_servidor($substituto);
          if (!$substituiu) {
            $this->mensagem = "Substituicao n&atilde;o realizado.<br>";

            return FALSE;
          }
        }
      }

      // Substitui��o do servidor no quadro de hor�rios (caso seja professor)
      if ('true' == $professor) {
        $quadroHorarios = new clsPmieducarQuadroHorarioHorarios(NULL, NULL, NULL,
          NULL, NULL, NULL, $this->ref_ref_cod_instituicao, NULL, $this->ref_cod_servidor,
          NULL, NULL, NULL, NULL, 1, NULL, NULL);
        $quadroHorarios->substituir_servidor($substituto);
      }
    }

    $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
    $destination = 'educar_servidor_det.php?cod_servidor=%s&ref_cod_instituicao=%s';
    $destination = sprintf($destination, $this->ref_cod_servidor, $this->ref_ref_cod_instituicao);

    header('Location: ' . $destination);
    die();
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
?>
<script type="text/javascript">
function acao2()
{
  if (document.getElementById('ref_cod_servidor_todos').value == ''){
    alert("Selecione um servidor substituto!");
    return false;
  }

  acao();
}
</script>