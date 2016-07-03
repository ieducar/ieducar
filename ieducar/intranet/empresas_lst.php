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
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/localizacaoSistema.php';


class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Empresas!" );
		$this->processoAp = array("41", "649");
                $this->addEstilo( "localizacaoSistema" );

	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Empresas";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		
		$this->addCabecalhos( array( "Raz�o Social", "Nome Fantasia" ) );
		
		$this->campoTexto( "fantasia", "Nome Fantasia",  $_GET['nm_pessoa'], "50", "255", true );
		$this->campoTexto( "razao_social", "Raz�o Social",  $_GET['razao_social'], "50", "255", true );
		$this->campoCnpj( "id_federal", "CNPJ",  $_GET['id_federal'], "50", "255", true );
		
		// Paginador
		$limite = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		
		$par_nome = false;
		$par_razao = false;
		$par_cnpj = false;
		$opcoes = false;
		if ($_GET['fantasia'])
		{
			$par_fantasia = $_GET['fantasia'];
		}
		if ($_GET['razao_social'])
		{
			$par_razao = $_GET['razao_social'];
			
			$objPessoaFJ = new clsPessoaFj();
			$lista = $objPessoaFJ->lista($par_razao);
			if($lista)
			foreach ($lista as $pessoa) {
				$opcoes[] = $pessoa['idpes'];
			}	
		}
		if ($_GET['id_federal'])
		{
			$par_cnpj =  idFederal2Int( $_GET['id_federal'] );
		}
		
		$objPessoa = new clsPessoaJuridica();
		$empresas = $objPessoa->lista( $par_cnpj, $par_fantasia, false, $iniciolimit, $limite, "fantasia asc",$opcoes );
		if($empresas)
		{
			foreach ( $empresas AS $empresa )
			{
				$total = $empresa['total'];
				$cod_empresa = $empresa['idpes'];
				$razao_social = $empresa['nome'];
				$nome_fantasia = $empresa['fantasia'];
				$this->addLinhas( array( "<a href='empresas_det.php?cod_empresa={$cod_empresa}'><img src='imagens/noticia.jpg' border=0>$razao_social</a>", "<a href='empresas_det.php?cod_empresa={$cod_empresa}'>{$nome_fantasia}</a>" ) );
			}
		}
		// Paginador
		$this->addPaginador2( " empresas_lst.php", $total, $_GET, $this->nome, $limite );

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra(41, $this->pessoa_logada, 3)) {
			$this->acao = "go(\"empresas_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos(array($_SERVER['SERVER_NAME'] . '/intranet' => 'i-Educar', '' => 'Registro de Empresas'));
                $this->enviaLocalizacao($localizacao->montar());
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>