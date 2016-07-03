<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Iniciar/Finalizar Ano Letivo" );
		$this->processoAp = "561";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $ref_cod_escola;
	var $tipo_acao;
	var $ano;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		/**
		 * verifica permissao para realizar operacao
		 */
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 561, $this->pessoa_logada, 7,  "educar_escola_lst.php" );
		/**
		 * Somente inicia ano por POST
		 */
		if(!$_POST)
		{
			header("location: educar_escola_lst.php");
			die;
		}

		foreach ($_POST as $key => $value)
			$this->$key = $value;

		/**
		 *  Os 3 campos devem estar preenchidos para poder realizar acao
		 */
		if(!$this->ref_cod_escola || !$this->tipo_acao || !$this->ano)
		{
			header("location: educar_escola_lst.php");
			die;
		}

		if(strtolower($this->tipo_acao) == 'editar')
		{
			/**
			 * redirediona para a pagina de edicao do ano letivo
			 */
			$referrer = @$HTTP_REFERER;
			header("location: educar_ano_letivo_modulo_cad.php?ref_cod_escola={$this->ref_cod_escola}&ano={$this->ano}&referrer=educar_escola_det.php");
			die;
		}

		/**
		 * verifica se existe ano letivo
		 */

		$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo($this->ref_cod_escola,$this->ano,null,null,null,null,null,null);
		$det_ano = $obj_ano_letivo->detalhe();

		if(!$obj_ano_letivo->detalhe())
		{
			header("location: educar_escola_lst.php");
			die;
		}

		/**
		 * verifica se ano letivo da escola nao possui nenhuma matricula
		 */

		if($this->tipo_acao == "iniciar" && $det_ano['andamento'] == 0)
			$this->iniciarAnoLetivo();
		elseif($this->tipo_acao == "finalizar"  && $det_ano['andamento'] == 1)
			$this->finalizarAnoLetivo();
		else
		{
			header("location: educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo'");
			die;
		}

		/**
		 * exibe mensagem e redireciona para detalhe da escola
		 */

		echo "<script>
				alert('Ano realizada com sucesso');
				window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
			  </script>";

		header("location: educar_escola_lst.php");
		die;
	}

	function iniciarAnoLetivo()
	{

		/**
		 * VERIFICA se nao existe ano em andamento
		 */
		$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
		$existe_ano_andamento = $obj_ano_letivo->lista($this->ref_cod_escola,null,null,null,1,null,null,null,null,1);
		if($existe_ano_andamento)
		{
			echo "<script>
					alert('N�o foi poss�vel iniciar ano letivo, j� existe ano em andamento!');
					window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
				  </script>";
			die;
		}

		/**
		 *  INICIALIZA ano letivo
		 */

		$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo($this->ref_cod_escola,$this->ano,$this->pessoa_logada,$this->pessoa_logada,1,null,null,1);

		if(! $obj_ano_letivo->edita()) {
			die("<script>
					alert('Erro ao finalizar o ano letivo!');
					window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
				  </script>");
		}

		if (! $GLOBALS['coreExt']['Config']->app->regras_negocio->desativar_rematricula_automatica) {
			$this->rematricularAlunosAprovados();
			$this->rematricularAlunosReprovados();
		}

		die("<script>
				alert('Ano letivo inicializado com sucesso!');
				window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
			  </script>");
	}

	function finalizarAnoLetivo()
	{
		/**
		 * VERIFICA se n�o existem matriculas em andamento
		 */

		$obj_matriculas = new clsPmieducarMatricula();
		$existe_matricula_andamento_com_curso = $obj_matriculas->lista(null,null,$this->ref_cod_escola,null,null,null,null,3,null,null,null,null,1,$this->ano,null,null,1,null,null,null,null,null,null,null,null,false);

		if($existe_matricula_andamento_com_curso)
		{
			echo "<script>
					alert('N�o foi poss�vel finalizar o ano letivo existem matr�culas em andamento!');
					window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
				  </script>";
		}


		$obj_matriculas = new clsPmieducarMatricula(null,null,$this->ref_cod_escola,null,$this->pessoa_logada,null,null,null,null,null,1,$this->ano);
		$existe_matricula_andamento = $obj_matriculas->lista(null,null,$this->ref_cod_escola,null,null,null,null,3,null,null,null,null,1,$this->ano,null,null,1,null,null,null,null,null,null,null,null,true);
		if($existe_matricula_andamento)
		{
			$editou = $obj_matriculas->aprova_matricula_andamento_curso_sem_avaliacao();
			if(!editou)
			{
				echo "<script>
						alert('N�o foi poss�vel finalizar o ano letivo.\\nErro ao editar matriculas de curso sem avalia��o!');
						window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}';
					  </script>";
			}
		}

		/**
		 *  FINALIZA ano letivo
		 */

		$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo($this->ref_cod_escola,$this->ano,$this->pessoa_logada,$this->pessoa_logada,2,null,null,1);
		if(!$obj_ano_letivo->edita())
		{
			echo "<script>
					alert('Erro ao finalizar o ano letivo!');
					window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
				  </script>";
		}else
		{
			echo "<script>
					alert('Ano letivo finalizado com sucesso!');
					window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
				  </script>";
		}
	}

	function rematricularAlunosAprovados() {
		$obj_matricula = new clsPmieducarMatricula();
		$lst_matricula = $obj_matricula->lista( null,null,$this->ref_cod_escola,null,null,null,null,1,null,null,null,null,1,$this->ano-1,null,null,1 );

		if (! is_array($lst_matricula))
			return;

		foreach ($lst_matricula AS $key => $matricula) {
			$obj_sequencia = new clsPmieducarSequenciaSerie();
			$lst_sequencia = $obj_sequencia->lista( $matricula['ref_ref_cod_serie'],null,null,null,null,null,null,null,1 );

			if ( is_array($lst_sequencia) && (count($lst_sequencia) == 1) ) {
				$det_sequencia = array_shift($lst_sequencia);
				$serie_destino = $det_sequencia["ref_serie_destino"];

				$obj_serie = new clsPmieducarSerie( $serie_destino );
				$det_serie = $obj_serie->detalhe();

				$obj_escola_curso = new clsPmieducarEscolaCurso($this->ref_cod_escola, $det_serie["ref_cod_curso"]);

				if (is_array($obj_escola_curso->detalhe())) {
					$obj = new clsPmieducarMatricula( $matricula['cod_matricula'],null,null,null,$this->pessoa_logada,null,null,null,null,null,1,null,0 );
					$editou = $obj->edita();
					if( $editou ) {
						$obj = new clsPmieducarMatricula( null,null,$this->ref_cod_escola,$serie_destino,null,$this->pessoa_logada,$matricula['ref_cod_aluno'],3,null,null,1,$this->ano,1,null,null,null,null,$det_serie["ref_cod_curso"] );
						$cadastra = $obj->cadastra();
						if( !$cadastra ) {
							echo "<script>
									alert('Erro ao matricular os alunos da Escola!');
									window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
								  </script>";
						}
					}
				}
			}
		}
	}

	function rematricularAlunosReprovados() {
		$obj_matricula = new clsPmieducarMatricula();
		$lst_matricula = $obj_matricula->lista( null,null,$this->ref_cod_escola,null,null,null,null,2,null,null,null,null,1,$this->ano-1,null,null,1 );

		if (! is_array($lst_matricula) )
			return;

		foreach ($lst_matricula AS $key => $matricula) {
			$obj_serie = new clsPmieducarSerie( $matricula['ref_ref_cod_serie'] );
			$det_serie = $obj_serie->detalhe();

			$obj = new clsPmieducarMatricula( $matricula['cod_matricula'],null,null,null,$this->pessoa_logada,null,null,null,null,null,1,null,0 );
			$editou1 = $obj->edita();
			if( $editou1 ) {
				$obj = new clsPmieducarMatricula( null,null,$this->ref_cod_escola,$matricula['ref_ref_cod_serie'],null,$this->pessoa_logada,$matricula['ref_cod_aluno'],3,null,null,1,$this->ano,1,null,null,null,null,$det_serie["ref_cod_curso"] );
				$cadastra1 = $obj->cadastra();
				if( !$cadastra1 ) {
					echo "<script>
							alert('Erro ao matricular os alunos da Escola!');
							window.location = 'educar_escola_det.php?cod_escola={$this->ref_cod_escola}#ano_letivo';
						  </script>";
				}
			}
		}
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>