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
require_once( "include/public/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Bairro" );
		$this->processoAp = "756";
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
	
	var $idmun;
	var $geom;
	var $idbai;
	var $nome;
	var $idpes_rev;
	var $data_rev;
	var $origem_gravacao;
	var $idpes_cad;
	var $data_cad;
	var $operacao;
	var $idsis_rev;
	var $idsis_cad;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Bairro - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->idbai=$_GET["idbai"];

		$tmp_obj = new clsPublicBairro();
		$lst_bairro = $tmp_obj->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idbai );
		
		if( ! $lst_bairro )
		{
			header( "location: public_bairro_lst.php" );
			die();
		}
		else 
		{
			$registro = $lst_bairro[0];
		}
		

		if( $registro["nome"] )
		{
			$this->addDetalhe( array( "Nome", "{$registro["nome"]}") );
		}
		if( $registro["nm_municipio"] )
		{
			$this->addDetalhe( array( "Munic&iacute;pio", "{$registro["nm_municipio"]}") );
		}
		if( $registro["nm_estado"] )
		{
			$this->addDetalhe( array( "Estado", "{$registro["nm_estado"]}") );
		}
		if( $registro["nm_pais"] )
		{
			$this->addDetalhe( array( "Pais", "{$registro["nm_pais"]}") );
		}
		if( $registro["origem_gravacao"] )
		{
			$this->addDetalhe( array( "Origem Gravac&atilde;o", "{$registro["origem_gravacao"]}") );
		}
		if( $registro["operacao"] )
		{
			$this->addDetalhe( array( "Operac&atilde;o", "{$registro["operacao"]}") );
		}


		$this->url_novo = "public_bairro_cad.php";
		$this->url_editar = "public_bairro_cad.php?idbai={$registro["idbai"]}";

		$this->url_cancelar = "public_bairro_lst.php";
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