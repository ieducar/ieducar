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
require_once ("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Cole&ccedil&atilde;o" );
		$this->processoAp = "593";
		$this->addEstilo('localizacaoSistema');
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

	var $cod_acervo_colecao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_colecao;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Cole&ccedil&atilde;o - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		

		$this->addCabecalhos( array(
			"Cole&ccedil;&atilde;o",
			"Biblioteca"
		) );

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_biblioteca = true;
		$get_cabecalho = "lista_busca";
		
		include("include/pmieducar/educar_campo_lista.php");
		// outros Filtros
		$this->campoTexto( "nm_colecao", "Cole&ccedil;&atilde;o", $this->nm_colecao, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		if(!is_numeric($this->ref_cod_biblioteca))
		{
			$obj_bib_user = new clsPmieducarBibliotecaUsuario();
			$this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
		}
		
		$obj_acervo_colecao = new clsPmieducarAcervoColecao();
		$obj_acervo_colecao->setOrderby( "nm_colecao ASC" );
		$obj_acervo_colecao->setLimite( $this->limite, $this->offset );

		$lista = $obj_acervo_colecao->lista(
			$this->cod_acervo_colecao,
			null,
			null,
			$this->nm_colecao,
			$this->descricao,
			null,
			null,
			null,
			null, 
			1,
			$this->ref_cod_biblioteca
		);

		$total = $obj_acervo_colecao->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
				$det_biblioteca = $obj_biblioteca->detalhe();
				$registro['ref_cod_biblioteca'] = $det_biblioteca['nm_biblioteca'];
				$this->addLinhas( array(
					"<a href=\"educar_acervo_colecao_det.php?cod_acervo_colecao={$registro["cod_acervo_colecao"]}\">{$registro["nm_colecao"]}</a>",
					"<a href=\"educar_acervo_colecao_det.php?cod_acervo_colecao={$registro["cod_acervo_colecao"]}\">{$registro["ref_cod_biblioteca"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_acervo_colecao_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 593, $this->pessoa_logada, 11 ) )
		{
		$this->acao = "go(\"educar_acervo_colecao_cad.php\")";
		$this->nome_acao = "Novo";
		}

		$this->largura = "100%";

	    $localizacao = new LocalizacaoSistema();
	    $localizacao->entradaCaminhos( array(
	         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
	         "educar_biblioteca_index.php"                  => "i-Educar - Biblioteca",
	         ""                                  => "Listagem de cole&ccedil;&otilde;es"
	    ));
	    $this->enviaLocalizacao($localizacao->montar());
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