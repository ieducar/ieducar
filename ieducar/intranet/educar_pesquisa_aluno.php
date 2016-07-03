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
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

//class clsIndexBase extends clsBase
class clsIndex extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Aluno" );
		$this->processoAp = "0";
		$this->renderMenu = false;
		$this->renderMenuSuspenso = false;
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	var $ref_cod_aluno;
	var $nm_aluno;
	var $cod_aluno;

	var $ref_cod_escola;

	function Gerar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->nm_aluno = $_GET["nm_aluno"];
		$this->cod_aluno = $_GET["cod_aluno"];

		$this->ref_cod_escola = $_GET['ref_cod_escola'];

		if(!$this->ref_cod_escola)
		{
			$this->ref_cod_escola = $_POST['ref_cod_escola'];
		}

		$this->campoOculto("ref_cod_escola", $this->ref_cod_escola);

		$this->titulo = "Aluno - Listagem";

		$this->addCabecalhos( array(
			"Aluno"
		) );

		$this->campoNumero( "cod_aluno", "C&oacute;digo Aluno", $this->nm_aluno, 8, 20, false );
		$this->campoTexto( "nm_aluno", "Nome Aluno", $this->nm_aluno, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_aluno = new clsPmieducarAluno();
		$obj_aluno->setOrderby( "nome_aluno ASC" );
		$obj_aluno->setLimite( $this->limite, $this->offset );

		$lista = $obj_aluno->lista(
			$this->cod_aluno,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			null,
			$this->nm_aluno,
			null,
			null,
			null,
			null,
			null,
			$this->ref_cod_escola
		);

		$total = $obj_aluno->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
//			echo "<pre>";print_r($lista);die;
			foreach ( $lista AS $registro )
			{
				$registro["nome_aluno"] = str_replace("'","", $registro['nome_aluno']);
				$script = " onclick=\"addVal1('ref_cod_aluno','{$registro['cod_aluno']}'); addVal1('nm_aluno','{$registro['nome_aluno']}'); addVal1('nm_aluno_','{$registro['nome_aluno']}');fecha();\"";

				$this->addLinhas( array(
					"<a href=\"javascript:void(0);\" {$script}>{$registro["nome_aluno"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_pesquisa_aluno.php", $total, $_GET, $this->nome, $this->limite );
		$this->largura = "100%";
	}
}
// cria uma extensao da classe base
//$pagina = new clsIndexBase();
$pagina = new clsIndex();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>

function addVal1( campo, valor )
{
	if( !window.parent.document.getElementById( campo ) )
		return;
	obj       =  window.parent.document.getElementById( campo );
	obj.value = valor;
}

function fecha()
{
	window.parent.fechaExpansivel('div_dinamico_'+(parent.DOM_divs.length*1-1));
//	window.parent.document.getElementById('tipoacao').value = '';
	if( window.parent.document.getElementById('passo') )
	{
		window.parent.document.getElementById('passo').value = 2;
		window.parent.document.forms[0].submit();
	}
}
</script>
