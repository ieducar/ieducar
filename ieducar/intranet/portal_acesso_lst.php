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
require_once( "include/portal/clsPortalAcesso.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Acesso" );
		$this->processoAp = "666";
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

	var $cod_pessoa;
	var $min_quantidade_falhas;
	var $ultimo_sucesso_ini;
	var $ultimo_sucesso_fim;
	var $quinto_erro_ini;
	var $quinto_erro_fim;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		if( is_numeric( $_GET["cod_pessoa_libera"] ) )
		{
			$obj_acesso = new clsPortalAcesso();
			$obj_acesso->setCamposLista( "cod_acesso" );
			$obj_acesso->setLimite(1);
			$obj_acesso->setOrderby("data_hora DESC");
			$lista = $obj_acesso->lista(null,null,null,null,$_GET["cod_pessoa_libera"],null,'f');
			if( $lista )
			{
				foreach ( $lista AS $cod_acesso )
				{
					$obj_acesso = new clsPortalAcesso($cod_acesso,null,null,null,null,null,'t');
					if( $obj_acesso->edita() )
					{
						$this->mensagem = "Alteração realizada com sucesso";
					}
				}
			}
		}

		$this->titulo = "Acesso - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		

		$this->addCabecalhos( array(
			"Cod. Pessoa",
			"Nome Pessoa",
			"Falhas",
			"Ultimo Sucesso",
			"Quinto Erro"
		) );

		// Filtros de Foreign Keys


		// outros Filtros
		$this->campoData( "ultimo_sucesso_ini", "Data minima do ultimo sucesso", $this->ultimo_sucesso_ini, false );
		$this->campoData( "ultimo_sucesso_fim", "Data maxima do ultimo sucesso", $this->ultimo_sucesso_fim, false );

		$this->campoData( "quinto_erro_ini", "Data minima do quinto erro", $this->quinto_erro_ini, false );
		$this->campoData( "quinto_erro_fim", "Data maxima do quinto erro", $this->quinto_erro_fim, false );

		$this->campoNumero( "cod_pessoa", "Pessoa", $this->cod_pessoa, 15, 255, false );


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_acesso = new clsPortalAcesso();
		$obj_acesso->setOrderby( "falha DESC" );
		$obj_acesso->setLimite( $this->limite, $this->offset );

		$lista = $obj_acesso->lista_falhas(
			$this->cod_pessoa,
			$this->min_quantidade_falhas,
			$this->max_quantidade_falhas,
			$this->ultimo_sucesso_ini,
			$this->ultimo_sucesso_fim,
			$this->quinto_erro_ini,
			$this->quinto_erro_fim
		);

		$total = $obj_acesso->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data
				$registro["ultimo_sucesso_time"] = strtotime( substr( $registro["ultimo_sucesso"], 0, 16 ) );
				$registro["ultimo_sucesso_br"] = date( "d/m/Y H:i", $registro["ultimo_sucesso_time"] );

				$registro["quinto_erro_time"] = strtotime( substr( $registro["quinto_erro"], 0, 16 ) );
				$registro["quinto_erro_br"] = date( "d/m/Y H:i", $registro["quinto_erro_time"] );


				// pega detalhes de foreign_keys

				$pessoa = new clsPessoa_($registro["cod_pessoa"]);
				$det_pessoa = $pessoa->detalhe();
				$registro["nome"] = $det_pessoa["nome"];

				$this->addLinhas( array(
					"<a href=\"portal_acesso_lst.php?cod_pessoa_libera={$registro["cod_pessoa"]}\">{$registro["cod_pessoa"]}</a>",
					"<a href=\"portal_acesso_lst.php?cod_pessoa_libera={$registro["cod_pessoa"]}\">{$registro["nome"]}</a>",
					"<a href=\"portal_acesso_lst.php?cod_pessoa_libera={$registro["cod_pessoa"]}\">{$registro["falha"]}</a>",
					"<a href=\"portal_acesso_lst.php?cod_pessoa_libera={$registro["cod_pessoa"]}\">{$registro["ultimo_sucesso_br"]}</a>",
					"<a href=\"portal_acesso_lst.php?cod_pessoa_libera={$registro["cod_pessoa"]}\">{$registro["quinto_erro_br"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "portal_acesso_lst.php", $total, $_GET, $this->nome, $this->limite );

		$this->acao = "go(\"portal_acesso_cad.php\")";
		$this->nome_acao = "Novo";

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