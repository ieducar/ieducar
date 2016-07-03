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
	header( 'Content-type: text/xml' );

	require_once '../includes/bootstrap.php';
  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryForDisabledApi();

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";

	require_once ("include/clsBanco.inc.php");
	require_once ("include/pmiacervo/geral.inc.php");
	require_once( "include/Geral.inc.php" );

	if($_GET['ref_cod_fundo'])
	{
		$fundo = @$_GET['ref_cod_fundo'];
	}
	if($_GET['ref_cod_grupo'])
	{
		$grupo = @$_GET['ref_cod_grupo'];
	}
	if($_GET['ref_cod_serie'])
	{
		$serie = @$_GET['ref_cod_serie'];
	}

	if($serie)
	{
		$Objcaixa = new clsPmiacervoCaixa();
		$ListaCaixas = $Objcaixa->lista(null, $serie, $fundo, $grupo);
		if($ListaCaixas)
		{
			foreach ($ListaCaixas as $campo)
			{
				echo "<item>{$campo['cod_caixa']}</item>";
				echo "<item>{$campo['identificacao_caixa']}</item>";

			}
		}
	}
	elseif(!isset($_GET['ref_cod_serie']) && $grupo)
	{
		$Objserie = new clsPmiacervoSerie();
		$Listaserie = $Objserie->lista(null,$grupo, $fundo);
		if($Listaserie)
		{
			foreach ($Listaserie as $campo)
			{
				echo "<item>{$campo['cod_serie']}</item>";
				echo "<item>{$campo['sigla_serie']} - {$campo['nm_serie']}</item>";
			}
		}
	}
	elseif(!isset($_GET['ref_cod_grupo']) && $fundo)
	{
		$ObjGrupo = new clsPmiacervoGrupo();
		$ListaGrupo = $ObjGrupo->lista(null, $fundo);
		if($ListaGrupo)
		{
			foreach ($ListaGrupo as $campo)
			{
				echo "<item>{$campo['cod_grupo']}</item>";
				echo "<item>{$campo['sigla_grupo']} - {$campo['nm_grupo']}</item>";
			}
		}
	}
	echo "</query>";
?>