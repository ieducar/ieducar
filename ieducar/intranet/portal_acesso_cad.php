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
require_once ("include/clsCadastro.inc.php");
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

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $cod_acesso;
	var $data_hora;
	var $ip_externo;
	var $ip_interno;
	var $cod_pessoa;
	var $obs;
	var $sucesso;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_acesso=$_GET["cod_acesso"];


		if( is_numeric( $this->cod_acesso ) )
		{

			$obj = new clsPortalAcesso( $this->cod_acesso );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				$this->data_hora = dataFromPgToBr( $this->data_hora );


				$this->fexcluir = true;

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "portal_acesso_det.php?cod_acesso={$registro["cod_acesso"]}" : "portal_acesso_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_acesso", $this->cod_acesso );

		// foreign keys

		// text
		$this->campoTexto( "ip_externo", "Ip Externo", $this->ip_externo, 30, 255, true );
		$this->campoTexto( "ip_interno", "Ip Interno", $this->ip_interno, 30, 255, true );
		$this->campoNumero( "cod_pessoa", "Pessoa", $this->cod_pessoa, 15, 255, true );
		$this->campoMemo( "obs", "Obs", $this->obs, 60, 10, false );

		// data
		$this->campoData( "data_hora", "Data Hora", $this->data_hora, true );

		// time

		// bool
		$this->campoBoolLista( "sucesso", "Sucesso", $this->sucesso );
		//$this->campoCheck( "sucesso", "Sucesso", ( $this->sucesso == 't' ) );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsPortalAcesso( $this->cod_acesso, $this->data_hora, $this->ip_externo, $this->ip_interno, $this->cod_pessoa, $this->obs, $this->sucesso );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: portal_acesso_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPortalAcesso\nvalores obrigatorios\nis_string( $this->data_hora ) && is_string( $this->ip_externo ) && is_string( $this->ip_interno ) && is_numeric( $this->cod_pessoa ) && ! is_null( $this->sucesso )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsPortalAcesso($this->cod_acesso, $this->data_hora, $this->ip_externo, $this->ip_interno, $this->cod_pessoa, $this->obs, $this->sucesso);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: portal_acesso_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPortalAcesso\nvalores obrigatorios\nif( is_numeric( $this->cod_acesso ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();



		$obj = new clsPortalAcesso($this->cod_acesso, $this->data_hora, $this->ip_externo, $this->ip_interno, $this->cod_pessoa, $this->obs, $this->sucesso);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: portal_acesso_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPortalAcesso\nvalores obrigatorios\nif( is_numeric( $this->cod_acesso ) )\n-->";
		return false;
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