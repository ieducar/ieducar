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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Publica��es!" );
		$this->processoAp = "209";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
		$this->titulo = "Detalhe de concurso";
		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_portal_concurso = @$_GET['cod_portal_concurso'];

		$objPessoa = new clsPessoaFisica();

		$db = new clsBanco();
		$db->Consulta( "SELECT nm_concurso, descricao, data_hora, ref_ref_cod_pessoa_fj, caminho, tipo_arquivo FROM portal_concurso WHERE cod_portal_concurso = '{$cod_portal_concurso}'" );
		if ($db->ProximoRegistro())
		{
			list ( $nome, $descricao, $data, $pessoa, $caminho, $tipo ) = $db->Tupla();
			//$pessoa = $db->CampoUnico( "SELECT nm_pessoa FROM pessoa_fj WHERE cod_pessoa_fj = '$pessoa'" );
			list($pessoa) = $objPessoa->queryRapida($pessoa, "nome");

			$this->addDetalhe( array("Respons�vel", $pessoa ) );
			$this->addDetalhe( array("Data", date( "d/m/Y H:i", strtotime(substr( $data,0,19) ) ) ) );
			$this->addDetalhe( array("Nome", $nome) );
			$this->addDetalhe( array("Descri��o", $descricao) );
			$this->addDetalhe( array("Arquivo", "<a href='arquivos/$caminho''><img src='/intranet/imagens/nvp_icon_{$tipo}.gif' border='0'></a>") );
		}
		$this->url_novo = "concursos_cad.php";
		$this->url_editar = "concursos_cad.php?cod_portal_concurso=$cod_portal_concurso";
		$this->url_cancelar = "concursos_lst.php";

		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>
