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
	if( is_numeric( $_GET["ins"] ) )
	{
		$db = new clsBanco();

		// USUARIO ESCOLA
		$db->Consulta( "
		SELECT
			u.cod_usuario
			, p.nome
		FROM
			pmieducar.usuario u
			, pmieducar.tipo_usuario tu
			, cadastro.pessoa p
		WHERE
			u.ref_cod_instituicao = {$_GET["ins"]}
			AND u.cod_usuario = p.idpes
			AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
			AND u.ativo = 1
			AND tu.nivel = 4
		ORDER BY
			p.nome ASC"
		);

		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome ) = $db->Tupla();
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
		}

		// USUARIO BIBLIOTECA
		$db->Consulta( "
		SELECT
			u.cod_usuario
			, p.nome
		FROM
			pmieducar.usuario u
			, pmieducar.tipo_usuario tu
			, cadastro.pessoa p
		WHERE
			u.ref_cod_instituicao = {$_GET["ins"]}
			AND u.cod_usuario = p.idpes
			AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
			AND u.ativo = 1
			AND tu.nivel = 8
		ORDER BY
			p.nome ASC"
		);

		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome ) = $db->Tupla();
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
		}
	}
	elseif( is_numeric( $_GET["esc"] ) )
	{
		$db = new clsBanco();

		// USUARIO ESCOLA
		$db->Consulta( "
		SELECT
			u.cod_usuario
			, p.nome
		FROM
			pmieducar.usuario u
			, pmieducar.tipo_usuario tu
			, cadastro.pessoa p
		WHERE
			u.ref_cod_escola = {$_GET["esc"]}
			AND u.cod_usuario = p.idpes
			AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
			AND u.ativo = 1
			AND tu.nivel = 4
		ORDER BY
			p.nome ASC"
		);

		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome ) = $db->Tupla();
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
		}

		// USUARIO BIBLIOTECA
		$db->Consulta( "
		SELECT
			u.cod_usuario
			, p.nome
		FROM
			pmieducar.usuario u
			, pmieducar.tipo_usuario tu
			, cadastro.pessoa p
		WHERE
			u.ref_cod_escola = {$_GET["esc"]}
			AND u.cod_usuario = p.idpes
			AND u.ref_cod_tipo_usuario = tu.cod_tipo_usuario
			AND u.ativo = 1
			AND tu.nivel = 8
		ORDER BY
			p.nome ASC"
		);

		while ( $db->ProximoRegistro() )
		{
			list( $cod, $nome ) = $db->Tupla();
			echo "	<usuario cod_usuario=\"{$cod}\">{$nome}</usuario>\n";
		}
	}
	echo "</query>";
?>