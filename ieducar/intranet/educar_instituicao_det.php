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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Institui&ccedil;&atilde;o" );
		$this->processoAp = "559";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $complemento;
	var $nm_responsavel;
	var $ddd_telefone;
	var $telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $pessoa_logada;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Institui&ccedil;&atilde;o - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_instituicao=$_GET["cod_instituicao"];

		$tmp_obj = new clsPmieducarInstituicao( $this->cod_instituicao );
		$registro = $tmp_obj->detalhe();

		if( class_exists( "clsTipoLogradouro" ) )
		{
			$obj_ref_idtlog = new clsTipoLogradouro( $registro["ref_idtlog"] );
			$det_ref_idtlog = $obj_ref_idtlog->detalhe();
			$registro["ref_idtlog"] = $det_ref_idtlog["descricao"];
		}
		else
		{
			$registro["ref_idtlog"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsUrbanoTipoLogradouro\n-->";
		}

		$registro["cep"] = int2CEP( $registro["cep"] );
		$this->addDetalhe( array( "C�digo Institui��o", "{$registro["cod_instituicao"]}") );
		$this->addDetalhe( array( "Nome da Institui��o", "{$registro["nm_instituicao"]}") );
		$this->addDetalhe( array( "CEP", "{$registro["cep"]}") );
		$this->addDetalhe( array( "Logradouro", "{$registro["logradouro"]}") );
		$this->addDetalhe( array( "Bairro", "{$registro["bairro"]}") );
		$this->addDetalhe( array( "Cidade", "{$registro["cidade"]}") );
		$this->addDetalhe( array( "Tipo do Logradouro", "{$registro["ref_idtlog"]}") );
		$this->addDetalhe( array( "UF", "{$registro["ref_sigla_uf"]}") );
		$this->addDetalhe( array( "N�mero", "{$registro["numero"]}") );
		$this->addDetalhe( array( "Complemento", "{$registro["complemento"]}") );
		$this->addDetalhe( array( "DDD Telefone", "{$registro["ddd_telefone"]}") );
		$this->addDetalhe( array( "Telefone", "{$registro["telefone"]}") );
		$this->addDetalhe( array( "Nome do Respons�vel", "{$registro["nm_responsavel"]}") );

		$obj_permissoes = new clsPermissoes();
		if ( $obj_permissoes->permissao_cadastra( 559, $this->pessoa_logada, 1 ) ) {
			$this->url_novo = "educar_instituicao_cad.php";
			$this->url_editar = "educar_instituicao_cad.php?cod_instituicao={$registro["cod_instituicao"]}";
		}
		$this->url_cancelar = "educar_instituicao_lst.php";
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