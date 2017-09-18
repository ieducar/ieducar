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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Operador" );
		$this->processoAp = "589";
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
	
	var $cod_operador;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nome;
	var $valor;
	var $fim_sentenca;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Operador - Listagem";
		
		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;
		
		
	
		$this->addCabecalhos( array( 
			"Nome",
			"Valor",
			"Fim Sentenca"
		) );
		
		// Filtros de Foreign Keys


		// outros Filtros
		$this->campoTexto( "nome", "Nome", $this->nome, 30, 255, false );
		$this->campoTexto( "valor", "Valor", $this->valor, 30, 255, false );
		$opcoes = array( "Não", "Sim" );
		$this->campoLista( "fim_sentenca", "Fim Sentenca", $opcoes, $this->fim_sentenca, "", false, "", "", false, false );
		
		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;
		
		$obj_operador = new clsPmieducarOperador();
		$obj_operador->setOrderby( "nome ASC" );
		$obj_operador->setLimite( $this->limite, $this->offset );
		
		$lista = $obj_operador->lista(
			$this->cod_operador,
			null,
			null,
			$this->nome,
			$this->valor,
			$this->fim_sentenca,
			null,
			null,
			1
		);
		
		$total = $obj_operador->_total;
		
		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data
				$registro["data_cadastro_time"] = strtotime( substr( $registro["data_cadastro"], 0, 16 ) );
				$registro["data_cadastro_br"] = date( "d/m/Y H:i", $registro["data_cadastro_time"] );

				$registro["data_exclusao_time"] = strtotime( substr( $registro["data_exclusao"], 0, 16 ) );
				$registro["data_exclusao_br"] = date( "d/m/Y H:i", $registro["data_exclusao_time"] );


				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarUsuario" ) )
				{
					$obj_ref_usuario_exc = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
					$det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
					$registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];
				}
				else
				{
					$registro["ref_usuario_exc"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarUsuario\n-->";
				}

				if( class_exists( "clsPmieducarUsuario" ) )
				{
					$obj_ref_usuario_cad = new clsPmieducarUsuario( $registro["ref_usuario_cad"] );
					$det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
					$registro["ref_usuario_cad"] = $det_ref_usuario_cad["data_cadastro"];
				}
				else
				{
					$registro["ref_usuario_cad"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarUsuario\n-->";
				}

				$registro["fim_sentenca"] = ( $registro["fim_sentenca"] ) ? "Sim": "Não";

				$this->addLinhas( array( 
					"<a href=\"educar_operador_det.php?cod_operador={$registro["cod_operador"]}\">{$registro["nome"]}</a>",
					"<a href=\"educar_operador_det.php?cod_operador={$registro["cod_operador"]}\">{$registro["valor"]}</a>",
					"<a href=\"educar_operador_det.php?cod_operador={$registro["cod_operador"]}\">{$registro["fim_sentenca"]}</a>" 
				) );
			}
		}
		$this->addPaginador2( "educar_operador_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 589, $this->pessoa_logada, 0, null, true ) )
		{
			$this->acao = "go(\"educar_operador_cad.php\")";
			$this->nome_acao = "Novo";
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