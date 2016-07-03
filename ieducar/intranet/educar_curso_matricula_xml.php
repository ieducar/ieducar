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

	if( is_numeric( $_GET["alu"] ) && is_numeric( $_GET["ins"] ) )
	{
		$db = new clsBanco();
		$db->Consulta( "
		SELECT
			m.cod_matricula
			, m.ref_cod_curso
			, c.padrao_ano_escolar
		FROM
			pmieducar.matricula m
			, pmieducar.curso c
		WHERE
			m.ref_cod_aluno = '{$_GET["alu"]}'
			AND m.ultima_matricula = 1
			AND m.ativo = 1
			AND m.ref_cod_curso = c.cod_curso
			AND c.ref_cod_instituicao = '{$_GET["ins"]}'
		ORDER BY
			m.cod_matricula ASC
		");

		// caso o aluno nao tenha nenhuma matricula em determinada instituicao
		if (!$db->numLinhas())
		{
			$db->Consulta( "
			SELECT
				cod_curso
				, nm_curso
			FROM
				pmieducar.curso
			WHERE
				padrao_ano_escolar = 0
				AND ativo = 1
				AND ref_cod_instituicao = '{$_GET["ins"]}'
				AND NOT EXISTS
				(
					SELECT
						ref_cod_curso
					FROM
						pmieducar.serie
					WHERE
						ref_cod_curso = cod_curso
						AND ativo = 1
				)
			ORDER BY
				nm_curso ASC
			");

			if ($db->numLinhas())
			{
				while ( $db->ProximoRegistro() )
				{
					list( $cod, $nome ) = $db->Tupla();
					echo "	<curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
				}
			}
		} // caso o aluno tenha matricula(s) em determinada instituicao
		else
		{
			while ( $db->ProximoRegistro() )
			{
				list( $matricula, $curso, $padrao_ano_escolar ) = $db->Tupla();

				if ( $padrao_ano_escolar == 0 )
				{
					$cursos_matriculado[] = $curso;
				}
			}
//			echo "<pre>"; print_r($cursos_matriculado); die();
			if (is_array($cursos_matriculado))
			{
				$sql = "
				SELECT
					cod_curso
					, nm_curso
				FROM
					pmieducar.curso
				WHERE
					padrao_ano_escolar = 0
					AND ativo = 1
					AND ref_cod_instituicao = '{$_GET["ins"]}'
					AND NOT EXISTS
					(
						SELECT
							ref_cod_curso
						FROM
							pmieducar.serie
						WHERE
							ref_cod_curso = cod_curso
							AND ativo = 1
					)";

				if (is_array($cursos_matriculado))
				{
					foreach ($cursos_matriculado as $cursos)
						$sql .= " AND cod_curso != '{$cursos}' ";
				}

				$sql .= "
				ORDER BY
					nm_curso ASC ";

				$db->Consulta( $sql );
				if ($db->numLinhas())
				{
					while ( $db->ProximoRegistro() )
					{
						list( $cod, $nome ) = $db->Tupla();
						echo "	<curso cod_curso=\"{$cod}\">{$nome}</curso>\n";
					}
				}
			}
		}
	}
	echo "</query>";
?>