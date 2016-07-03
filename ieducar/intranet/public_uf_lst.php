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
require_once( "include/public/geral.inc.php" );
require_once 'include/localizacaoSistema.php';


class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Uf" );
		$this->processoAp = "754";
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
	var $__pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $__titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $__limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $__offset;

	var $sigla_uf;
	var $nome;
	var $geom;
	var $idpais;

	function Gerar()
	{
		@session_start();
		$this->__pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->__titulo = "Uf - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Nome",
			"Sigla Uf",
			"Pais"
		) );

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPais" ) )
		{
			$objTemp = new clsPais();
			$lista = $objTemp->lista( false, false, false, false, false, "nome ASC" );
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['idpais']}"] = "{$registro['nome']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPais nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "idpais", "Pais", $opcoes, $this->idpais, "", false, "", "", false, false );

		// outros Filtros
		$this->campoTexto( "sigla_uf", "Sigla Uf", $this->sigla_uf, 2, 2, false );
		$this->campoTexto( "nome", "Nome", $this->nome, 30, 30, false );


		// Paginador
		$this->__limite = 20;
		$this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

		$obj_uf = new clsPublicUf();
		$obj_uf->setOrderby( "nome ASC" );
		$obj_uf->setLimite( $this->__limite, $this->__offset );

		$lista = $obj_uf->lista(
			$this->nome,
			$this->geom,
			$this->idpais,
			$this->sigla_uf
		);

		$total = $obj_uf->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$this->addLinhas( array(
					"<a href=\"public_uf_det.php?sigla_uf={$registro["sigla_uf"]}\">{$registro["nome"]}</a>",
					"<a href=\"public_uf_det.php?sigla_uf={$registro["sigla_uf"]}\">{$registro["sigla_uf"]}</a>",
					"<a href=\"public_uf_det.php?sigla_uf={$registro["sigla_uf"]}\">{$registro["nm_pais"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "public_uf_lst.php", $total, $_GET, $this->nome, $this->__limite );

		$this->acao = "go(\"public_uf_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos(array($_SERVER['SERVER_NAME'] . '/intranet' => 'i-Educar', '' => 'Busca por Estados'));
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