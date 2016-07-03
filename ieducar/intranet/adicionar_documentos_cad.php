<?php

/*
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
 */

$desvio_diretorio = '';
require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/Geral.inc.php';


class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Adicionar Documentos" );
		$this->processoAp = "0"; //nao alterar, paginas com ap diferentes chamam essa
		$this->renderMenu = false;

	}
}

class indice extends clsCadastro
{
	var $idpes,
		$rg,
		$data_exp_rg,
		$sigla_uf_exp_rg,
		$tipo_cert_civil,
		$num_termo,
		$num_livro,
		$num_folha,
		$data_emissao_cert_civil,
		$sigla_uf_cert_civil,
		$cartorio_cert_civil,
		$num_cart_trabalho,
		$serie_cart_trabalho,
		$data_emissao_cart_trabalho,
		$sigla_uf_cart_trabalho,
		$num_tit_eleitor,
		$zona_tit_eleitor,
		$secao_tit_eleitor,
		$idorg_exp_rg;

	function Inicializar()
	{
		$retorno = "Novo";

		$this->idpes = ($_GET['id_pessoa']) ? $_GET['id_pessoa'] : $_SESSION['id_pessoa'];
		$ObjDocumento = new clsDocumento($this->idpes);
		$detalheDocumento = $ObjDocumento->detalhe();

		$this->rg = $detalheDocumento['rg'];
		if($detalheDocumento['data_exp_rg'])
		{
			$this->data_exp_rg = date( "d/m/Y", strtotime( substr($detalheDocumento['data_exp_rg'],0,19) ) );
		}
		$this->sigla_uf_exp_rg = $detalheDocumento['sigla_uf_exp_rg'];
		$this->tipo_cert_civil = $detalheDocumento['tipo_cert_civil'];
		$this->num_termo = $detalheDocumento['num_termo'];
		$this->num_livro = $detalheDocumento['num_livro'];
		$this->num_folha = $detalheDocumento['num_folha'];
		if($detalheDocumento['data_emissao_cert_civil'])
		{
			$this->data_emissao_cert_civil = date( "d/m/Y", strtotime( substr($detalheDocumento['data_emissao_cert_civil'],0,19) ) );
		}
		$this->sigla_uf_cert_civil = $detalheDocumento['sigla_uf_cert_civil'];

		$this->cartorio_cert_civil = $detalheDocumento['cartorio_cert_civil'];
		$this->num_cart_trabalho = $detalheDocumento['num_cart_trabalho'];
		$this->serie_cart_trabalho = $detalheDocumento['serie_cart_trabalho'];
		if($detalheDocumento['data_emissao_cart_trabalho'])
		{
			$this->data_emissao_cart_trabalho = date( "d/m/Y", strtotime( substr($detalheDocumento['data_emissao_cart_trabalho'],0,19) ) );
		}
		$this->sigla_uf_cart_trabalho = $detalheDocumento['sigla_uf_cart_trabalho'];
		$this->num_tit_eleitor = $detalheDocumento['num_tit_eleitor'];
		$this->zona_tit_eleitor = $detalheDocumento['zona_tit_eleitor'];
		$this->secao_tit_eleitor = $detalheDocumento['secao_tit_eleitor'];
		$this->idorg_exp_rg = $detalheDocumento['idorg_exp_rg'];
		$this->certidao_nascimento = $detalheDocumento['certidao_nascimento'];

		$ObjDocumento = new clsDocumento($this->idpes);

		if ($ObjDocumento->detalhe())
		{
			$retorno = "Editar";
		}
		else
		{
			$retorno = "Novo";
		}

		return $retorno;
	}

	function Gerar()
	{



		$objUf = new clsUf();
		$listauf = $objUf->lista();
		$listaEstado = array("0"=>"Selecione");
		if($listauf)
		{
			foreach ($listauf as $uf) {
				$listaEstado[$uf['sigla_uf']] = $uf['sigla_uf'];
			}
		}

		$objOrgaoEmissorRg = new clsOrgaoEmissorRg();
		$listaOrgaoEmissorRg = $objOrgaoEmissorRg->lista();
		$listaOrgao = array("0"=>"Selecione");
		if($listaOrgaoEmissorRg)
		{
			foreach ($listaOrgaoEmissorRg as $orgaoemissor){
				$listaOrgao[$orgaoemissor['idorg_rg']] = $orgaoemissor['sigla'];
			}
		}

		$this->campoOculto( "idpes", $this->idpes);

		$this->campoTexto("rg", "Rg", $this->rg, "10", "10", false);
		$this->campoData("data_exp_rg", "Data Expedi��o RG", $this->data_exp_rg, false);
		$this->campoLista("sigla_uf_exp_rg", "�rg�o Expedidor", $listaEstado, $this->sigla_uf_exp_rg, false, false, false, false, false);

		$lista_tipo_cert_civil = array();
		$lista_tipo_cert_civil["0"] = "Selecione";
		$lista_tipo_cert_civil[91] = "Nascimento";
		$lista_tipo_cert_civil[92] = "Casamento";
		$this->campoLista( "tipo_cert_civil", "Tipo Certificado Civil", $lista_tipo_cert_civil, $this->tipo_cert_civil);

		$this->campoTexto("num_termo", "Termo", $this->num_termo, "8", "8", false);
		$this->campoTexto("num_livro", "Livro", $this->num_livro, "8", "8", false);
		$this->campoTexto("num_folha", "Folha", $this->num_folha, "4", "4", false);
		$this->campoTexto('certidao_nascimento', 'Certid�o nascimento', $this->certidao_nascimento, '37', '40', FALSE);

		$this->campoData("data_emissao_cert_civil", "Emiss�o Certid�o Civil", $this->data_emissao_cert_civil, false);
		$this->campoLista("sigla_uf_cert_civil", "Sigla Certid�o Civil", $listaEstado, $this->sigla_uf_cert_civil, false, false, false, false, false);
		$this->campoMemo("cartorio_cert_civil", "Cart�rio", $this->cartorio_cert_civil, "35", "4", false);
		$this->campoTexto("num_cart_trabalho", "Carteira de Trabalho", $this->num_cart_trabalho, "7", "7", false);
		$this->campoTexto("serie_cart_trabalho", "S�rie", $this->serie_cart_trabalho, "5", "5", false);
		$this->campoData("data_emissao_cart_trabalho", "Emiss�o Carteira", $this->data_emissao_cart_trabalho, false);
		$this->campoLista("sigla_uf_cart_trabalho", "Sigla Carteira de Trabalho", $listaEstado, $this->sigla_uf_cart_trabalho, false, false, false, false, false);
		$this->campoTexto("num_tit_eleitor", "T�tulo de Eleitor", $this->num_tit_eleitor, "13", "13", false);
		$this->campoTexto("zona_tit_eleitor", "Zona", $this->zona_tit_eleitor, "4", "4", false);
		$this->campoTexto("secao_tit_eleitor", "Se��o", $this->secao_tit_eleitor, "10", "10", false);
		$this->campoLista("idorg_exp_rg", "�rg�o Expedi��o RG", $listaOrgao, $this->idorg_exp_rg, false, false, false, false, false);

	}

