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
require_once 'lib/Portabilis/Date/Utils.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Matriculas Turma');
    $this->processoAp = 659;
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

  var $ref_cod_matricula;

  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $sequencial;

  var $ref_cod_instituicao;
  var $ref_ref_cod_escola;
  var $ref_cod_curso;
  var $ref_ref_cod_serie;
  var $ref_cod_turma;

  var $matriculas_turma;
  var $incluir_matricula;
  var $data_enturmacao;

  var $check_desenturma;

  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_turma = $_GET['ref_cod_turma'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(659, $this->pessoa_logada, 7,
      'educar_matriculas_turma_lst.php');

    if (is_numeric($this->ref_cod_turma)) {
      $obj_turma = new clsPmieducarTurma();
      $lst_turma = $obj_turma->lista($this->ref_cod_turma);

      if (is_array($lst_turma)) {
        $registro = array_shift($lst_turma);
      }

      if ($registro) {
        // passa todos os valores obtidos no registro para atributos do objeto
        foreach ($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $retorno = 'Editar';
      }

      $this->url_cancelar = $retorno == 'Editar' ?
        sprintf('educar_matriculas_turma_det.php?ref_cod_matricula=%d&ref_cod_turma=%d', $this->ref_cod_matricula, $this->ref_cod_turma) :
        'educar_matriculas_turma_lst.php';

      $this->nome_url_cancelar = 'Cancelar';
      return $retorno;
    }

    header('Location: educar_matriculas_turma_lst.php');
    die;
  }

  function Gerar()
  {
    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        $this->$campo = $this->$campo ? $this->$campo : $val;
      }
    }

    $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);
    $this->campoOculto('ref_ref_cod_escola', $this->ref_ref_cod_escola);
    $this->campoOculto('ref_ref_cod_serie', $this->ref_ref_cod_serie);
    $this->campoOculto('ref_cod_curso', $this->ref_cod_curso);

    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      $obj_cod_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
      $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
      $nm_instituicao = $obj_cod_instituicao_det['nm_instituicao'];
      $this->campoRotulo('nm_instituicao', 'Institui&ccedil;&atilde;o', $nm_instituicao);
    }

    if ($nivel_usuario == 1 || $nivel_usuario == 2) {
      if ($this->ref_ref_cod_escola) {
        $obj_ref_cod_escola = new clsPmieducarEscola($this->ref_ref_cod_escola);
        $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
        $nm_escola = $det_ref_cod_escola['nome'];
        $this->campoRotulo('nm_escola', 'Escola', $nm_escola);
      }
    }

    if ($this->ref_cod_curso) {
      $obj_ref_cod_curso = new clsPmieducarCurso($this->ref_cod_curso);
      $det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
      $nm_curso = $det_ref_cod_curso['nm_curso'];
      $this->campoRotulo('nm_curso', 'Curso', $nm_curso);
    }

    if ($this->ref_ref_cod_serie) {
      $obj_ref_cod_serie = new clsPmieducarSerie($this->ref_ref_cod_serie);
      $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
      $nm_serie = $det_ref_cod_serie["nm_serie"];
      $this->campoRotulo('nm_serie', 'S&eacute;rie', $nm_serie);

      // busca o ano em q a escola esta em andamento
      $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
      $lst_ano_letivo = $obj_ano_letivo->lista($this->ref_ref_cod_escola, NULL,
        NULL, NULL, 1, NULL, NULL, NULL, NULL, 1);

      if (is_array($lst_ano_letivo)) {
        $det_ano_letivo = array_shift($lst_ano_letivo);
        $ano_letivo = $det_ano_letivo['ano'];
      }
      else {
        $this->mensagem = 'N�o foi poss�vel encontrar o ano letivo em andamento da escola.';
        return FALSE;
      }
    }

    if ($this->ref_cod_turma) {
      $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
      $det_turma = $obj_turma->detalhe();
      $nm_turma = $det_turma['nm_turma'];
      $this->campoRotulo('nm_turma', 'Turma', $nm_turma);
    }

    // Inlui o aluno
    $this->campoQuebra();

    if ($_POST['matriculas_turma']) {
      $this->matriculas_turma = unserialize(urldecode($_POST['matriculas_turma']));
    }

    $alunosEnturmados = false;

    if (is_numeric($this->ref_cod_turma) && !$_POST) {
      $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
      $obj_matriculas_turma->setOrderby('sequencial_fechamento, nome_aluno');
      $lst_matriculas_turma = $obj_matriculas_turma->lista(NULL, $this->ref_cod_turma,
         NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL,
         array(1, 2, 3), NULL, NULL, $ano_letivo, NULL, TRUE, NULL, 1, TRUE);

      if (is_array($lst_matriculas_turma)) {
        $alunosEnturmados = true;
        foreach ($lst_matriculas_turma as $key => $campo) {
          $this->matriculas_turma[$campo['ref_cod_matricula']]['sequencial_'] = $campo['sequencial'];
        }
      }
    }

    if ($_POST['ref_cod_matricula']) {
      $obj_matriculas_turma = new clsPmieducarMatriculaTurma($_POST['ref_cod_matricula'],
        $this->ref_cod_turma);

      $sequencial = $obj_matriculas_turma->buscaSequencialMax();

      $this->matriculas_turma[$_POST['ref_cod_matricula']]['sequencial_'] = $sequencial;
      unset($this->ref_cod_matricula);
    }

    if ($this->matriculas_turma) {
      $this->campoRotulo('titulo', 'Matr&iacute;culas', "<b>&nbsp;Alunos matriculados&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Marque alunos para desenturmar</b><label style='display: block; width: 350px; margin-left: 196px;'><input type='checkbox' name='CheckTodos' onClick='marcarCheck(".'"check_desenturma[]"'.");'/>Marcar Todos</label>");
      foreach ($this->matriculas_turma as $matricula => $campo) {
        $obj_matricula = new clsPmieducarMatricula($matricula);
        $det_matricula = $obj_matricula->detalhe();

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($det_matricula['ref_cod_aluno']);
        $det_aluno = array_shift($lst_aluno);
        $nm_aluno = $det_aluno['nome_aluno'];

        $this->campoTextoInv('ref_cod_matricula_' . $matricula, '', $nm_aluno,
          30, 255, FALSE, FALSE, TRUE, '', '', '', '', 'ref_cod_matricula');

        $this->campoCheck('check_desenturma['.$matricula.']','',$matricula);
      }
    }

    $this->campoOculto('matriculas_turma', serialize($this->matriculas_turma));

    // Aluno
    $opcoes = array();
    $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
    $alunos = $obj_matriculas_turma->alunosNaoEnturmados($this->ref_ref_cod_escola,
      $this->ref_ref_cod_serie, $this->ref_cod_curso, $ano_letivo);

    if (is_array($alunos)) {
      for ($i = 0; $i < count($alunos); $i++) {
        $obj_matricula = new clsPmieducarMatricula( $alunos[$i] );
        $det_matricula = $obj_matricula->detalhe();

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($det_matricula['ref_cod_aluno']);
        $det_aluno = array_shift($lst_aluno);

        $opcoes[$alunos[$i]] = $det_aluno['nome_aluno'];
      }
    }

    if (count($opcoes)) {
      $this->inputsHelper()->date('data_enturmacao', array('label' => 'Data da enturma��o', 'value' => date('Y-m-d')));
      asort($opcoes);
      foreach ($opcoes as $key => $aluno) {
        $this->campoCheck('ref_cod_matricula[' . $key . ']', 'Aluno', $key,
          $aluno, NULL, NULL, NULL);
      }
    }
    else if($alunosEnturmados){
      $this->campoRotulo('rotulo_1', '-', 'Todos os alunos matriculados na s�rie j� se encontram enturmados.');
    }else {      
      $this->campoRotulo('rotulo_1', '-', 'N�o h� alunos enturmados.');
    }

    $this->campoQuebra();
  }

  function Novo()
  {
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    $this->data_enturmacao = Portabilis_Date_Utils::brToPgSQL($this->data_enturmacao);
    @session_write_close();

    // realiza desenturma��es
    foreach ($this->check_desenturma as $matricula) {
      $this->removerEnturmacao($matricula,$this->ref_cod_turma);
    }    

    if ($this->matriculas_turma) {
      foreach ($this->ref_cod_matricula as $matricula => $campo) {
        $obj = new clsPmieducarMatriculaTurma($matricula, $this->ref_cod_turma,
          NULL, $this->pessoa_logada, NULL, NULL, 1, NULL, $campo['sequencial_']);

        $existe = $obj->existe();

        if (!$existe) {
          $obj->data_enturmacao = $this->data_enturmacao;
          $cadastrou = $obj->cadastra();

          if (!$cadastrou) {
            $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
            return FALSE;
          }
        }
      }

      $this->mensagem .= 'Cadastro efetuada com sucesso.<br>';
      header('Location: educar_matriculas_turma_lst.php');
      die();
    }

    header('Location: educar_matriculas_turma_lst.php');
    die();
  }

  function Excluir()
  {
  }

  function removerEnturmacao($matriculaId, $turmaId) {
    $sequencialEnturmacao = $this->getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId);
    $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                 $turmaId,
                                                 $this->pessoa_logada, 
                                                 NULL, 
                                                 NULL,
                                                 NULL, 
                                                 0,
                                                 NULL,
                                                 $sequencialEnturmacao);

    return $enturmacao->edita();
  }


  function getSequencialEnturmacaoByTurmaId($matriculaId, $turmaId) {
    $db = new clsBanco();
    $sql = 'select coalesce(max(sequencial), 1) from pmieducar.matricula_turma where ativo = 1 and ref_cod_matricula = $1 and ref_cod_turma = $2';

    if ($db->execPreparedQuery($sql, array($matriculaId, $turmaId)) != false) {
      $db->ProximoRegistro();
      $sequencial = $db->Tupla();
      return $sequencial[0];
    }
    return 1;
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

  function fixUpCheckBoxes(){
    $j('input[name^=check_desenturma]').each(function(index, element){
      element.id = 'check_desenturma[]';
      element.checked = false;
    });
  }

  fixUpCheckBoxes();

  function marcarCheck(idValue) {
      // testar com formcadastro
      var contaForm = document.formcadastro.elements.length;
      var campo = document.formcadastro;
      var i;
        for (i=0; i<contaForm; i++) {
            if (campo.elements[i].id == idValue) {

                campo.elements[i].checked = campo.CheckTodos.checked;
            }
        }
  }
</script>
