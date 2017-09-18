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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Coffebreak" );
		$this->processoAp = "564";
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
	
	var $cod_coffebreak_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $desc_tipo;
	var $custo_unitario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Tipo Coffebreak - Listagem";
		
		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;
		
		
	
		$this->addCabecalhos( array( 
			"Nome Tipo",
			"Custo Unitario"
		) );
		
		// Filtros de Foreign Keys


		// outros Filtros
		$this->campoTexto( "nm_tipo", "Tipo Coffee Break", $this->nm_tipo, 30, 255, false );
		$this->campoNumero( "custo_unitario", "Custo Unit&aacute;rio", $this->custo_unitario, 15, 255, false );

		
		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;
		
		$obj_coffebreak_tipo = new clsPmieducarCoffebreakTipo();
		$obj_coffebreak_tipo->setOrderby( "nm_tipo ASC" );
		$obj_coffebreak_tipo->setLimite( $this->limite, $this->offset );
		
		$lista = $obj_coffebreak_tipo->lista(
			$this->cod_coffebreak_tipo,
			null,
			null,
			$this->nm_tipo,
			null,
			$this->custo_unitario,
			null,
			null,
			null,
			null,
			1
		);
		
		$total = $obj_coffebreak_tipo->_total;
		
		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{

				$registro["custo_unitario"] = str_replace(".",",",$registro["custo_unitario"]);
				
				$this->addLinhas( array( 
					"<a href=\"educar_coffebreak_tipo_det.php?cod_coffebreak_tipo={$registro["cod_coffebreak_tipo"]}\">{$registro["nm_tipo"]}</a>",
					"<a href=\"educar_coffebreak_tipo_det.php?cod_coffebreak_tipo={$registro["cod_coffebreak_tipo"]}\">{$registro["custo_unitario"]}</a>" 
				) );
			}
		}
		
		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();
		
		if($obj_permissao->permissao_cadastra(554, $this->pessoa_logada,7))
		{		
			$this->acao = "go(\"educar_coffebreak_tipo_cad.php\")";
			$this->nome_acao = "Novo";
		}
		//**

			
		$this->addPaginador2( "educar_coffebreak_tipo_lst.php", $total, $_GET, $this->nome, $this->limite );

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