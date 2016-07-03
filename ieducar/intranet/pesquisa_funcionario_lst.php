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
/**
 * Os par�metros passados para esta p�gina de listagem devem estar dentro da classe clsParametrosPesquisas.inc.php
 *
 * @author Adriano Erik Weiguert Nagasava
 *
 */

$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ( "include/Geral.inc.php" );
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Pesquisa por Funcion&aacute;rio!" );
		$this->processoAp         = "0";
		$this->renderMenu         = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsListagem
{

	var $chave_campo;
	var $importarCpf;

	function Gerar()
	{
		@session_start();
		$id_pessoa  = $_SESSION['id_pessoa'];
		$this->nome = "form1";

		if ( $_GET["campos"] ) {
			$parametros							 = new clsParametrosPesquisas();
			$parametros->deserializaCampos( $_GET["campos"] );
			$_SESSION['campos'] 				 = $parametros->geraArrayComAtributos();
			unset( $_GET["campos"] );
		}
		else {
			$parametros							 = new clsParametrosPesquisas();
			$parametros->preencheAtributosComArray( $_SESSION['campos'] );
		}
		@session_write_close();
		$submit = false;

		$this->addCabecalhos( array( "Matr&iacute;cula", "Funcion&aacute;rio" ) );

		// Filtros de Busca
		$this->campoTexto( "campo_busca", "Funcion�rio", "", 50, 255, false, false, false, "Matr�cula/Nome do Funcion�rio" );
		$this->campoOculto("com_matricula",$_GET['com_matricula']);

		if ( $_GET['campo_busca'] )
			$chave_busca = @$_GET['campo_busca'];

		if ( $_GET['busca'] )
			$busca       = @$_GET['busca'];

		// Paginador
		$limite      = 10;
		$iniciolimit = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"] * $limite - $limite: 0;

		$this->chave_campo = $_GET['chave_campo'];
		$this->campoOculto("chave_campo", $this->chave_campo);
		if(is_numeric($this->chave_campo))
				$chave = "[$this->chave_campo]";
			else
				$chave = "";

		$this->importarCpf = $_GET['importa_cpf'];

		if($_GET['com_matricula'])
			$com_matricula = null;
		else
			$com_matricula = true;

		if ( $busca == 'S' ) {

			$obj_funcionario = new clsFuncionario();
			$lst_funcionario = $obj_funcionario->lista( false, $chave_busca, false, false, false, false, false, $iniciolimit, $limite, false, $com_matricula );

			if ( !$lst_funcionario )
			{
				$lst_funcionario = $obj_funcionario->lista( $chave_busca, false, false, false, false, false, false, $iniciolimit, $limite, false, $com_matricula );
			}
		}
		else {
			$obj_funcionario = new clsFuncionario();
			$lst_funcionario = $obj_funcionario->lista( false, false, false, false, false, false, false, $iniciolimit, $limite, false, $com_matricula );
		}
		if ( $lst_funcionario ) {
			foreach ( $lst_funcionario as $funcionario ) {
				$funcao  = " set_campo_pesquisa(";
				$virgula = "";
				$cont    = 0;

				foreach ( $parametros->getCampoNome() as $campo ){
					if ( $parametros->getCampoTipo( $cont ) == "text" ) {
						if ( $parametros->getCampoValor( $cont ) == "cpf" )
						{
							if($this->importarCpf || $busca)
							{
								$objPessoa = new clsPessoaFisica($funcionario["ref_cod_pessoa_fj"]);
								$objPessoa_det = $objPessoa->detalhe();
								$funcionario[$parametros->getCampoValor( $cont )] = $objPessoa_det["cpf"];
							}
							$funcionario[$parametros->getCampoValor( $cont )] = int2CPF( $funcionario[$parametros->getCampoValor( $cont )] );
						}
						$funcao .= "{$virgula} '{$campo}{$chave}', '{$funcionario[$parametros->getCampoValor( $cont )]}'";
						$virgula = ",";
					}
					elseif ( $parametros->getCampoTipo( $cont ) == "select" )
					{
						if ( $parametros->getCampoValor( $cont ) == "cpf" )
						{
							if($this->importarCpf || $busca)
							{
								$objPessoa = new clsPessoaFisica($funcionario["ref_cod_pessoa_fj"]);
								$objPessoa_det = $objPessoa->detalhe();
								$funcionario[$parametros->getCampoValor( $cont )] = $objPessoa_det["cpf"];
							}

							$funcionario[$parametros->getCampoValor( $cont )] = int2CPF( $funcionario[$parametros->getCampoValor( $cont )] );
						}
						$funcao .= "{$virgula} '{$campo}{$chave}', '{$funcionario[$parametros->getCampoIndice( $cont )]}', '{$funcionario[$parametros->getCampoValor( $cont )]}'";
						$virgula = ",";
					}
					$cont++;
				}
				if ( $parametros->getSubmit() )
					$funcao .= "{$virgula} 'submit' )";
				else
					$funcao .= " )";
				$this->addLinhas( array( "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$funcionario["matricula"]}</a>", "<a href='javascript:void( 0 );' onclick=\"javascript:{$funcao}\">{$funcionario["nome"]}</a>" ) );
				$total = $funcionario['_total'];
			}
		}
		// Paginador
		$this->addPaginador2( "pesquisa_funcionario_lst.php", $total, $_GET, $this->nome, $limite );

		// Define Largura da P�gina
		$this->largura = "100%";
	}
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>

<script type="text/javascript">
/*
try{
	window.onload = setTimeout("document.forms[0].elements[1].focus()", 1000);//setFocus('campo_busca');
}catch(e){
	console.log(e);
}
*/
</script>