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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Motivo Transfer&ecirc;ncia" );
		$this->processoAp = "575";
		$this->addEstilo("localizacaoSistema");
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

	var $cod_transferencia_tipo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $desc_tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_escola;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Transferencia Tipo - Detalhe";
		

		$this->cod_transferencia_tipo=$_GET["cod_transferencia_tipo"];

		$tmp_obj = new clsPmieducarTransferenciaTipo( $this->cod_transferencia_tipo );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_transferencia_tipo_lst.php" );
			die();
		}
		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_cod_escola"] );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
			$registro["ref_cod_instituicao"] = $det_ref_cod_escola["ref_cod_instituicao"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
		}
		if (class_exists("clsPmieducarInstituicao"))
		{
			$obj_instituicao = new clsPmieducarInstituicao($registro["ref_cod_instituicao"]);
			$obj_instituicao_det = $obj_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $obj_instituicao_det['nm_instituicao'];
		}
		else
		{
			$cod_instituicao = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
		{
			if( $registro["ref_cod_instituicao"] )
			{
				$this->addDetalhe( array( "Institui&ccedil;&atilde;o", "{$registro["ref_cod_instituicao"]}") );
			}
		}
		if ($nivel_usuario == 1 || $nivel_usuario == 2)
		{
			if( $registro["ref_cod_escola"] )
			{
				$this->addDetalhe( array( "Escola", "{$registro["ref_cod_escola"]}") );
			}
		}
		if( $registro["nm_tipo"] )
		{
			$this->addDetalhe( array( "Motivo Transfer&ecirc;ncia", "{$registro["nm_tipo"]}") );
		}
		if( $registro["desc_tipo"] )
		{
			$this->addDetalhe( array( "Descri&ccedil;&atilde;o", "{$registro["desc_tipo"]}") );
		}

		if( $obj_permissoes->permissao_cadastra( 575, $this->pessoa_logada, 7 ) )
		{
			$this->url_novo = "educar_transferencia_tipo_cad.php";
			$this->url_editar = "educar_transferencia_tipo_cad.php?cod_transferencia_tipo={$registro["cod_transferencia_tipo"]}";
		}
		$this->url_cancelar = "educar_transferencia_tipo_lst.php";
		$this->largura = "100%";

		$localizacao = new LocalizacaoSistema();
	    $localizacao->entradaCaminhos( array(
	         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
	         "educar_index.php"                  => "i-Educar - Escola",
	         ""                                  => "Detalhe do tipo de transfer&ecirc;ncia"
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