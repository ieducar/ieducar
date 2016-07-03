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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matr&iacute;cula" );
		$this->processoAp = "578";
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	var $cod_matricula;
	var $ref_cod_reserva_vaga;
	var $ref_ref_cod_escola;
	var $ref_ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_aluno;
	var $aprovado;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ano;
	var $ultima_matricula;

	var $ref_cod_instituicao;
	var $ref_cod_curso;
	var $ref_cod_escola;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Matr&iacute;cula - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		if ( !$this->ref_cod_aluno )
		{
			header( "location: educar_aluno_lst.php" );
			die();
		}
		$this->campoOculto("ref_cod_aluno",$this->ref_cod_aluno);

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
      "Ano",
      "Matr�cula",
      "Situa��o",
      "Turma",
			"S&eacute;rie",
			"Curso"
		);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			$lista_busca[] = "Escola";
			$lista_busca[] = "Institui&ccedil;&atilde;o";
		}
		else if ($nivel_usuario == 2)
		{
			$lista_busca[] = "Escola";
		}
		$this->addCabecalhos($lista_busca);

		$get_escola = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		include("include/pmieducar/educar_campo_lista.php");

		if ( $this->ref_cod_escola )
		{
			$this->ref_ref_cod_escola = $this->ref_cod_escola;
		}

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_matricula = new clsPmieducarMatricula();
		$obj_matricula->setOrderby('ano DESC, ref_ref_cod_serie DESC, aprovado, cod_matricula');
		$obj_matricula->setLimite( $this->limite, $this->offset );

		$lista = $obj_matricula->lista(
			$this->cod_matricula,
			null,
			$this->ref_ref_cod_escola,
			$this->ref_ref_cod_serie,
			null,
			null,
			$this->ref_cod_aluno,
			null,
			null,
			null,
			null,
			null,
			1,
			null,
			$this->ref_cod_curso,
			$this->ref_cod_instituicao,
			1
		);

		$total = $obj_matricula->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsPmieducarCurso" ) )
				{
					$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
					$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
					$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
				}
				else
				{
					$registro["ref_cod_curso"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
				}
				if( class_exists( "clsPmieducarSerie" ) )
				{
					$obj_serie = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
					$det_serie = $obj_serie->detalhe();
					$registro["ref_ref_cod_serie"] = $det_serie["nm_serie"];
				}
				else
				{
					$registro["ref_ref_cod_serie"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarSerie\n-->";
				}
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["ref_ref_cod_escola"] = $det_ref_cod_escola["nome"];
				}
				else
				{
					$registro["ref_ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
				}

        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista($registro['cod_matricula'], NULL, NULL,
                                           NULL, NULL, NULL, NULL, NULL, 1);
        $nomesTurmas = array();
        foreach ($enturmacoes as $enturmacao) {
          $turma         = new clsPmieducarTurma($enturmacao['ref_cod_turma']);
          $turma         = $turma->detalhe();
          $nomesTurmas[] = $turma['nm_turma'];
        }
        $nomesTurmas = implode('<br />', $nomesTurmas);

        $situacao = $registro['aprovado'];
        if ($situacao) {
            $situacao = App_Model_MatriculaSituacao::getInstance()->getValue($registro['aprovado']);
        } else {
            $situacao = "";
        }

				$lista_busca = array();

   			$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ano"]}</a>";
   			$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["cod_matricula"]}</a>";
   			$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">$situacao</a>";

				if ($nomesTurmas) {
					$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">$nomesTurmas</a>";
        }
				else
					$lista_busca[] = "";

				if ($registro["ref_ref_cod_serie"])
					$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ref_ref_cod_serie"]}</a>";
				else
					$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">-</a>";

				/*$lista_busca = array(
					"<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ref_ref_cod_serie"]}</a>",
					"<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ref_cod_curso"]}</a>"
				);*/
				$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ref_cod_curso"]}</a>";

				if ($nivel_usuario == 1)
				{
					if ($registro["ref_ref_cod_escola"])
						$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ref_ref_cod_escola"]}</a>";
					else
						$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">-</a>";

					$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ref_cod_instituicao"]}</a>";
				}
				else if ($nivel_usuario == 2)
				{
					if ($registro["ref_ref_cod_escola"])
						$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">{$registro["ref_ref_cod_escola"]}</a>";
					else
						$lista_busca[] = "<a href=\"educar_matricula_det.php?cod_matricula={$registro["cod_matricula"]}\">-</a>";
				}
				$this->addLinhas($lista_busca);
			}
		}
    else
    {
				$this->addLinhas(array('Aluno sem matr�culas em andamento na sua escola.'));
    }

		$this->addPaginador2( "educar_matricula_lst.php", $total, $_GET, $this->nome, $this->limite );
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{
			$this->acao = "go(\"educar_matricula_cad.php?ref_cod_aluno={$this->ref_cod_aluno}\")";
			$this->nome_acao = "Nova Matr&iacute;cula";
		}
		$this->array_botao[] = 'Voltar';
		$this->array_botao_url[] = "educar_aluno_det.php?cod_aluno={$this->ref_cod_aluno}";

		$this->largura = "100%";
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
<script>

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
}

</script>
