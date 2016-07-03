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
require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesPessoaTransporte.inc.php';
require_once 'include/localizacaoSistema.php';

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Usu�rios de transporte" );
		$this->processoAp = "21240";
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

	var $cod_pessoa_transporte;
	var $ref_cod_rota_transporte_escolar;
	var $nome_pessoa;
	var $nome_destino;

	function Gerar()
	{

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Usu�rio de transporte - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		// Cria lista de rotas 
		$obj_rota = new clsModulesRotaTransporteEscolar();
		$obj_rota->setOrderBy(' descricao asc ');
		$lista_rota = $obj_rota->lista();
		$select_rota = array("" => "Selecione uma rota" );
		foreach ($lista_rota as $reg) {
			$select_rota["{$reg['cod_rota_transporte_escolar']}"] = "{$reg['descricao']}";
		}

		$this->campoNumero("cod_pessoa_transporte","C&oacute;digo",$this->cod_pessoa_transporte,20,255,false);
		$this->campoTexto("nome_pessoa","Nome da pessoa", $this->nome_pessoa,50,255,false);
		$this->campoTexto("nome_destino","Nome do destino", $this->nome_destino,70,255,false);
		$this->campoLista( "ref_cod_rota_transporte_escolar", "Rota", $select_rota, $this->ref_cod_rota_transporte_escolar, "", false, "", "", false, false );

		$obj_permissoes = new clsPermissoes();

		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

		$this->addCabecalhos( array(
			"C&oacute;digo",
			"Nome da pessoa",
			"Rota",
			"Destino",
			"Ponto de embarque"
		) );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		
		$obj = new clsModulesPessoaTransporte();
		$obj->setLimite($this->limite,$this->offset);

		$lista = $obj->lista($this->cod_pessoa_transporte, null,
			$this->ref_cod_rota_transporte_escolar,null, null,$this->nome_pessoa,$this->nome_destino
			);
		$total = $lista->_total;

		foreach ( $lista AS $registro ) {

			$this->addLinhas( array(
				"<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["cod_pessoa_transporte"]}</a>",				
				"<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["nome_pessoa"]}</a>",	
				"<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["nome_rota"]}</a>",	
				"<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">".(trim($registro["nome_destino"])=='' ? $registro["nome_destino2"] : $registro["nome_destino"])."</a>",	
				"<a href=\"transporte_pessoa_det.php?cod_pt={$registro["cod_pessoa_transporte"]}\">{$registro["nome_ponto"]}</a>"
			) );
		}

		$this->addPaginador2( "transporte_pessoa_lst.php", $total, $_GET, $this->nome, $this->limite );

		$this->acao = "go(\"../module/TransporteEscolar/Pessoatransporte\")";
		$this->nome_acao = "Novo";

		//**
		$this->largura = "100%";

                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos(array($_SERVER['SERVER_NAME'] . '/intranet' => 'i-Educar', '' => 'Transporte de Pessoas'));
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