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

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Publica��es!" );
		$this->processoAp = "209";
	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Concursos";
		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array( "Concurso", "Descri��o" ) );

		$db = new clsBanco();
		$dba = new clsBanco();

		// Paginador
		$this->limite = 10;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;


		$total = $dba->UnicoCampo( "SELECT count(0) FROM portal_concurso" );

		$db->Consulta( "SELECT cod_portal_concurso, nm_concurso, descricao FROM portal_concurso ORDER BY data_hora DESC limit $this->limite offset $this->offset " );
		while ($db->ProximoRegistro())
		{
			list ( $cod, $nm_concurso, $descricao ) = $db->Tupla();
			$this->addLinhas( array( "<a href='concursos_det.php?cod_portal_concurso={$cod}'><img src='imagens/noticia.jpg' border=0>$nm_concurso</a>", $descricao ) );
		}

		$this->addPaginador2( "concursos_lst.php", $total, $_GET, $this->nome, $this->limite );

		$this->acao = "go(\"concursos_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
