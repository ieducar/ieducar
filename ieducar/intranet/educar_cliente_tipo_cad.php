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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Cliente" );
		$this->processoAp = "596";
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

	var $cod_cliente_tipo;
	var $ref_cod_biblioteca;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;
	var $ref_cod_escola;

	var $ref_cod_exemplar_tipo;
	var $dias_emprestimo;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_cliente_tipo=$_GET["cod_cliente_tipo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 596, $this->pessoa_logada, 11,  "educar_cliente_tipo_lst.php" );

		if( is_numeric( $this->cod_cliente_tipo ) )
		{

			$obj = new clsPmieducarClienteTipo( $this->cod_cliente_tipo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
				$obj_det = $obj_biblioteca->detalhe();
				$this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
				$this->ref_cod_escola = $obj_det["ref_cod_escola"];

				if( $obj_permissoes->permissao_excluir( 596, $this->pessoa_logada, 11 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_cliente_tipo_det.php?cod_cliente_tipo={$registro["cod_cliente_tipo"]}" : "educar_cliente_tipo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_cliente_tipo", $this->cod_cliente_tipo );

		if ($this->cod_cliente_tipo)
		{
			$instituicao_desabilitado = true;
			$escola_desabilitado = true;
			$biblioteca_desabilitado = true;
		}

		// foreign keys
		$get_escola     = 1;
		$escola_obrigatorio = false;
		$get_biblioteca = 1;
		$instituicao_obrigatorio = true;
		$biblioteca_obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_tipo", "Tipo Cliente", $this->nm_tipo, 30, 255, true );
		$this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );

		//-----------------------INICIO EXEMPLAR TIPO------------------------//

		if( class_exists( "clsPmieducarExemplarTipo" ) )
		{
			$opcoes = array( "" => "Selecione" );
			//$todos_tipos_exemplares = "tipo_exemplar = new Array();\n";
			$script .= "var editar_ = 0;\n";
			if($_GET['cod_cliente_tipo'])
			{
				$script .= "editar_ = {$_GET['cod_cliente_tipo']};\n";
			}
			/*$objTemp = new clsPmieducarExemplarTipo();
			$objTemp->setOrderby("nm_tipo ASC");
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					if ($this->cod_cliente_tipo && $registro["cod_exemplar_tipo"])
					{
						$obj_clt_tp_exp_tp = new clsPmieducarClienteTipoExemplarTipo( $this->cod_cliente_tipo, $registro["cod_exemplar_tipo"] );
						$det_clt_tp_exp_tp = $obj_clt_tp_exp_tp->detalhe();
						$dias_emprestimo = $det_clt_tp_exp_tp["dias_emprestimo"];
						if($dias_emprestimo)
						{
							$todos_tipos_exemplares .= "tipo_exemplar[tipo_exemplar.length] = new Array({$registro["cod_exemplar_tipo"]},'{$registro["nm_tipo"]}', {$registro["ref_cod_biblioteca"]}, {$dias_emprestimo});\n";
						}
					}
					else
						$todos_tipos_exemplares .= "tipo_exemplar[tipo_exemplar.length] = new Array({$registro["cod_exemplar_tipo"]},'{$registro["nm_tipo"]}', {$registro["ref_cod_biblioteca"]});\n";
				}

			}
			*/
			echo "<script>{$script}</script>";


			// se o caso � EDITAR
			if ($this->ref_cod_biblioteca)
			{
				$objTemp = new clsPmieducarExemplarTipo();
				$objTemp->setOrderby("nm_tipo ASC");
				$lista = $objTemp->lista(null,$this->ref_cod_biblioteca,null,null,null,null,null,null,null,null,1);
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_exemplar_tipo']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarExemplarTipo n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoRotulo( "div_exemplares", "Tipo Exemplar", "<div id='exemplares'></div>" );
		$this->acao_enviar = "Valida();";
		//-----------------------FIM EXEMPLAR TIPO------------------------//
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 596, $this->pessoa_logada, 11,  "educar_cliente_tipo_lst.php" );

		$array_tipos = array();
		foreach ( $_POST AS $key => $exemplar_tipo )
		{
			if(substr($key, 0, 5) == "tipo_")
			{
				$array_tipos[substr($key, 5)] = $exemplar_tipo;
			}
		}

		$obj = new clsPmieducarClienteTipo( null, $this->ref_cod_biblioteca, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{

		//-----------------------CADASTRA EXEMPLAR TIPO------------------------//
			if ($array_tipos)
			{
				foreach ( $array_tipos AS $exemplar_tipo => $dias_emprestimo )
				{
					$obj = new clsPmieducarClienteTipoExemplarTipo( $cadastrou, $exemplar_tipo, $dias_emprestimo );
					$cadastrou2  = $obj->cadastra();
					if ( !$cadastrou2 )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarClienteTipoExemplarTipo\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$exemplar_tipo} ) && is_numeric( {$dias_emprestimo} )\n-->";
						return false;
					}
				}
			}
		//-----------------------FIM CADASTRA EXEMPLAR TIPO------------------------//

			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_cliente_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarClienteTipo\nvalores obrigatorios\nis_numeric( $this->ref_cod_biblioteca ) && is_numeric( $this->pessoa_logada ) && is_string( $this->nm_tipo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 596, $this->pessoa_logada, 11,  "educar_cliente_tipo_lst.php" );

		$array_tipos = array();
		foreach ( $_POST AS $key => $exemplar_tipo )
		{
			if(substr($key, 0, 5) == "tipo_")
			{
				$array_tipos[substr($key, 5)] = $exemplar_tipo;
			}
		}

		$obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo, $this->ref_cod_biblioteca, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1);
		$editou = $obj->edita();
		if( $editou )
		{

		//-----------------------EDITA EXEMPLAR TIPO------------------------//
			if ($array_tipos)
			{
				foreach ( $array_tipos AS $exemplar_tipo => $dias_emprestimo )
				{
					$obj = new clsPmieducarClienteTipoExemplarTipo( $this->cod_cliente_tipo, $exemplar_tipo, $dias_emprestimo );

          if($obj->existe() == false)
  					$result = $obj->cadastra();
          else
  					$result = $obj->edita();

					if (! $result)
					{
						$this->mensagem = "Aparentemente ocorreu um erro ao gravar os dias de emprestimo.<br>";
						return false;
					}
				}
			}

		//-----------------------FIM EDITA EXEMPLAR TIPO------------------------//

			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_cliente_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarClienteTipo\nvalores obrigatorios\nif( is_numeric( $this->cod_cliente_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 596, $this->pessoa_logada, 11,  "educar_cliente_tipo_lst.php" );


		$obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo, null, $this->pessoa_logada, null, null, null, null, null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_cliente_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarClienteTipo\nvalores obrigatorios\nif( is_numeric( $this->cod_cliente_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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

var scriptValida = "";
var retorno = 1;
var divExemplares = document.getElementById( "tr_div_exemplares" );
setVisibility ('tr_div_exemplares', false);

function getExemplarTipo()
{
	var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
	var campoClienteTipo = document.getElementById('cod_cliente_tipo').value;
	var xml1 = new ajax(getExemplarTipo_XML);
	strURL = "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca+"&cod_tipo_cliente="+campoClienteTipo;
//	strURL = "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca;
	xml1.envia(strURL);
}

function getExemplarTipo_XML(xml)
{
	var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
	var exemplares = document.getElementById('exemplares');
	var nm_tipo_exemplar = document.createElement( "input" );
	var span = document.createElement( "span" );
	var dias_tipo_exemplar = document.createElement( "input" );
	var br = document.createElement( "br" );
	var tipos = false;

	exemplares.innerHTML = "";
	scriptValida = "";

	var tipo_exemplar = xml.getElementsByTagName( "exemplar_tipo" );

	var aux = exemplares.innerHTML;

	if(tipo_exemplar.length)
		setVisibility ('tr_div_exemplares', true);

	for (var j = 0; j < tipo_exemplar.length; j++)
	{
		//if (tipo_exemplar[j][2] == campoBiblioteca)
		//{
		tipos = true;
		exemplares.appendChild(nm_tipo_exemplar);
		exemplares.appendChild(span);
		exemplares.appendChild(dias_tipo_exemplar);
		exemplares.appendChild(br);
		span.innerHTML = "Dias de Empr�stimo";
		span.setAttribute( "class", "dias" );
		nm_tipo_exemplar.setAttribute( "id", "teste"+j );
		nm_tipo_exemplar.setAttribute( 'type', 'text' );
		nm_tipo_exemplar.setAttribute( 'disabled', 'true' );
		nm_tipo_exemplar.setAttribute( 'class', 'obrigatorio' );
		nm_tipo_exemplar.setAttribute( 'style', 'margin: 2px;' );
		nm_tipo_exemplar.setAttribute( 'value', tipo_exemplar[j].firstChild.nodeValue );
		dias_tipo_exemplar.setAttribute( "id", "tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo") );
		dias_tipo_exemplar.setAttribute( 'type', 'text' );
		dias_tipo_exemplar.setAttribute( 'size', '3' );
		dias_tipo_exemplar.setAttribute( 'autocomplete', 'off' );
		dias_tipo_exemplar.setAttribute( 'style', 'margin: 2px;' );
		dias_tipo_exemplar.setAttribute( 'maxlength', '3' );
		if(tipo_exemplar[j].getAttribute("dias_emprestimo"))
			dias_tipo_exemplar.setAttribute( 'value', tipo_exemplar[j].getAttribute("dias_emprestimo"));
		else
			dias_tipo_exemplar.setAttribute( 'value', '');

		dias_tipo_exemplar.setAttribute( 'class', 'obrigatorio' );

		exemplares.innerHTML += aux;

		scriptValida += "if (!(/[^ ]/.test( document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').value )) || !((/^[0-9]+$/).test( document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').value )))\n";
		scriptValida +=	"{\n";
		scriptValida +=	"retorno = 0;\n";
		scriptValida +=	"mudaClassName( 'formdestaque', 'formlttd' );\n";
		scriptValida +=	"document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').className = \"formdestaque\";\n";
		scriptValida +=	"alert( 'Preencha o campo \""+tipo_exemplar[j].firstChild.nodeValue +"\" corretamente!' );\n";
		scriptValida +=	"document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').focus();\n";
		//scriptValida +=	"return retorno;\n";
		scriptValida +=	"}\n\n";
		document.getElementById("tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")).name = dias_tipo_exemplar.id;
		//}
	}

	if(!tipos)
	{
		setVisibility ('tr_div_exemplares', false);
	}
}

function Valida()
{
	eval(scriptValida);
	if (retorno == 0)
	{
		retorno = 1;
		return false;
	}
	acao();
}

if(document.getElementById('ref_cod_biblioteca').type == 'hidden')
{
	getExemplarTipo();

}
else
{
	document.getElementById('ref_cod_biblioteca').onchange = function()
	{
		getExemplarTipo();
	}

}

if(editar_)
{
	getExemplarTipo();
}

</script>
<style>
.dias
{
	padding: 6px;
}
</style>
