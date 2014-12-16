<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author 								 							 *
	*	@updated 													 		 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 														 *
	*																    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matrícula Turma - Alunos" );
		$this->processoAp = "586"; //TODO verificar o que é este processoAp
		$this->addEstilo( "localizacaoSistema" );
	}
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
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
  
  var $ano;
  var $nm_prof_regente;

  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ref_cod_turma = $_GET['ref_cod_turma'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(586, $this->pessoa_logada, 7,
      'educar_matriculas_turma_lst.php'); //TODO processoAp aparece aqui também

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
      }
      
      $ano = $registro["ano"];
      
      if ($registro['ref_cod_regente']) {
      	$obj_pessoa = new clsPessoa_($registro['ref_cod_regente']);
      	$det = $obj_pessoa->detalhe();
      	if ($det["nome"])
      		$this->nm_prof_regente = $det["nome"];
      }

      //Configuracao do form e botoes
      $this->url_cancelar = 'educar_turma_lst.php';
      $this->nome_url_cancelar = 'Voltar';
      $this->nome_url_sucesso = 'Emitir Relatório';
      $this->action = '../module/Reports/AlunosTurma';
      $this->target = '_blank';
      $this->onSubmit = 'true';
      $this->acao_enviar = 'document.formcadastro.submit()';
      $this->titulo_aplication = 'Lista de Alunos por Turma';
            
      return $retorno;
    }

    header('Location: educar_turma_lst.php');
    die;
  }

  function Gerar()
  {

    if ($_POST) {
      foreach ($_POST as $campo => $val) {
        $this->$campo = $this->$campo ? $this->$campo : $val;
      }
    }

	$this->campoOculto("ref_cod_turma", $this->ref_cod_turma);
    
    $obj_cod_instituicao = new clsPmieducarInstituicao($this->ref_cod_instituicao);
    $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
    $nm_instituicao = $obj_cod_instituicao_det['nm_instituicao'];
    $this->campoRotulo('nm_instituicao', 'Institui&ccedil;&atilde;o', $nm_instituicao);

    if ($this->ref_ref_cod_escola) {
      $obj_ref_cod_escola = new clsPmieducarEscola($this->ref_ref_cod_escola);
      $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
      $nm_escola = $det_ref_cod_escola['nome'];
      $this->campoRotulo('nm_escola', 'Escola', $nm_escola);
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

      // busca o ano da turma
      $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
      $lst_ano_letivo = $obj_ano_letivo->lista($this->ref_ref_cod_escola, NULL,
        NULL, NULL, 1, NULL, NULL, NULL, NULL, 1);

      if (is_array($lst_ano_letivo)) {
        $det_ano_letivo = array_shift($lst_ano_letivo);
        $ano_letivo = $det_ano_letivo['ano'];
      }
      else {
        $this->mensagem = 'Não foi possível encontrar o ano letivo em andamento da escola.';
        return FALSE;
      }
    }

    if ($this->ref_cod_turma) {
      $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
      $det_turma = $obj_turma->detalhe();
      $nm_turma = $det_turma['nm_turma'];
      $this->campoRotulo('nm_turma', 'Turma', $nm_turma);
    }
    
    if ($this->ano)
    	$this->campoRotulo('ano', 'Ano', $this->ano);
    else
    	$this->campoRotulo('ano', 'Ano', 'Não foi possível encontrar o ano referente a esta turma.');
    	
    if ($this->nm_prof_regente)
    	$this->campoRotulo('nm_prof_regente', 'Prof. Regente', $this->nm_prof_regente);
    else
    	$this->campoRotulo('nm_prof_regente', 'Prof. Regente', 'Esta turma não possui Professor Regente');
    
    // Inclui os alunos
    $this->campoQuebra();

    if (is_numeric($this->ref_cod_turma) && !$_POST) {
      $obj_matriculas_turma = new clsPmieducarMatriculaTurma();
      $obj_matriculas_turma->setOrderby('nome_aluno');
      $lst_matriculas_turma = $obj_matriculas_turma->lista(NULL, $this->ref_cod_turma,
         NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
         array(1, 2, 3), NULL, NULL, NULL, NULL, TRUE, NULL, NULL, TRUE);

      if (is_array($lst_matriculas_turma)) {
        foreach ($lst_matriculas_turma as $key => $campo) {
          $this->matriculas_turma[$campo['ref_cod_matricula']]['sequencial_'] = $campo['sequencial'];
        }
      }
      else {
      	$this->campoRotulo('rotulo_1', '-', 'Não há nenhum aluno matriculado nesta turma.');
      	$this->acao_enviar = false;
      }
    }

    if ($this->matriculas_turma) {
      foreach ($this->matriculas_turma as $matricula => $campo) {
        $obj_matricula = new clsPmieducarMatricula($matricula);
        $det_matricula = $obj_matricula->detalhe();

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($det_matricula['ref_cod_aluno']);
        $det_aluno = array_shift($lst_aluno);
        $nm_aluno = $det_aluno['nome_aluno'];

        $this->campoTextoInv('ref_cod_matricula_' . $matricula, '', $nm_aluno,
          30, 255, FALSE, FALSE, FALSE, '', '', '', '', 'ref_cod_matricula');
      }
    }

    $this->campoQuebra();
  }

  function Novo()
  {
  }

  function Editar()
  {
  }

  function Excluir()
  {
  }
  
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();