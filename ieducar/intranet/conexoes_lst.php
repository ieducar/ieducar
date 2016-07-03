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
require_once 'include/localizacaoSistema.php';

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Conex�es!" );
		$this->processoAp = "157";
                $this->addEstilo( "localizacaoSistema" );


	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Conex�es";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
		
		$this->addCabecalhos( array( "Data Hora", "Local do Acesso") );
		
		// Paginador
		$limite = 20;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
				
		@session_start();
		$id_pessoa = $_SESSION['id_pessoa'];
		session_write_close();
		
		$sql = "SELECT to_char(b.data_hora, 'DD/MM/YYY - HH24:MI:SS') as data_hora, b.ip_externo FROM acesso b WHERE cod_pessoa={$id_pessoa}";
		if (!empty($_GET['status']))
		{
			if ($_GET['status'] == 'P')
				$where .= " AND ip_externo = '200.215.80.163'";
			else if ($_GET['status'] == 'X')
				$where .= " AND ip_externo <> '200.215.80.163'";
		}
		if(!empty($_GET['data_inicial']))
		{
			$data = explode("/", $_GET['data_inicial']);
			$where .= " AND data_hora >= '{$data[2]}-{$data[1]}-{$data[0]}'";
		}
		
		if(!empty($_GET['data_final']))
		{
			$data = explode("/", $_GET['data_final']);
			$where .= " AND data_hora <= '{$data[2]}-{$data[1]}-{$data[0]}'";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo("SELECT count(*) FROM acesso WHERE cod_pessoa={$id_pessoa} $where");
				
		$sql .= " $where ORDER BY b.data_hora DESC LIMIT $iniciolimit, $limite";	
		
		$db->Consulta( $sql );
		while ( $db->ProximoRegistro() )
		{
			list ($data_hora, $ip_externo) = $db->Tupla();
			
			$local = $ip_externo == '200.215.80.163' ? 'Prefeitura' : 'Externo';

			$this->addLinhas( array("<img src='imagens/noticia.jpg' border=0>$data_hora", $local ) );
		}

		/*$this->acao = "go(\"bairros_cad.php\")";
		$this->nome_acao = "Novo";*/
		
		$opcoes[""] = "Escolha uma op��o...";
		$opcoes["P"] = "Prefeitura";
		$opcoes["X"] = "Externo";
	
		$this->campoLista( "status", "Status", $opcoes, $_GET['status'] );
		
		$this->campoData("data_inicial","Data Inicial",$_GET['data_inicial']);
		$this->campoData("data_final","Data Final",$_GET['data_final']);
		
		$this->addPaginador2( "conexoes_lst.php", $total, $_GET, $this->nome, $limite );

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