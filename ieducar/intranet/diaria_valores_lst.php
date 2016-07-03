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
		//$this->SetTitulo( "{$this->_instituicao} Di�ria Valores" );
		$this->processoAp = "295";
                $this->addEstilo( "localizacaoSistema" );

	}
}

class indice extends clsListagem
{
	function Gerar()
	{
		$this->titulo = "Di&aacute;rias";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
	
		$this->addCabecalhos( array( "Grupo", "Vig&ecirc;ncia", "Estadual", "100%", "75%", "50%", "25%" ) );
		
		
		$where = "";
		$gruda = "";
		if ( ! empty( $_GET['ref_sec'] ) )
		{
			$where .= "";
		}
		$db = new clsBanco();
		$db2 = new clsBanco();
		$total = $db->UnicoCampo( "SELECT count(0) FROM pmidrh.diaria_valores $where" );
		
		// Paginador
		$limite = 20;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
		
		$objPessoa = new clsPessoaFisica();
		
		$sql = "SELECT cod_diaria_valores, ref_cod_diaria_grupo, data_vigencia, estadual, p100, p75, p50, p25 FROM pmidrh.diaria_valores $where ORDER BY data_vigencia DESC, estadual ASC, ref_cod_diaria_grupo ASC";
		$db->Consulta( $sql );
		while ( $db->ProximoRegistro() )
		{
			list ( $cod_diaria_valores, $ref_cod_diaria_grupo, $data_vigencia, $estadual, $p100, $p75, $p50, $p25 ) = $db->Tupla();
			
			$nome_grupo = $db2->CampoUnico( "SELECT desc_grupo FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo = '{$ref_cod_diaria_grupo}'" );
			
			$data_vigencia = date( "d/m/Y", strtotime( $data_vigencia ) );
			
			if( strlen( $nome_grupo ) > 40 )
			{
				$nome_grupo = substr( $nome_grupo, 0, 37 );
			}
			
			$estadual = ( $estadual )? "Sim": "N�o";
			
			$this->addLinhas( array( 
			"<a href='diaria_valores_det.php?cod_diaria_valores={$cod_diaria_valores}'><img src='imagens/noticia.jpg' border=0>$nome_grupo</a>", 
			"<a href='diaria_valores_det.php?cod_diaria_valores={$cod_diaria_valores}'>$data_vigencia</a>",
			"<a href='diaria_valores_det.php?cod_diaria_valores={$cod_diaria_valores}'>$estadual</a>",
			"<a href='diaria_valores_det.php?cod_diaria_valores={$cod_diaria_valores}'>" . number_format( $p100, 2, ",", "." ) . "</a>",
			"<a href='diaria_valores_det.php?cod_diaria_valores={$cod_diaria_valores}'>" . number_format( $p75, 2, ",", "." ) . "</a>",
			"<a href='diaria_valores_det.php?cod_diaria_valores={$cod_diaria_valores}'>" . number_format( $p50, 2, ",", "." ) . "</a>",
			"<a href='diaria_valores_det.php?cod_diaria_valores={$cod_diaria_valores}'>" . number_format( $p25, 2, ",", "." ) . "</a>" ) );
		}
		
		// Paginador
		$this->addPaginador2( "diaria_valores_lst.php", $total, $_GET, $this->nome, $limite );
		
		$this->acao = "go(\"diaria_valores_cad.php\")";
		$this->nome_acao = "Novo";

		$this->largura = "100%";
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos(array($_SERVER['SERVER_NAME'] . '/intranet' => 'i-Educar', '' => 'Di�ria Valores'));
                $this->enviaLocalizacao($localizacao->montar());
	}
}
$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>