<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
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
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Calendario Anotacao" );
		$this->processoAp = "620";
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

	var $cod_calendario_anotacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_anotacao;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $dia;
	var $mes;
	var $ano;

	var $ref_cod_calendario_ano_letivo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();



		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		if($this->ref_cod_calendario_ano_letivo && $this->ano && $this->mes && $this->dia)
		{
			$obj_calendario = new clsPmieducarCalendarioAnoLetivo($this->ref_cod_calendario_ano_letivo);
			if(!$obj_calendario->existe())
			{
				header("location: educar_calendario_ano_letivo_lst.php");
				die;
			}
			$this->titulo = "Anota&ccedil;&otilde;oes Calend&aacute;rio <b>{$this->dia}/{$this->mes}/{$this->ano}</b> - Listagem";

			@session_start();
			$_SESSION["calendario"]["anotacao"]["dia"] =  $this->dia;
			$_SESSION["calendario"]["anotacao"]["mes"] =  $this->mes;
			$_SESSION["calendario"]["anotacao"]["ano"] =  $this->ano;
			$_SESSION["calendario"]["anotacao"]["ref_cod_calendario_ano_letivo"] =  $this->ref_cod_calendario_ano_letivo;
			session_write_close();
		}else{
			header("location: educar_calendario_ano_letivo_lst.php");
		}


		

		$this->addCabecalhos( array(
			"Anotac&atilde;o",
			"Descri&ccedil;&atilde;o"
		) );

		// Filtros de Foreign Keys


		//// outros Filtros
	//	$this->campoTexto( "nm_anotacao", "Nome Anotac&atilde;o", $this->nm_anotacao, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		/*$obj_calendario_anotacao = new clsPmieducarCalendarioAnotacao();
		$obj_calendario_anotacao->setOrderby( "nm_anotacao ASC" );
		$obj_calendario_anotacao->setLimite( $this->limite, $this->offset );

		$lista = $obj_calendario_anotacao->lista(
			$this->cod_calendario_anotacao,
			null,
			null,
			$this->nm_anotacao,
			$this->descricao,
			null,
			null,
			1
		);*/

		$obj_calendario_anotacao_dia = new clsPmieducarCalendarioDiaAnotacao();
		$obj_calendario_anotacao_dia->setLimite( $this->limite, $this->offset );


		$lista = $obj_calendario_anotacao_dia->lista($this->dia,$this->mes,$this->ref_cod_calendario_ano_letivo,null,1);


		$total = $obj_calendario_anotacao_dia->_total;

		// monta a lista
		$get = "&dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}";
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{

				$obj_calendario_anotacao = new clsPmieducarCalendarioAnotacao($registro['ref_cod_calendario_anotacao'],null,null,null,null,null,null,1);
				$det = $obj_calendario_anotacao->detalhe();
				/*
					"<a href=\"educar_calendario_anotacao_det.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}\">{$registro["ref_dia"]}</a>",
					"<a href=\"educar_calendario_anotacao_det.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}\">{$registro["ref_mes"]}</a>",
				*/
				$this->addLinhas( array(
					"<a href=\"educar_calendario_anotacao_cad.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}{$get}\">{$det["nm_anotacao"]}</a>",
					"<a href=\"educar_calendario_anotacao_cad.php?cod_calendario_anotacao={$det["cod_calendario_anotacao"]}{$get}\">{$det["descricao"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_calendario_anotacao_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7 ) )
		{
			$this->acao = "go(\"educar_calendario_anotacao_cad.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}\")";
			$this->nome_acao = "Nova Anota&ccedil;&atilde;o";
			$this->array_botao = array('Dia Extra/N&atilde;o Letivo','Calend&aacute;rio');
			$this->array_botao_url = array("educar_calendario_dia_cad.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}","educar_calendario_ano_letivo_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}");
		}

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