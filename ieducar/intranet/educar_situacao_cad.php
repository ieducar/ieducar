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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Situa&ccedil;&atilde;o" );
		$this->processoAp = "602";
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

	var $cod_situacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_situacao;
	var $permite_emprestimo;
	var $descricao;
	var $situacao_padrao;
	var $situacao_emprestada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $ref_cod_biblioteca_;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_situacao=$_GET["cod_situacao"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 602, $this->pessoa_logada, 11,  "educar_situacao_lst.php" );

		$this->ref_cod_biblioteca = $this->ref_cod_biblioteca_ = $obj_permissoes->getBiblioteca($this->pessoa_logada);
		if( is_numeric( $this->cod_situacao ) )
		{

			$obj = new clsPmieducarSituacao( $this->cod_situacao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if ($this->cod_situacao)
				{
					$obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
					$det_biblioteca = $obj_biblioteca->detalhe();
					$this->ref_cod_instituicao = $det_biblioteca["ref_cod_instituicao"];
					$this->ref_cod_escola = $det_biblioteca["ref_cod_escola"];
					$this->ref_cod_biblioteca = $this->ref_cod_biblioteca_ = $this->ref_cod_biblioteca;

				}

				if( $obj_permissoes->permissao_excluir( 602, $this->pessoa_logada, 11 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_situacao_det.php?cod_situacao={$registro["cod_situacao"]}" : "educar_situacao_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_situacao", $this->cod_situacao );

		// foreign keys
		$get_escola     = 1;
		$escola_obrigatorio = false;
		$get_biblioteca = 1;
		$instituicao_obrigatorio = true;
		$biblioteca_obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		//-------------- JS para os Check --------------//
		//if (!$this->cod_situacao)
	//	{
			/*$todas_situacoes = "situacao = new Array();\n";
			$obj_biblioteca = new clsPmieducarSituacao();
			$lista = $obj_biblioteca->lista(null,null,null,null,null,null,null,null,null,null,null,null,1);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$todas_situacoes .= "situacao[situacao.length] = new Array( {$registro["cod_situacao"]}, {$registro['situacao_padrao']}, {$registro['situacao_emprestada']}, {$registro['ref_cod_biblioteca']});\n";
				}
			}
			echo "<script>{$todas_situacoes}</script>";*/
	//	}

		// text
		$this->campoTexto( "nm_situacao", "Situa&ccedil;&atilde;o", $this->nm_situacao, 30, 255, true );

		$opcoes = array("" => "Selecione", 1 => "n&atilde;o", 2 => "sim" );
		$this->campoLista( "permite_emprestimo", "Permite Empr&eacute;stimo", $opcoes, $this->permite_emprestimo);
		$this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );

		$obj_situacao = new clsPmieducarSituacao();
		if($this->ref_cod_biblioteca_)
			$lst_situacao = $obj_situacao->lista(null,null,null,null,null,null,1,null,null,null,null,null,1,$this->ref_cod_biblioteca_,null,null);

		if ($lst_situacao)
		{	//echo "<pre>";

			$achou = false;
			//print_r($lst_situacao);die;
			foreach ($lst_situacao as $situacao){
				if($situacao['cod_situacao'] == $this->cod_situacao)
					$achou = true;
			}
			if(!$achou)
				$script .="setVisibility('tr_situacao_padrao',false);\n";
			//$lista = array_shift($lst_situacao);
			//$situacao = $lista["cod_situacao"];
			//$biblioteca = $lista["ref_cod_biblioteca"];

		}
		$this->campoCheck( "situacao_padrao", "Situa&ccedil;&atilde;o Padr&atilde;o", $this->situacao_padrao );
		//if (!isset($lst_situacao) || $this->cod_situacao == $situacao || $this->ref_cod_biblioteca != $biblioteca)
		//if (!$this->cod_situacao)
			//$this->campoCheck( "situacao_padrao", "Situa&ccedil;&atilde;o Padr&atilde;o", $this->situacao_padrao );

		//$lst_situacao = $obj_situacao->lista(null,null,null,null,null,null,null,1,null,null,null,null,1,$this->ref_cod_biblioteca,$this->ref_cod_instituicao,$this->ref_cod_escola);
	/*	if ($lst_situacao)
		{
			$lista = array_shift($lst_situacao);
			$situacao = $lista["cod_situacao"];
			$biblioteca = $lista["ref_cod_biblioteca"];
		}*/
		//if (!isset($lst_situacao) || $this->cod_situacao == $situacao || $this->ref_cod_biblioteca != $biblioteca)

		$obj_situacao = new clsPmieducarSituacao();
		if($this->ref_cod_biblioteca_)
			$lst_situacao = $obj_situacao->lista(null,null,null,null,null,null,null,1,null,null,null,null,1,$this->ref_cod_biblioteca_,null,null);

		if ($lst_situacao)
		{
			$achou = false;
			foreach ($lst_situacao as $situacao){
				if($situacao['cod_situacao'] == $this->cod_situacao)
					$achou = true;
			}
			//$lista = array_shift($lst_situacao);
			//$situacao = $lista["cod_situacao"];
			//$biblioteca = $lista["ref_cod_biblioteca"];
			if(!$achou)
				$script .="setVisibility('tr_situacao_emprestada',false);\n";

		}

		if($script)
			echo "<script>window.onload=function(){{$script}}</script>";
		$this->campoCheck( "situacao_emprestada", "Situa&ccedil;&atilde;o Emprestada", $this->situacao_emprestada );
		//if ($this->situacao_emprestada)
			//$this->campoCheck( "situacao_emprestada", "Situa&ccedil;&atilde;o Emprestada", $this->situacao_emprestada );

		$this->acao_enviar = "valida()";
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 602, $this->pessoa_logada, 11,  "educar_situacao_lst.php" );

    $this->situacao_padrao = is_null($this->situacao_padrao) ? 0 : 1;
    $this->situacao_emprestada = is_null($this->situacao_emprestada) ? 0 : 1;

		$obj = new clsPmieducarSituacao( null, null, $this->pessoa_logada, $this->nm_situacao, $this->permite_emprestimo, $this->descricao, $this->situacao_padrao, $this->situacao_emprestada, null, null, 1, $this->ref_cod_biblioteca );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_situacao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarSituacao\nvalores obrigat&oacute;rios\nis_numeric( $this->pessoa_logada ) && is_string( $this->nm_situacao ) && is_numeric( $this->permite_emprestimo ) && is_numeric( $this->situacao_padrao ) && is_numeric( $this->situacao_emprestada ) && is_numeric( $this->ref_cod_biblioteca )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 602, $this->pessoa_logada, 11,  "educar_situacao_lst.php" );

    $this->situacao_padrao = is_null($this->situacao_padrao) ? 0 : 1;
    $this->situacao_emprestada = is_null($this->situacao_emprestada) ? 0 : 1;

		$obj = new clsPmieducarSituacao($this->cod_situacao, $this->pessoa_logada, null, $this->nm_situacao, $this->permite_emprestimo, $this->descricao, $this->situacao_padrao, $this->situacao_emprestada, null, null, 1, $this->ref_cod_biblioteca);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_situacao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarSituacao\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_situacao ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 602, $this->pessoa_logada, 11,  "educar_situacao_lst.php" );


		$obj = new clsPmieducarSituacao($this->cod_situacao, $this->pessoa_logada, null,null,null,null,null,null,null,null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_situacao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarSituacao\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_situacao ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
<script>

document.getElementById('ref_cod_biblioteca').onchange = function()
{
//	getSituacao();
	var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

	var xml_situacao = new ajax( getSituacao );
	xml_situacao.envia( "educar_situacao_xml.php?bib="+campoBiblioteca );
}

function getSituacao(xml_situacao)
{
	/*
	var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

	setVisibility('tr_situacao_padrao',true);
	setVisibility('tr_situacao_emprestada',true);

	for (var j = 0; j < situacao.length; j++)
	{
		if (situacao[j][3] == campoBiblioteca)
		{
			if (situacao[j][1] == 1) //jah existe uma situacao padrao
				setVisibility('tr_situacao_padrao',false);

			if (situacao[j][2] == 1) //jah existe uma situacao emprestada
				setVisibility('tr_situacao_emprestada',false);
		}
	}
	*/
	setVisibility('tr_situacao_padrao',true);
	setVisibility('tr_situacao_emprestada',true);

	var DOM_array = xml_situacao.getElementsByTagName( "situacao" );

	if(DOM_array.length)
	{
		for( var i = 0; i < DOM_array.length; i++ )
		{
			if (DOM_array[i].getAttribute("situacao_padrao") == 1) //jah existe uma situacao padrao
				setVisibility('tr_situacao_padrao',false);

			if (DOM_array[i].getAttribute("situacao_emprestada") == 1) //jah existe uma situacao emprestada
				setVisibility('tr_situacao_emprestada',false);
		}
	}
}

function valida()
{
	var campoPadrao = document.getElementById('situacao_padrao').checked;
	var campoEmprestada = document.getElementById('situacao_emprestada').checked;

	if( campoPadrao == true && campoEmprestada == true)
	{
		alert("N�o � permitido setar ao mesmo tempo os campos \n 'Situa��o Padr�o' e 'Situa��o Emprestada'!");
		return false;
	}

	if(!acao())
		return;
	document.forms[0].submit();
}

</script>
