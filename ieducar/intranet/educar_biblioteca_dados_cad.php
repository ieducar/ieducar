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
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Dados Biblioteca" );
		$this->processoAp = "629";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $cod_biblioteca;
	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $nm_biblioteca;
	var $valor_multa;
	var $max_emprestimo;
	var $valor_maximo_multa;
	var $data_cadastro;
	var $data_exclusao;
	var $requisita_senha;
	var $ativo;
	var $dias_espera;

	var $dias_da_semana = array( '' => 'Selecione', 1 => 'Domingo', 2 => 'Segunda', 3 => 'Ter&ccedil;a', 4 => 'Quarta', 5 => 'Quinta', 6 => 'Sexta', 7 => 'S&aacute;bado' );
	var $dia;
	var $biblioteca_dia_semana;
	var $incluir_dia_semana;
	var $excluir_dia_semana;

	var $nm_feriado;
	var $data_feriado;
	var $biblioteca_feriado;
	var $incluir_feriado;
	var $excluir_feriado;

	var $tombo_automatico;

	function Inicializar()
	{
//		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_biblioteca=$_GET["cod_biblioteca"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 629, $this->pessoa_logada, 11,  "educar_biblioteca_dados_lst.php" );

		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if($nivel_usuario <= 3)
			$permitido = true;
		else{
			$obj_usuario_bib = new clsPmieducarBibliotecaUsuario();
			$lista_bib = $obj_usuario_bib->lista(null,$this->pessoa_logada);
			$permitido = false;
			if($lista_bib)
			{
				foreach ($lista_bib as $biblioteca)
				{
					if($this->cod_biblioteca == $biblioteca['ref_cod_biblioteca'])
						$permitido = true;
				}
			}
		}

		if( !$permitido)
			header( "Location: educar_biblioteca_dados_lst.php" );
		if( is_numeric( $this->cod_biblioteca ) )
		{

			$obj = new clsPmieducarBiblioteca( $this->cod_biblioteca );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if( $obj_permissoes->permissao_excluir( 629, $this->pessoa_logada, 11 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_biblioteca_dados_det.php?cod_biblioteca={$registro["cod_biblioteca"]}" : "educar_biblioteca_dados_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_biblioteca", $this->cod_biblioteca );

		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;

		// foreign keys

		// text
		$this->campoTexto( "nm_biblioteca", "Biblioteca", $this->nm_biblioteca, 30, 255, true,false,false,"","","","",true );
		$this->campoMonetario( "valor_multa", "Valor Multa", $this->valor_multa, 8, 8, true );
		$this->campoNumero( "max_emprestimo", "M&aacute;ximo Empr&eacute;stimo", $this->max_emprestimo, 8, 8, true );
		$this->campoMonetario( "valor_maximo_multa", "Valor M&aacute;ximo Multa", $this->valor_maximo_multa, 8, 8, true );

//		$opcoes = array( "" => "Selecione", 1 => "n&atilde;o", 2 => "sim" );
//		$this->campoLista( "requisita_senha", "Requisita Senha", $opcoes, $this->requisita_senha );
		$this->campoCheck( "requisita_senha", "Requisita Senha", $this->requisita_senha );
		$this->campoNumero( "dias_espera", "Dias Espera", $this->dias_espera, 2, 2, true );

	//-----------------------INCLUI DIA SEMANA------------------------//
		$this->campoQuebra();

		if ( $_POST["biblioteca_dia_semana"] )
			$this->biblioteca_dia_semana = unserialize( urldecode( $_POST["biblioteca_dia_semana"] ) );
		if( is_numeric( $this->cod_biblioteca ) && !$_POST )
		{
			$obj = new clsPmieducarBibliotecaDia();
			$registros = $obj->lista( $this->cod_biblioteca );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$this->biblioteca_dia_semana["dia_"][] = $campo["dia"];
				}
			}
		}
		if ( $_POST["dia"] )
		{
			$this->biblioteca_dia_semana["dia_"][] = $_POST["dia"];
			unset( $this->dia );
		}

		$this->campoOculto( "excluir_dia_semana", "" );
		unset($aux);

		if ( $this->biblioteca_dia_semana )
		{
			foreach ( $this->biblioteca_dia_semana as $key => $campo )
			{
				if($campo)
				{
					foreach ($campo as $chave => $dias)
					{
						if ( $this->excluir_dia_semana == $dias )
						{
							$this->biblioteca_dia_semana[$chave] = null;
							$this->excluir_dia_semana = null;
						}
						else
						{
							$this->campoTextoInv( "dia_{$dias}", "", $this->dias_da_semana[$dias], 8, 8, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_dia_semana').value = '{$dias}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );
							$aux["dia_"][] = $dias;
						}
					}

				}
			}
			unset($this->biblioteca_dia_semana);
			$this->biblioteca_dia_semana = $aux;
		}

		$this->campoOculto( "biblioteca_dia_semana", serialize( $this->biblioteca_dia_semana ) );

		$opcoes = $this->dias_da_semana;

		if ( $aux )
			$this->campoLista( "dia", "Dia da Semana", $opcoes, $this->dia,"",false,"","<a href='#' onclick=\"getElementById('incluir_dia_semana').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>",false,false );
		else
			$this->campoLista( "dia", "Dia da Semana", $opcoes, $this->dia,"",false,"","<a href='#' onclick=\"getElementById('incluir_dia_semana').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>" );

		$this->campoOculto( "incluir_dia_semana", "" );
//		$this->campoRotulo( "bt_incluir_dia_semana", "Dia da Semana", "<a href='#' onclick=\"getElementById('incluir_dia_semana').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_incluir2.gif' title='Incluir' border=0></a>" );

		$this->campoQuebra();
	//-----------------------FIM INCLUI DIA SEMANA------------------------//

	//-----------------------INCLUI FERIADO------------------------//
		$this->campoQuebra();

		if ( $_POST["biblioteca_feriado"] )
			$this->biblioteca_feriado = unserialize( urldecode( $_POST["biblioteca_feriado"] ) );
		if( is_numeric( $this->cod_biblioteca ) && !$_POST )
		{
			$obj = new clsPmieducarBibliotecaFeriados();
			$registros = $obj->lista( null, $this->cod_biblioteca );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$aux["nm_feriado_"]= $campo["nm_feriado"];
					$aux["data_feriado_"]= dataFromPgToBr($campo["data_feriado"]);
					$this->biblioteca_feriado[] = $aux;
				}
			}
		}

		unset($aux);

		if ( $_POST["nm_feriado"] && $_POST["data_feriado"] )
		{
			$aux["nm_feriado_"] = $_POST["nm_feriado"];
			$aux["data_feriado_"] = $_POST["data_feriado"];
			$this->biblioteca_feriado[] = $aux;
			unset( $this->nm_feriado );
			unset( $this->data_feriado );
		}

		$this->campoOculto( "excluir_feriado", "" );
		unset($aux);

		if ( $this->biblioteca_feriado )
		{
			foreach ( $this->biblioteca_feriado as $key => $feriado)
			{
				if ( $this->excluir_feriado == $feriado["nm_feriado_"] )
				{
					unset($this->biblioteca_feriado[$key]);
					unset($this->excluir_feriado);
				}
				else
				{
					$this->campoTextoInv( "nm_feriado_{$feriado["nm_feriado_"]}", "", $feriado["nm_feriado_"], 30, 255, false, false, true );
					$this->campoTextoInv( "data_feriado_{$feriado["nm_feriado_"]}", "", $feriado['data_feriado_'], 10, 10, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_feriado').value = '{$feriado["nm_feriado_"]}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );
					$aux["nm_feriado_"] = $feriado["nm_feriado_"];
					$aux["data_feriado_"] = $feriado['data_feriado_'];
				}
			}
		}
		$this->campoOculto( "biblioteca_feriado", serialize( $this->biblioteca_feriado ) );


		$this->campoTexto( "nm_feriado", "Feriado", $this->nm_feriado, 30, 255 );
		$this->campoData( "data_feriado", " Data Feriado", $this->data_feriado );

		$this->campoOculto( "incluir_feriado", "" );
		$this->campoRotulo( "bt_incluir_feriado", "Feriado", "<a href='#' onclick=\"getElementById('incluir_feriado').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>" );

		$this->campoQuebra();
	//-----------------------FIM INCLUI FERIADO------------------------//
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 629, $this->pessoa_logada, 11,  "educar_biblioteca_dados_lst.php" );

		$this->valor_multa = str_replace(".","",$this->valor_multa);
		$this->valor_multa = str_replace(",",".",$this->valor_multa);
		$this->valor_maximo_multa = str_replace(".","",$this->valor_maximo_multa);
		$this->valor_maximo_multa = str_replace(",",".",$this->valor_maximo_multa);

    $this->requisita_senha = is_null($this->requisita_senha) ? 0 : 1;

		$obj = new clsPmieducarBiblioteca( $this->cod_biblioteca, null, null, null, $this->valor_multa, $this->max_emprestimo, $this->valor_maximo_multa, null, null, $this->requisita_senha, 1, $this->dias_espera, $this->tombo_automatico );
		$editou = $obj->edita();
		if( $editou )
		{
		//-----------------------EDITA DISCIPLINA------------------------//
			$obj  = new clsPmieducarBibliotecaDia( $this->cod_biblioteca );
			$excluiu = $obj->excluirTodos();
			if ( $excluiu )
			{
				$this->biblioteca_dia_semana = unserialize( urldecode( $this->biblioteca_dia_semana ) );
				if ($this->biblioteca_dia_semana)
				{
					foreach ( $this->biblioteca_dia_semana AS $campo )
					{
						for ($i = 0; $i < sizeof($campo) ; $i++)
						{
							$obj = new clsPmieducarBibliotecaDia( $this->cod_biblioteca, $campo[$i] );
							$cadastrou1  = $obj->cadastra();
							if ( !$cadastrou1 )
							{
								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarBibliotecaDia\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_biblioteca ) && is_numeric( {$campo[$i]} ) \n-->";
								return false;
							}
						}
					}
				}
			}
		//-----------------------FIM EDITA DISCIPLINA------------------------//

		//-----------------------EDITA FERIADO------------------------//
			$obj  = new clsPmieducarBibliotecaFeriados();
			$excluiu = $obj->excluirTodos($this->cod_biblioteca);
			if ( $excluiu )
			{
				$this->biblioteca_feriado = unserialize( urldecode( $this->biblioteca_feriado ) );
				if ($this->biblioteca_feriado)
				{
					foreach ( $this->biblioteca_feriado AS $feriado )
					{
						$feriado["data_feriado_"] = dataToBanco($feriado["data_feriado_"]);
						$obj = new clsPmieducarBibliotecaFeriados( null, $this->cod_biblioteca, $feriado["nm_feriado_"], null, $feriado["data_feriado_"], null, null, 1 );
						$cadastrou2  = $obj->cadastra();
						if ( !$cadastrou2 )
						{
							$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
							echo "<!--\nErro ao cadastrar clsPmieducarBibliotecaFeriados\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_biblioteca ) && is_string( {$feriado["nm_feriado_"]} ) && is_string( {$feriado["data_feriado_"]} )\n-->";
							return false;
						}
					}
				}
			}
		//-----------------------FIM EDITA FERIADO------------------------//
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_biblioteca_dados_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarBiblioteca\nvalores obrigatorios\nif( is_numeric( $this->cod_biblioteca ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 629, $this->pessoa_logada, 11,  "educar_biblioteca_dados_lst.php" );


		$obj = new clsPmieducarBiblioteca($this->cod_biblioteca, null, null, null, "NULL", "NULL", "NULL", null,null, "NULL", 1, "NULL");
		$editou = $obj->edita();
		if( $editou )
		{
			$obj  = new clsPmieducarBibliotecaDia( $this->cod_biblioteca );
			$excluiu1 = $obj->excluirTodos();
			if ( $excluiu1 )
			{
				$obj  = new clsPmieducarBibliotecaFeriados();
				$excluiu2 = $obj->excluirTodos($this->cod_biblioteca);
				if ( $excluiu2 )
				{
					$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_biblioteca_dados_lst.php" );
					die();
					return true;
				}
			}
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarBiblioteca\nvalores obrigatorios\nif( is_numeric( $this->cod_biblioteca ) )\n-->";
		return false;
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
