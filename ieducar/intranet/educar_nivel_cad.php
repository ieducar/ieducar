<?php

/*
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 */

/**
 * Cadastro de nível de categoria.
 *
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @todo     Aparentemente este arquivo apenas redireciona o usuário (linha 80)
 * @version  $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  public function Formular() {
    $this->SetTitulo($this->_instituicao . 'Nível');
    $this->processoAp = '829';
    $this->addEstilo('localizacaoSistema');
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

	var $cod_nivel;
	var $ref_cod_categoria_nivel;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_nivel_anterior;
	var $nm_nivel;
	var $salario_base;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_categoria;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_categoria_nivel = $_GET["cod_categoria"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3,  "educar_categoria_nivel_det.php?cod_categoria_nivel={$this->cod_nivel}",true );

		if( is_numeric( $this->ref_cod_categoria_nivel ) )
		{

			$obj = new clsPmieducarCategoriaNivel( $this->ref_cod_categoria_nivel );
			$registro  = $obj->detalhe();
			if( $registro )
			{

				$this->nm_categoria = $registro['nm_categoria_nivel'];
				$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 829, $this->pessoa_logada, 3, null, true ) )
				{
					$this->fexcluir = true;
				}

				$obj_niveis = new clsPmieducarNivel();
				$obj_niveis->setOrderby("cod_nivel");
				$lst_niveis = $obj_niveis->lista(nul,$this->ref_cod_categoria_nivel, null, null,null,null,null,null,null,null,null,1);

				if($lst_niveis)
				{
					foreach ($lst_niveis as $id => $nivel)
					{
						$id++;
						$nivel['salario_base'] = number_format($nivel['salario_base'],2,',','.');
						$this->cod_nivel[] = array($nivel['nm_nivel'],$nivel['salario_base'],$id,$nivel['cod_nivel']);
					}
				}
				else
				{
					$this->cod_nivel[] = array('','','1','');
				}

				$retorno = "Editar";
			}
		}
		else
		{
			header("location:educar_categoria_nivel_lst.php");
			die();
		}

		$this->url_cancelar = "educar_categoria_nivel_det.php?cod_categoria_nivel={$this->cod_nivel}";
		$this->nome_url_cancelar = "Cancelar";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "Adicionar n&iacute;veis &agrave; categoria"             
    ));
    $this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar()
	{

		$this->campoOculto("ref_cod_categoria_nivel", $this->ref_cod_categoria_nivel);

		$this->campoRotulo("nm_categoria","Categoria",$this->nm_categoria);

		$this->campoTabelaInicio("tab1","N&iacute;veis",array("Nome N&iacute;vel",'Sal&aacute;rio','Ordem'),$this->cod_nivel);

			$this->campoTexto("nm_nivel","Nome N&iacute;vel","",30,100,true);
			$this->campoMonetario( "salario_base", "Salario Base", $this->salario_base, 10, 8, true );
			$this->campoNumero("nr_nivel","Ordem","1",5,5,false,false,false,false,false,false,true);
			$this->campoOculto("cod_nivel","");

		$this->campoTabelaFim();




	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 829, $this->pessoa_logada, 3,  "educar_categoria_nivel_det.php?cod_categoria_nivel={$this->cod_nivel}",true );



		$obj = new clsPmieducarNivel( $this->cod_nivel, $this->ref_cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_nivel_anterior, $this->nm_nivel, $this->salario_base, $this->data_cadastro, $this->data_exclusao, $this->ativo );

		$obj->desativaTodos();

		if($this->nm_nivel)
		{
			$nivel_anterior = null;
			$niveis = array();
			foreach ($this->nm_nivel as $id => $nm_nivel)
			{
				$obj_nivel = new clsPmieducarNivel($this->cod_nivel[$id],$this->ref_cod_categoria_nivel,$this->pessoa_logada,$this->pessoa_logada,$nivel_anterior,$nm_nivel,str_replace(',','.',str_replace('.','',$this->salario_base[$id])),null,null,1);
				if($obj_nivel->existe())
				{
					$obj_nivel->edita();
					$nivel_anterior = $this->cod_nivel[$id];
				}
				else
					$nivel_anterior = $obj_nivel->cadastra();

				$niveis[] = $nivel_anterior;
			}

			/**
			 * desativa todos os subniveis dos niveis que nao se
			 * encontram ativos
			 */

			if($niveis)
			{
				$obj = new clsPmieducarSubnivel(null,$this->pessoa_logada,$this->pessoa_logada,null,$this->ref_cod_nivel);

				$obj->desativaTodos($niveis);
			}

			header("location:educar_categoria_nivel_det.php?cod_categoria_nivel={$this->ref_cod_categoria_nivel}");
			die();

			return true;
		}


		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarNivel\nvalores obrigatorios\nis_numeric( $this->ref_cod_categoria_nivel ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_nivel_anterior ) && is_string( $this->nm_nivel )\n-->";
		return false;
	}

	function Editar()
	{
		if(!$this->Novo())

			return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 829, $this->pessoa_logada, 3,  "educar_nivel_lst.php", true );


		$obj = new clsPmieducarNivel($this->cod_nivel, $this->ref_cod_categoria_nivel, $this->pessoa_logada, $this->pessoa_logada, $this->ref_cod_nivel_anterior, $this->nm_nivel, $this->salario_base, $this->data_cadastro, $this->data_exclusao, 0);
		$excluiu = $obj->desativaTodos();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header("location:educar_categoria_nivel_det.php?cod_categoria_nivel={$this->ref_cod_categoria_nivel}");
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarNivel\nvalores obrigatorios\nif( is_numeric( $this->cod_nivel ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
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

<script type="text/javascript">

	function setOrdem(id)
	{
		document.getElementById('nr_nivel['+(id)+']').value = (id+1);
	}

	tab_add_1.afterAddRow = function() {
		setOrdem(this.id-1);
	}

	tab_add_1.afterRemoveRow = function() {
		reordena();
	}

	function reordena()
	{
		for(var ct=0;ct < tab_add_1.getId();ct++)
		{
			setOrdem(ct);
		}
	}
</script>