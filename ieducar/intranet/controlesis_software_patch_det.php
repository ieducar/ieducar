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
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - Detalhe Patch de Software " );
		$this->processoAp = "795";
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

	var $cod_software_patch;
	var $ref_funcionario_exc;
	var $ref_funcionario_cad;
	var $ref_cod_software;
	var $data_patch;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Software Patch - Detalhe";
		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_software_patch=$_GET["cod_software_patch"];

		$tmp_obj = new clsPmicontrolesisSoftwarePatch( $this->cod_software_patch );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: controlesis_software_patch_lst.php" );
			die();
		}

		if( class_exists( "clsFuncionario" ) )
		{
			$obj_ref_funcionario_cad = new clsFuncionario( $registro["ref_funcionario_cad"] );
			$det_ref_funcionario_cad = $obj_ref_funcionario_cad->detalhe();
			if( is_object( $det_ref_funcionario_cad["idpes"] ) )
			{
			$det_ref_funcionario_cad = $det_ref_funcionario_cad["idpes"]->detalhe();
			$registro["ref_funcionario_cad"] = $det_ref_funcionario_cad["nome"];
			}
			else
			{
			$pessoa = new clsPessoa_( $det_ref_funcionario_cad["idpes"] );
			$det_ref_funcionario_cad = $pessoa->detalhe();
			$registro["ref_funcionario_cad"] = $det_ref_funcionario_cad["nome"];
			}
		}
		else
		{
			$registro["ref_funcionario_cad"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
		}

		if( class_exists( "clsFuncionario" ) )
		{
			$obj_ref_funcionario_exc = new clsFuncionario( $registro["ref_funcionario_exc"] );
			$det_ref_funcionario_exc = $obj_ref_funcionario_exc->detalhe();
			if( is_object( $det_ref_funcionario_exc["idpes"] ) )
			{
			$det_ref_funcionario_exc = $det_ref_funcionario_exc["idpes"]->detalhe();
			$registro["ref_funcionario_exc"] = $det_ref_funcionario_exc["nome"];
			}
			else
			{
			$pessoa = new clsPessoa_( $det_ref_funcionario_exc["idpes"] );
			$det_ref_funcionario_exc = $pessoa->detalhe();
			$registro["ref_funcionario_exc"] = $det_ref_funcionario_exc["nome"];
			}
		}
		else
		{
			$registro["ref_funcionario_exc"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
		}

		if( class_exists( "clsPmicontrolesisSoftware" ) )
		{
			$obj_ref_cod_software = new clsPmicontrolesisSoftware( $registro["ref_cod_software"] );
			$det_ref_cod_software = $obj_ref_cod_software->detalhe();
			$registro["ref_cod_software"] = $det_ref_cod_software["nm_software"];
		}
		else
		{
			$registro["ref_cod_software"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmicontrolesisSoftware\n-->";
		}


		if( $registro["cod_software_patch"] )
		{
			$this->addDetalhe( array( "Software Patch", "{$registro["cod_software_patch"]}") );
		}
		if( $registro["ref_cod_software"] )
		{
			$this->addDetalhe( array( "Software", "{$registro["ref_cod_software"]}") );
		}
		if( $registro["data_patch"] )
		{
			$this->addDetalhe( array( "Data Patch", dataFromPgToBr( $registro["data_patch"], "d/m/Y" ) ) );
		}


		$this->url_novo = "controlesis_software_patch_cad.php";
		$this->url_editar = "controlesis_software_patch_cad.php?cod_software_patch={$registro["cod_software_patch"]}";

		$this->array_botao[] = 'Relatorio Altera��es';
		$this->array_botao_url_script[] = "showExpansivelImprimir(400, 200,  \"controlesis_relatorio_software_patch.php?cod_software_patch={$this->cod_software_patch}\",[], \"Relat�rio i-Educar\" )";

		$this->url_cancelar = "controlesis_software_patch_lst.php";
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
