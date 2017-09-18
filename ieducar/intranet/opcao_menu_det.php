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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");

class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Opção Menu" );
		$this->processoAp = "475";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		@session_start();
		$idpes = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->titulo = "Tipo de Menu";
		

		$db = new clsBanco();
		$tipo_menu = $db->UnicoCampo("SELECT tipo_menu FROM funcionario WHERE ref_cod_pessoa_fj = '$idpes'");
		$opcao = array("0"=>"Menu Padrão","1"=> "Menu Suspenso");

		$this->addDetalhe( array("Tipo de Menu", $opcao[$tipo_menu]) );
		
		//botoes
		$this->url_editar 	= "opcao_menu_cad.php";
		$this->url_cancelar = "opcao_menu_lst.php";
		
		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""                                  => "Detalhe da prefer&ecirc;ncia"
    ));
    $this->enviaLocalizacao($localizacao->montar());				
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>