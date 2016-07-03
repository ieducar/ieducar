<?php
/**
 *
 *	@author Prefeitura Municipal de Itaja�
 *	@updated 29/03/2007
 *   Pacote: i-PLB Software P�blico Livre e Brasileiro
 *
 *	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�
 *						ctima@itajai.sc.gov.br
 *
 *	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou
 *	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme
 *	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da
 *	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.
 *
 *	Este programa  � distribu�do na expectativa de ser �til, mas SEM
 *	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-
 *	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-
 *	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.
 *
 *	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU
 *	junto  com  este  programa. Se n�o, escreva para a Free Software
 *	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 *	02111-1307, USA.
 *
 */

require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Cliente" );
		$this->processoAp = "603";
                $this->addEstilo( "localizacaoSistema" );
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

	var $cod_cliente;
	var $ref_cod_cliente_tipo;
	var $ref_cod_biblioteca;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idpes;
	var $login;
	var $senha;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $status;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Cliente - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Cliente",
			"Tipo",
			"Status"
		) );

		$opcoes = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );
		if( $this->ref_idpes )
		{
			$objTemp = new clsPessoaFisica( $this->ref_idpes );
			$detalhe = $objTemp->detalhe();
			$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "ref_idpes", "idpes", "nome" );
		$parametros->setCodSistema( 1 );
		$parametros->setPessoa( 'F' );
		$parametros->setPessoaEditar( 'N' );
		$parametros->setPessoaNovo( 'N' );
		$this->campoListaPesq( "ref_idpes", "Cliente", $opcoes, $this->ref_idpes, "pesquisa_pessoa_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );

		$this->campoLista( "status", "Status", array( '' => "Selecione", 'R' => "Regular", 'S' => "Suspenso" ), $this->status, "", false, "", "", false, false );

		$instituicao_obrigatorio  = true;
		$escola_obrigatorio		  = false;
		$biblioteca_obrigatorio	  = true;
		$cliente_tipo_obrigatorio = true;
		$get_instituicao 		  = true;
		$get_escola		 		  = true;
		$get_biblioteca  		  = true;
		$get_cliente_tipo		  = true;

		include( "include/pmieducar/educar_campo_lista.php" );
		
		
		
		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_cliente = new clsPmieducarCliente();
		$obj_cliente->setOrderby( "nome ASC" );
		$obj_cliente->setLimite( $this->limite, $this->offset );

		if ( $this->status != 'S' )
			$this->status = null;
		
		$cod_biblioteca = $this->ref_cod_biblioteca;
		if(!is_numeric($this->ref_cod_biblioteca))
		{	
			$db = new clsBanco();
			$db->Consulta("SELECT ref_cod_biblioteca FROM pmieducar.biblioteca_usuario WHERE ref_cod_usuario = '$this->pessoa_logada' ");
			if($db->numLinhas())
			{
				$cod_biblioteca = array();
				while ($db->ProximoRegistro()) 
				{
					list($ref_cod) = $db->Tupla();
					$cod_biblioteca[] = $ref_cod;
				}
			}
		}	
			$lista = $obj_cliente->listaCompleta( null,
												  null,
												  null, 
												  $this->ref_idpes,
												  null, 
												  null, 	
												  null,
												  null, 
												  null,
												  null,
												  1,
												  null,
												  $this->status,
												  $this->ref_cod_cliente_tipo,
												  null, 
												  $cod_biblioteca
												);
		$total = $obj_cliente->_total;
	    $obj_banco = new clsBanco();
		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if ( class_exists( "clsBanco" ) ) {

					$sql_unico = "SELECT 1
									FROM pmieducar.cliente_suspensao
								   WHERE ref_cod_cliente = {$registro["cod_cliente"]}
									 AND data_liberacao IS NULL
									 AND EXTRACT ( DAY FROM ( NOW() - data_suspensao ) ) < dias";
					$suspenso  = $obj_banco->CampoUnico( $sql_unico );
					if ( is_numeric( $suspenso ) )
						$registro["status"] = "Suspenso";
					else
						$registro["status"] = "Regular";
				}
				else {
					$registro["ref_idpes"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsBanco\n-->";
				}
//				echo "<pre>"; print_r($registro); die();
				$this->addLinhas( array(
					"<a href=\"educar_cliente_det.php?cod_cliente={$registro["cod_cliente"]}&ref_cod_biblioteca={$registro["cod_biblioteca"]}\">{$registro["nome"]}</a>",
					"<a href=\"educar_cliente_det.php?cod_cliente={$registro["cod_cliente"]}&ref_cod_biblioteca={$registro["cod_biblioteca"]}\">{$registro["nm_tipo"]}</a>",
					"<a href=\"educar_cliente_det.php?cod_cliente={$registro["cod_cliente"]}&ref_cod_biblioteca={$registro["cod_biblioteca"]}\">{$registro["status"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_cliente_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 603, $this->pessoa_logada, 11 ) )
		{
			$this->acao = "go(\"educar_cliente_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_biblooteca_index.php"                  => "Biblioteca",
                    ""                                  => "Lista de Clientes"
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