	function Novo() {
		if($this->data_emissao_cart_trabalho) {
			$this->data_emissao_cart_trabalho = explode("/",$this->data_emissao_cart_trabalho);
			$this->data_emissao_cart_trabalho = "{$this->data_emissao_cart_trabalho[2]}/{$this->data_emissao_cart_trabalho[1]}/{$this->data_emissao_cart_trabalho[0]}";
		}

		if($this->data_emissao_cert_civil) {
			$this->data_emissao_cert_civil = explode("/",$this->data_emissao_cert_civil);
			$this->data_emissao_cert_civil = "{$this->data_emissao_cert_civil[2]}/{$this->data_emissao_cert_civil[1]}/{$this->data_emissao_cert_civil[0]}";
		}

		if($this->data_exp_rg) {
			$this->data_exp_rg = explode("/",$this->data_exp_rg);
			$this->data_exp_rg = "{$this->data_exp_rg[2]}/{$this->data_exp_rg[1]}/{$this->data_exp_rg[0]}";
		}

		// remove caracteres n�o numericos
		$this->rg = preg_replace("/[^0-9]/", "", $this->rg);

		$ObjDocumento = new clsDocumento($this->idpes, $this->rg, $this->data_exp_rg, $this->sigla_uf_exp_rg, $this->tipo_cert_civil, $this->num_termo, $this->num_livro, $this->num_folha, $this->data_emissao_cert_civil, $this->sigla_uf_cert_civil, $this->cartorio_cert_civil, $this->num_cart_trabalho, $this->serie_cart_trabalho, $this->data_emissao_cart_trabalho, $this->sigla_uf_cart_trabalho, $this->num_tit_eleitor, $this->zona_tit_eleitor, $this->secao_tit_eleitor, $this->idorg_exp_rg, $this->certidao_nascimento );

		if( $ObjDocumento->cadastra() ) {
			echo "<script>window.close()</script>";
			return true;
		}

		return false;
	}

	function Editar() {
		if($this->data_emissao_cart_trabalho) {
			$this->data_emissao_cart_trabalho = explode("/",$this->data_emissao_cart_trabalho);
			$this->data_emissao_cart_trabalho = "{$this->data_emissao_cart_trabalho[2]}/{$this->data_emissao_cart_trabalho[1]}/{$this->data_emissao_cart_trabalho[0]}";
		}

		if($this->data_emissao_cert_civil) {
			$this->data_emissao_cert_civil = explode("/",$this->data_emissao_cert_civil);
			$this->data_emissao_cert_civil = "{$this->data_emissao_cert_civil[2]}/{$this->data_emissao_cert_civil[1]}/{$this->data_emissao_cert_civil[0]}";
		}

		if($this->data_exp_rg) {
			$this->data_exp_rg = explode("/",$this->data_exp_rg);
			$this->data_exp_rg = "{$this->data_exp_rg[2]}/{$this->data_exp_rg[1]}/{$this->data_exp_rg[0]}";
		}

		// remove caracteres n�o numericos
		$this->rg = preg_replace("/[^0-9]/", "", $this->rg);

		$ObjDocumento = new clsDocumento($this->idpes, $this->rg, $this->data_exp_rg, $this->sigla_uf_exp_rg, $this->tipo_cert_civil, $this->num_termo, $this->num_livro, $this->num_folha, $this->data_emissao_cert_civil, $this->sigla_uf_cert_civil, $this->cartorio_cert_civil, $this->num_cart_trabalho, $this->serie_cart_trabalho, $this->data_emissao_cart_trabalho, $this->sigla_uf_cart_trabalho, $this->num_tit_eleitor, $this->zona_tit_eleitor, $this->secao_tit_eleitor, $this->idorg_exp_rg, $this->certidao_nascimento );

    if ($ObjDocumento->edita()) {
      echo '<script>window.close()</script>';
      return TRUE;
    }

    return FALSE;
  }

	function Excluir()
	{
		$ObjDocumento = new clsDocumento($this->idpes);
		$ObjDocumento->exclui();
		echo "<script>document.location='meusdados.php';</script>";
		return true;
	}

}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>