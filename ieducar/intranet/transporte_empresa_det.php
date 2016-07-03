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
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesEmpresaTransporteEscolar.inc.php';

require_once 'Portabilis/View/Helper/Application.php';


/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Empresas');
    $this->processoAp = 21235;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    // Verifica��o de permiss�o para cadastro.
    $this->obj_permissao = new clsPermissoes();

    $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

    $this->titulo = 'Empresa transporte escolar - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $cod_empresa_transporte_escolar = $_GET['cod_empresa'];

    $tmp_obj = new clsModulesEmpresaTransporteEscolar($cod_empresa_transporte_escolar);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
      header('Location: transporte_empresa_lst.php');
      die();
    }

    $objPessoaJuridica = new clsPessoaJuridica();
    list ($id_federal, $endereco, $cep, $nm_bairro, $cidade, $ddd_telefone_1, $telefone_1, $ddd_telefone_2, $telefone_2, $ddd_telefone_mov, $telefone_mov, $ddd_telefone_fax, $telefone_fax, $email, $ins_est) = $objPessoaJuridica->queryRapida($registro['ref_idpes'], "cnpj","logradouro","cep","bairro","cidade", "ddd_1","fone_1","ddd_2","fone_2","ddd_mov","fone_mov","ddd_fax","fone_fax", "email","insc_estadual");    
    
    $this->addDetalhe( array("C�digo da empresa", $cod_empresa_transporte_escolar));
    $this->addDetalhe( array("Nome fantasia", $registro['nome_empresa']) );
    $this->addDetalhe( array("Nome do respons�vel", $registro['nome_responsavel']) );
    $this->addDetalhe( array("CNPJ", int2CNPJ($id_federal)) );
    $this->addDetalhe( array("Endere&ccedil;o", $endereco) );
    $this->addDetalhe( array("CEP", $cep) );
    $this->addDetalhe( array("Bairro", $nm_bairro) );
    $this->addDetalhe( array("Cidade", $cidade) );
    if (trim($telefone_1)!='')
      $this->addDetalhe( array("Telefone 1", "({$ddd_telefone_1}) {$telefone_1}") );
    if (trim($telefone_2)!='')
      $this->addDetalhe( array("Telefone 2", "({$ddd_telefone_2}) {$telefone_2}") );
    if (trim($telefone_mov)!='')
      $this->addDetalhe( array("Celular", "({$ddd_telefone_mov}) {$telefone_mov}") );
    if (trim($telefone_fax)!='')
      $this->addDetalhe( array("Fax", "({$ddd_telefone_fax}) {$telefone_fax}") );
    
    $this->addDetalhe( array("E-mail", $email) );

    if( ! $ins_est ) $ins_est = "isento";
      $this->addDetalhe( array("Inscri&ccedil;&atilde;o estadual", $ins_est) );
    $this->addDetalhe( array("Observa&ccedil;&atilde;o", $registro['observacao']));
    $this->url_novo = "../module/TransporteEscolar/Empresa";
    $this->url_editar = "../module/TransporteEscolar/Empresa?id={$cod_empresa_transporte_escolar}";
    $this->url_cancelar = "transporte_empresa_lst.php";

    $this->largura = "100%";
  }
}

// Instancia o objeto da p�gina
$pagina = new clsIndexBase();

// Instancia o objeto de conte�do
$miolo = new indice();

// Passa o conte�do para a p�gina
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
