<?php
/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesEmpresaTransporteEscolar.inc.php';
require_once 'include/localizacaoSistema.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Empresas" );
		$this->processoAp = "21235";
                $this->addEstilo( "localizacaoSistema" );

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

	var $cod_empresa;
	var $nome_empresa;
	var $nome_responsavel;

	function Gerar()
	{

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Empresas de transporte escolar - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->campoNumero("cod_empresa","C&oacute;digo da empresa",$this->cod_empresa,20,255,false);
		$this->campoTexto("nome_empresa","Nome fantasia", $this->nome_empresa,50,255,false);
		$this->campoTexto("nome_responsavel","Nome do respons�vel", $this->nome_responsavel,50,255,false);

		$obj_permissoes = new clsPermissoes();

		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		$this->addCabecalhos( array(
			"C&oacute;digo da empresa",
			"Nome fantasia",
			"Nome do respons&aacute;vel",
			"Telefone"
		) );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		
		$obj_empresa = new clsModulesEmpresaTransporteEscolar();
		$obj_empresa->setLimite($this->limite,$this->offset);

		$empresas = $obj_empresa->lista($this->cod_empresa,null,null,$this->nome_empresa, $this->nome_responsavel);
		$total = $empresas->_total;

		foreach ( $empresas AS $registro ) {

			$this->addLinhas( array(
				"<a href=\"transporte_empresa_det.php?cod_empresa={$registro["cod_empresa_transporte_escolar"]}\">{$registro["cod_empresa_transporte_escolar"]}</a>",
				"<a href=\"transporte_empresa_det.php?cod_empresa={$registro["cod_empresa_transporte_escolar"]}\">{$registro["nome_empresa"]}</a>",
				"<a href=\"transporte_empresa_det.php?cod_empresa={$registro["cod_empresa_transporte_escolar"]}\">{$registro["nome_responsavel"]}</a>",
				"<a href=\"transporte_empresa_det.php?cod_empresa={$registro["cod_empresa_transporte_escolar"]}\">{$registro["telefone"]}</a>"
			) );
		}

		$this->addPaginador2( "transporte_empresa_lst.php", $total, $_GET, $this->nome, $this->limite );

		$this->acao = "go(\"../module/TransporteEscolar/Empresa\")";
		$this->nome_acao = "Novo";

		//**
		$this->largura = "100%";
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos(array($_SERVER['SERVER_NAME'] . '/intranet' => 'i-Educar', '' => 'Transporte Empresa'));
                $this->enviaLocalizacao($localizacao->montar());
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
