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

	require_once( "include/clsBanco.inc.php" );
	require_once( "include/funcoes.inc.php" );

  require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
  Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"sugestoes\">\n";
	if( is_numeric( $_GET["inst"]  ) && is_numeric( $_GET["esc"]  ) && is_numeric( $_GET["com"]) && is_numeric( $_GET['cur'])&& is_numeric( $_GET['ser'])  )
	{
		if(is_numeric( $_GET["not_tur"]))
			$not_turma = " AND t.cod_turma != {$_GET['not_tur']} ";

		$db = new clsBanco();
		$consulta = "SELECT to_char(hora_inicial,'hh24:mm') as hora_inicial
							,to_char(hora_final,'hh24:mm')  as hora_final
 				 	   FROM pmieducar.turma t
				 	  WHERE t.ref_cod_instituicao = {$_GET["inst"]}
				 	    AND t.ref_ref_cod_escola = {$_GET['esc']}
				 	    AND t.ref_ref_cod_serie = {$_GET['ser']}
				 	    AND t.ref_cod_infra_predio_comodo = {$_GET['com']}
				 	    AND t.ref_cod_curso = {$_GET['cur']}
				 	   	$not_turma
					    AND t.ativo = 1
					  ORDER BY 2
						";

		$db->Consulta( $consulta );
		while ( $db->ProximoRegistro() )
		{
			list( $hora_inicial,$hora_final) = $db->Tupla();
			echo "	<item>{$hora_inicial}</item>\n";
			echo "	<item>{$hora_final}</item>\n";

		}
	}
	echo "</query>";
?>