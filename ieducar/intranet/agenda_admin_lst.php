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
		$this->SetTitulo( "{$this->_instituicao} Agenda" );
		$this->processoAp = "343";
                $this->addEstilo( "localizacaoSistema" );
	}
}

class indice extends clsListagem
{
	var $cd_agenda;
	var $nm_agenda;

	function Gerar()
	{
		$this->titulo = "Agendas Admin";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
	
		$this->addCabecalhos( array( "Agenda" ) );
		
		$this->campoTexto('pesquisa', 'Agenda', '', 50, 255);
				
		$where = "";
		
		if (!empty($_GET['pesquisa']))
		{
			$pesquisa = str_replace(' ', '%', $_GET['pesquisa']);
			$where = "WHERE nm_agenda ILIKE '%{$pesquisa}%'";
			$pesquisa = str_replace('%', ' ', $_GET['pesquisa']);
		}

		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) FROM portal.agenda {$where}" );
		
		// Paginador
		$limite = 15;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		
		$sql = "SELECT cod_agenda, nm_agenda, ref_ref_cod_pessoa_own FROM agenda {$where} ORDER BY nm_agenda ASC LIMIT $limite OFFSET $iniciolimit";
		
		$db2 = new clsBanco();
		$db2->Consulta( $sql );
		while ( $db2->ProximoRegistro() )
		{
			list ( $cod_agenda, $nm_agenda, $cod_pessoa_own ) = $db2->Tupla();
			$this->addLinhas( array( "<a href='agenda_admin_det.php?cod_agenda={$cod_agenda}'><img src='imagens/noticia.jpg' border=0>$nm_agenda</a>"));
		}
		
		// Paginador
		$this->addPaginador2( "agenda_admin_lst.php", $total, $_GET, $this->nome, $limite );
		
		$this->acao = "go(\"agenda_admin_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    ""                  => "Agenda Admin"
                ));
                $this->enviaLocalizacao($localizacao->montar());
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>