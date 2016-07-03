<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';
require_once 'Portabilis/Utils/Validation.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'image_check.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Pessoas F�sicas - Cadastro');
    $this->processoAp = 43;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $cod_pessoa_fj;
  var $nm_pessoa;
  var $id_federal;
  var $data_nasc;
  var $endereco;
  var $cep;
  var $idlog;
  var $idbai;
  var $sigla_uf;
  var $ddd_telefone_1;
  var $telefone_1;
  var $ddd_telefone_2;
  var $telefone_2;
  var $ddd_telefone_mov;
  var $telefone_mov;
  var $ddd_telefone_fax;
  var $telefone_fax;
  var $email;
  var $tipo_pessoa;
  var $sexo;
  var $busca_pessoa;
  var $complemento;
  var $apartamento;
  var $bloco;
  var $andar;
  var $numero;
  var $retorno;
  var $zona_localizacao;
  var $cor_raca;

  var $caminho_det;
  var $caminho_lst;

  // Vari�veis para controle da foto
  var $objPhoto;
  var $arquivoFoto;  

  function Inicializar()
  {
    $this->cod_pessoa_fj = @$_GET['cod_pessoa_fj'];
    $this->retorno       = 'Novo';
    
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();
    
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(43, $this->pessoa_logada, 3,
    		'atendidos_lst.php');

    if (is_numeric($this->cod_pessoa_fj)) {
      $this->retorno = 'Editar';
      $objPessoa     = new clsPessoaFisica();

      list($this->nm_pessoa, $this->id_federal, $this->data_nasc,
        $this->ddd_telefone_1, $this->telefone_1, $this->ddd_telefone_2,
        $this->telefone_2, $this->ddd_telefone_mov, $this->telefone_mov,
        $this->ddd_telefone_fax, $this->telefone_fax, $this->email,
        $this->tipo_pessoa, $this->sexo, $this->cidade,
        $this->bairro, $this->logradouro, $this->cep, $this->idlog, $this->idbai,
        $this->idtlog, $this->sigla_uf, $this->complemento, $this->numero,
        $this->bloco, $this->apartamento, $this->andar, $this->zona_localizacao, $this->estado_civil,
        $this->pai_id, $this->mae_id, $this->tipo_nacionalidade, $this->pais_origem, $this->naturalidade,
        $this->letra
      ) =

      $objPessoa->queryRapida(
        $this->cod_pessoa_fj, 'nome', 'cpf', 'data_nasc',  'ddd_1', 'fone_1',
        'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov', 'ddd_fax', 'fone_fax', 'email',
        'tipo', 'sexo', 'cidade', 'bairro', 'logradouro', 'cep', 'idlog',
        'idbai', 'idtlog', 'sigla_uf', 'complemento', 'numero', 'bloco', 'apartamento',
        'andar', 'zona_localizacao', 'ideciv', 'idpes_pai', 'idpes_mae', 'nacionalidade',
        'idpais_estrangeiro', 'idmun_nascimento', 'letra'
      );

      $this->id_federal      = is_numeric($this->id_federal) ? int2CPF($this->id_federal) : '';
      $this->cep             = is_numeric($this->cep)        ? int2Cep($this->cep) : '';
      $this->data_nasc       = $this->data_nasc              ? dataFromPgToBr($this->data_nasc) : '';

      $this->estado_civil_id = $this->estado_civil->ideciv;
      $this->pais_origem_id  = $this->pais_origem->idpais;
      $this->naturalidade_id = $this->naturalidade->idmun;

      $raca           = new clsCadastroFisicaRaca($this->cod_pessoa_fj);
      $raca           = $raca->detalhe();
      $this->cod_raca = is_array($raca) ? $raca['ref_cod_raca'] : null;
    }

    $this->nome_url_cancelar = 'Cancelar';

    return $this->retorno;
  }

  function Gerar()
  {
    $this->url_cancelar = $this->retorno == 'Editar' ?
      'atendidos_det.php?cod_pessoa=' . $this->cod_pessoa_fj : 'atendidos_lst.php';

    $this->campoCpf('id_federal', 'CPF', $this->id_federal, FALSE);

    $this->campoOculto('cod_pessoa_fj', $this->cod_pessoa_fj);
    $this->campoTexto('nm_pessoa', 'Nome', $this->nm_pessoa, '50', '255', TRUE);

    // TODO: Tornar configur�vel.
    /* N�o tem S3 nem quick-retrieval cloud file storage.
       Nada de foto.
    $foto = false;
    if (is_numeric($this->cod_pessoa_fj)){
      $objFoto = new ClsCadastroFisicaFoto($this->cod_pessoa_fj);
      $detalheFoto = $objFoto->detalhe();
      if(count($detalheFoto))
      $foto = $detalheFoto['caminho'];
    } else 
      $foto=false;
 
    if ($foto!=false){
      $this->campoRotulo('fotoAtual_','Foto atual','<img height="117" src="'.$foto.'"/>');
      $this->campoArquivo('file','Trocar foto',$this->arquivoFoto,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m�ximo: 150KB</span>');
    }else
      $this->campoArquivo('file','Foto',$this->arquivoFoto,40,'<br/> <span style="font-style: italic; font-size= 10px;">* Recomenda-se imagens nos formatos jpeg, jpg, png e gif. Tamanho m�ximo: 150KB</span>');
      */
    $this->campoOculto('file', null);
 

    // ao cadastrar pessoa do pai ou m�e apartir do cadastro de outra pessoa,
    // � enviado o tipo de cadastro (pai ou mae).
    $parentType = isset($_REQUEST['parent_type']) ? $_REQUEST['parent_type'] : '';
    $naturalidadeObrigatoria = ($parentType == '' ? true : false);


     // sexo

    $sexo = $this->sexo;

    // sugere sexo quando cadastrando o pai ou m�e

    if (! $sexo && $parentType == 'pai')
      $sexo = 'M';
    elseif (! $sexo && $parentType == 'mae')
      $sexo = 'F';


    $options = array(
      'label'       => 'Sexo / Estado civil',
      'value'     => $sexo,
      'resources' => array(
        '' => 'Sexo',
        'M' => 'Masculino',
        'F' => 'Feminino'
      ),
      'inline' => true
    );

    $this->inputsHelper()->select('sexo', $options);

    // estado civil

    $this->inputsHelper()->estadoCivil(array('label' => '', 'required' => empty($parentType)));


    // data nascimento

    $options = array(
      'label'       => 'Data nascimento',
      'value'       => $this->data_nasc,
      'required'    => empty($parentType)
    );

    $this->inputsHelper()->date('data_nasc', $options);


    // Input para os pais da pessoa f�sica.
    // Se o formul�rio foi gerado para cadastro de pai ou m�e,
    // a partir desta input, n�o mostrar novamente esta op��o.
    if (!$parentType) {
    	$this->inputPai();
    	$this->inputMae();
    }


    // documentos

    $documentos        = new clsDocumento();
    $documentos->idpes = $this->cod_pessoa_fj;
    $documentos        = $documentos->detalhe();

    // rg

    // o rg � obrigatorio ao cadastrar pai ou m�e, exceto se configurado como opcional.

    $required = (! empty($parentType));

    if ($required && $GLOBALS['coreExt']['Config']->app->rg_pessoa_fisica_pais_opcional) {
      $required = false;
    }

    $options = array(
      'required'    => $required,
      'label'       => 'RG / Data emiss�o',
      'placeholder' => 'Documento identidade',
      'value'       => $documentos['rg'],
      'max_length'  => 20,
      'size'        => 27,
      'inline'      => true
    );

    $this->inputsHelper()->integer('rg', $options);


    // data emiss�o rg

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emiss�o',
      'value'       => $documentos['data_exp_rg'],
      'size'        => 19
    );

    $this->inputsHelper()->date('data_emissao_rg', $options);


    // org�o emiss�o rg

    $selectOptions = array( null => 'Org�o emissor' );
    $orgaos        = new clsOrgaoEmissorRg();
    $orgaos        = $orgaos->lista();

    foreach ($orgaos as $orgao)
      $selectOptions[$orgao['idorg_rg']] = $orgao['sigla'];

    $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

    $options = array(
      'required'  => false,
      'label'     => '',
      'value'     => $documentos['idorg_exp_rg'],
      'resources' => $selectOptions,
      'inline'    => true
    );

    $this->inputsHelper()->select('orgao_emissao_rg', $options);


    // uf emiss�o rg

    $options = array(
      'required' => false,
      'label'    => '',
      'value'    => $documentos['sigla_uf_exp_rg']
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_rg'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // tipo de certidao civil

    $selectOptions = array(
      null                               => 'Tipo certid�o civil',
      'certidao_nascimento_novo_formato' => 'Nascimento (novo formato)',
      91                                 => 'Nascimento (antigo formato)',
      92                                 => 'Casamento'
    );


    // caso certidao nascimento novo formato tenha sido informado,
    // considera este o tipo da certid�o
    if (! empty($documentos['certidao_nascimento']))
      $tipoCertidaoCivil = 'certidao_nascimento_novo_formato';
    else
      $tipoCertidaoCivil = $documentos['tipo_cert_civil'];

    $options = array(
      'required'  => false,
      'label'     => 'Tipo certid�o civil',
      'value'     => $tipoCertidaoCivil,
      'resources' => $selectOptions,
      'inline'    => true
    );

    $this->inputsHelper()->select('tipo_certidao_civil', $options);


    // termo certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Termo',
      'value'       => $documentos['num_termo'],
      'max_length'  => 8,
      'inline'      => true
    );

    $this->inputsHelper()->integer('termo_certidao_civil', $options);


    // livro certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Livro',
      'value'       => $documentos['num_livro'],
      'max_length'  => 8,
      'size'        => 15,
      'inline'      => true
    );

    $this->inputsHelper()->text('livro_certidao_civil', $options);


    // folha certidao civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Folha',
      'value'       => $documentos['num_folha'],
      'max_length'  => 4,
      'inline'      => true
    );

    $this->inputsHelper()->integer('folha_certidao_civil', $options);


    // certidao nascimento (novo padr�o)

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Certid�o nascimento',
      'value'       => $documentos['certidao_nascimento'],
      'max_length'  => 50,
      'size'        => 50
    );

    $this->inputsHelper()->text('certidao_nascimento', $options);


    // uf emiss�o certid�o civil

    $options = array(
      'required' => false,
      'label'    => 'Estado emiss�o / Data emiss�o',
      'value'    => $documentos['sigla_uf_cert_civil'],
      'inline'   => true
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_certidao_civil'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // data emiss�o certid�o civil

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emiss�o',
      'value'       => $documentos['data_emissao_cert_civil']
    );

    $this->inputsHelper()->date('data_emissao_certidao_civil', $options);


    // cart�rio emiss�o certid�o civil

    $options = array(
      'required'    => false,
      'label'       => 'Cart�rio emiss�o',
      'value'       => $documentos['cartorio_cert_civil'],
      'cols'        => 45,
      'max_length'  => 150
    );

    $this->inputsHelper()->textArea('cartorio_emissao_certidao_civil', $options);


    // carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => 'Carteira de trabalho / S�rie',
      'placeholder' => 'Carteira de trabalho',
      'value'       => $documentos['num_cart_trabalho'],
      'max_length'  => 7,
      'inline'      => true

    );

    $this->inputsHelper()->integer('carteira_trabalho', $options);

    // serie carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'S�rie',
      'value'       => $documentos['serie_cart_trabalho'],
      'max_length'  => 5
    );

    $this->inputsHelper()->integer('serie_carteira_trabalho', $options);


    // uf emiss�o carteira de trabalho

    $options = array(
      'required' => false,
      'label'    => 'Estado emiss�o / Data emiss�o',
      'value'    => $documentos['sigla_uf_cart_trabalho'],
      'inline'   => true
    );

    $helperOptions = array(
      'attrName' => 'uf_emissao_carteira_trabalho'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // data emiss�o carteira de trabalho

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Data emiss�o',
      'value'       => $documentos['data_emissao_cart_trabalho']
    );

    $this->inputsHelper()->date('data_emissao_carteira_trabalho', $options);


    // titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => 'Titulo eleitor / Zona / Se��o',
      'placeholder' => 'Titulo eleitor',
      'value'       => $documentos['num_tit_eleitor'],
      'max_length'  => 13,
      'inline'      => true
    );

    $this->inputsHelper()->integer('titulo_eleitor', $options);


    // zona titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Zona',
      'value'       => $documentos['zona_tit_eleitor'],
      'max_length'  => 4,
      'inline'      => true
    );

    $this->inputsHelper()->integer('zona_titulo_eleitor', $options);


    // se��o titulo eleitor

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Se��o',
      'value'       => $documentos['secao_tit_eleitor'],
      'max_length'  => 4
    );

    $this->inputsHelper()->integer('secao_titulo_eleitor', $options);


    // Cor/ra�a.

    $racas         = new clsCadastroRaca();
    $racas         = $racas->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, TRUE);
    $selectOptions = array('' => 'Ra�a');

    foreach ($racas as $raca)
      $selectOptions[$raca['cod_raca']] = $raca['nm_raca'];

    $selectOptions = Portabilis_Array_Utils::sortByValue($selectOptions);

    $this->campoLista('cor_raca', 'Ra�a', $selectOptions, $this->cod_raca, '', FALSE, '', '', '', FALSE);


    // nacionalidade

    // tipos
    $tiposNacionalidade = array(null => 'Selecione',
                                '1'  => 'Brasileiro',
                                '2'  => 'Naturalizado brasileiro',
                                '3'  => 'Estrangeiro');

    $options            = array('label'       => 'Nacionalidade',
                                'resources'   => $tiposNacionalidade,
                                'required'    => false,
                                'inline'      => true,
                                'value'       => $this->tipo_nacionalidade);

    $this->inputsHelper()->select('tipo_nacionalidade', $options);


    // pais origem

    $options = array(
      'label'       => '',
      'placeholder' => 'Informe o nome do pais',
      'required'    => true
    );

    $hiddenInputOptions = array(
      'options' => array('value' => $this->pais_origem_id)
    );

    $helperOptions = array(
      'objectName'         => 'pais_origem',
      'hiddenInputOptions' => $hiddenInputOptions
    );

    $this->inputsHelper()->simpleSearchPais('nome', $options, $helperOptions);


    // naturalidade

    //$options       = array('label' => 'Naturalidade', 'required'   => true);
    $options       = array('label' => 'Naturalidade', 'required'   => $naturalidadeObrigatoria); 

    $helperOptions = array('objectName'         => 'naturalidade',
                           'hiddenInputOptions' => array('options' => array('value' => $this->naturalidade_id)));

    $this->inputsHelper()->simpleSearchMunicipio('nome', $options, $helperOptions);


    // Detalhes do Endere�o

    $this->campoOculto('idbai', $this->idbai);
    $this->campoOculto('idlog', $this->idlog);
    $this->campoOculto('cep', $this->cep);
    $this->campoOculto('ref_sigla_uf', $this->sigla_uf);
    $this->campoOculto('ref_idtlog', $this->idtlog);
    $this->campoOculto('id_cidade', $this->cidade);


    // o endere�amento � opcional ao cadastrar pai ou m�e.
    $enderecamentoObrigatorio = empty($parentType);


    // considera como endere�o localizado por CEP quando alguma das variaveis de instancia
    // idbai (bairro) ou idlog (logradouro) est�o definidas, neste caso desabilita a edi��o
    // dos campos definidos via CEP.
    $desativarCamposDefinidosViaCep = ((bool)$GLOBALS['coreExt']['Config']->app->obriga_endereco_normalizado_pf) || ($this->idbai || $this->idlog);

    $this->campoCep(
      'cep_',
      'CEP',
      $this->cep,
      $enderecamentoObrigatorio,
      '-',
      "&nbsp;<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel(500, 550, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'educar_pesquisa_cep_log_bairro.php?campo1=bairro&campo2=idbai&campo3=cep&campo4=logradouro&campo5=idlog&campo6=ref_sigla_uf&campo7=cidade&campo8=ref_idtlog&campo9=isEnderecoExterno&campo10=cep_&campo11=sigla_uf&campo12=idtlog&campo13=id_cidade&campo14=zona_localizacao\'></iframe>');\">",
      $desativarCamposDefinidosViaCep
    );


    // estado

    $options = array(
      'label'    => 'Estado / Cidade',
      'value'    => $this->sigla_uf,
      'disabled' => $desativarCamposDefinidosViaCep,
      'inline'   => true,
      'required' => $enderecamentoObrigatorio
    );

    $helperOptions = array(
      'attrName' => 'sigla_uf'
    );

    $this->inputsHelper()->uf($options, $helperOptions);


    // cidade

    $options = array(
      'label'       => '',
      'placeholder' => 'Cidade',
      'value'       => $this->cidade,
      'max_length'  => 60,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('cidade', $options);


    // bairro

    $options = array(
      'label'       => 'Bairro / Zona localiza��o',
      'placeholder' => 'Bairro',
      'value'       => $this->bairro,
      'max_length'  => 40,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('bairro', $options);


    // zona localiza��o

    $zonas = App_Model_ZonaLocalizacao::getInstance();
    $zonas = $zonas->getEnums();
    $zonas = Portabilis_Array_Utils::insertIn(null, 'Zona localiza��o', $zonas);

    $options = array(
      'label'       => '',
      'placeholder' => 'Zona localiza��o',
      'value'       => $this->zona_localizacao,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'resources'   => $zonas,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->select('zona_localizacao', $options);


    // tipo logradouro

    $options = array(
      'label'       => 'Tipo / Logradouro',
      'value'       => $this->idtlog,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'inline'      => true,
      'required'    => $enderecamentoObrigatorio
    );

    $helperOptions = array(
      'attrName' => 'idtlog'
    );

    $this->inputsHelper()->tipoLogradouro($options, $helperOptions);


    // logradouro

    $options = array(
      'label'       => '',
      'placeholder' => 'Logradouro',
      'value'       => $this->logradouro,
      'max_length'  => 150,
      'disabled'    => $desativarCamposDefinidosViaCep,
      'required'    => $enderecamentoObrigatorio
    );

    $this->inputsHelper()->text('logradouro', $options);


    // complemento

    $options = array(
      'required'    => false,
      'value'       => $this->complemento,
      'max_length'  => 20
    );

    $this->inputsHelper()->text('complemento', $options);


    // numero

    $options = array(
      'required'    => false,
      'label'       => 'N�mero / Letra',
      'placeholder' => 'N�mero',
      'value'       => $this->numero,
      'max_length'  => 6,
      'inline'      => true
    );

    $this->inputsHelper()->integer('numero', $options);


    // letra

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Letra',
      'value'       => $this->letra,
      'max_length'  => 1,
      'size'        => 15
    );

    $this->inputsHelper()->text('letra', $options);


    // apartamento

    $options = array(
      'required'    => false,
      'label'       => 'N� apartamento / Bloco / Andar',
      'placeholder' => 'N� apartamento',
      'value'       => $this->apartamento,
      'max_length'  => 6,
      'inline'      => true
    );

    $this->inputsHelper()->integer('apartamento', $options);


    // bloco

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Bloco',
      'value'       => $this->bloco,
      'max_length'  => 20,
      'size'        => 15,
      'inline'      => true
    );

    $this->inputsHelper()->text('bloco', $options);


    // andar

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => 'Andar',
      'value'       => $this->andar,
      'max_length'  => 2
    );

    $this->inputsHelper()->integer('andar', $options);


    // contato

    $this->inputTelefone('1', 'Telefone residencial');
    $this->inputTelefone('mov', 'Celular');
    $this->inputTelefone('2', 'Telefone adicional');
    $this->inputTelefone('fax', 'Fax');

    $this->campoTexto('email', 'E-mail', $this->email, '50', '255', FALSE);


    // after change pessoa pai / mae

    if ($parentType)
      $this->inputsHelper()->hidden('parent_type', array('value' => $parentType));


    $styles = array(
      '/modules/Portabilis/Assets/Stylesheets/Frontend.css',
      '/modules/Portabilis/Assets/Stylesheets/Frontend/Resource.css',
      '/modules/Cadastro/Assets/Stylesheets/PessoaFisica.css'
    );

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

    $script = '/modules/Cadastro/Assets/Javascripts/PessoaFisica.js';
    Portabilis_View_Helper_Application::loadJavascript($this, $script);
  }

  function Novo() {
    return $this->createOrUpdate();
  }

  function Editar() {
    return $this->createOrUpdate($this->cod_pessoa_fj);
  }

  function Excluir() {
    echo '<script>document.location="atendidos_lst.php";</script>';
    return TRUE;
  }

  function afterChangePessoa($id) {
    Portabilis_View_Helper_Application::embedJavascript($this, "

      if(window.opener &&  window.opener.afterChangePessoa) {
        var parentType = \$j('#parent_type').val();

        if (parentType)
          window.opener.afterChangePessoa(self, parentType, $id, \$j('#nm_pessoa').val());
        else
          window.opener.afterChangePessoa(self, $id);
      }
      else
        document.location = 'atendidos_lst.php';

    ", $afterReady = true);
  }

  protected function loadAlunoByPessoaId($id) {
    $aluno            = new clsPmieducarAluno();
    $aluno->ref_idpes = $id;

    return $aluno->detalhe();
  }

  protected function inputPai() {
    $this->addParentsInput('pai');
  }

  protected function inputMae() {
    $this->addParentsInput('mae', 'm�e');
  }

  protected function addParentsInput($parentType, $parentTypeLabel = '') {
    if (! $parentTypeLabel)
      $parentTypeLabel = $parentType;

    if (! isset($this->_aluno))
      $this->_aluno = $this->loadAlunoByPessoaId($this->cod_pessoa_fj);

    $parentId = $this->{$parentType . '_id'};


    // mostra uma dica nos casos em que foi informado apenas o nome dos pais,
    //pela antiga interface do cadastro de alunos.

    if (! $parentId && $this->_aluno['nm_' . $parentType]) {
      $nome      = Portabilis_String_Utils::toLatin1($this->_aluno['nm_' . $parentType],
                                                     array('transform' => true, 'escape' => false));

      $inputHint = '<br /><b>Dica:</b> Foi informado o nome "' . $nome .
                   '" no cadastro de aluno,<br />tente pesquisar esta pessoa ' .
                   'pelo CPF ou RG, caso n�o encontre, cadastre uma nova pessoa.';
    }


    $hiddenInputOptions = array('options' => array('value' => $parentId));
    $helperOptions      = array('objectName' => $parentType, 'hiddenInputOptions' => $hiddenInputOptions);

    $options            = array('label'      => 'Pessoa ' . $parentTypeLabel,
                                'size'       => 50,
                                'required'   => false,
                                'input_hint' => $inputHint);

    $this->inputsHelper()->simpleSearchPessoa('nome', $options, $helperOptions);
  }

  protected function validatesCpf($cpf) {
    $isValid = true;

    if ($cpf && ! Portabilis_Utils_Validation::validatesCpf($cpf)) {
      $this->erros['id_federal'] = 'CPF inv�lido.';
      $isValid = false;
    }
    elseif($cpf) {
      $fisica      = new clsFisica();
      $fisica->cpf = idFederal2int($cpf);
      $fisica      = $fisica->detalhe();

      if ($fisica['cpf'] && $this->cod_pessoa_fj != $fisica['idpes']) {
        $link = "<a class='decorated' target='__blank' href='/intranet/atendidos_cad.php?cod_pessoa_fj=" .
                "{$fisica['idpes']}'>{$fisica['idpes']}</a>";

        $this->erros['id_federal'] = "CPF j� utilizado pela pessoa $link.";
        $isValid = false;
      }
    }

    return $isValid;
  }

  protected function createOrUpdate($pessoaIdOrNull = null) {
    if (! $this->validatesCpf($this->id_federal))
      return false;

    if (!$this->validatePhoto())
      return false;

    $pessoaId = $this->createOrUpdatePessoa($pessoaIdOrNull);

    $this->savePhoto($pessoaId);
    $this->createOrUpdatePessoaFisica($pessoaId);
    $this->createOrUpdateDocumentos($pessoaId);
    $this->createOrUpdateTelefones($pessoaId);
    $this->createOrUpdateEndereco($pessoaId);

    $this->afterChangePessoa($pessoaId);
    return true;
  }


  //envia foto e salva caminha no banco
   protected function savePhoto($id){
 
     if ($this->objPhoto!=null){
       
       $caminhoFoto = $this->objPhoto->sendPicture($id);
       if ($caminhoFoto!=''){
         //new clsCadastroFisicaFoto($id)->exclui();
         $obj = new clsCadastroFisicaFoto($id,$caminhoFoto);
         $detalheFoto = $obj->detalhe();
         if (is_array($detalheFoto) && count($detalheFoto)>0)
          $obj->edita();
         else
          $obj->cadastra();
       
         return true;
       } else{
         echo '<script>alert(\'Foto n�o salva.\')</script>';
         return false;
       }  
     }
   }
 
   // Retorna true caso a foto seja v�lida
   protected function validatePhoto(){
 
     $this->arquivoFoto = $_FILES["file"];
     if (!empty($this->arquivoFoto["name"])){      
       $this->objPhoto = new PictureController($this->arquivoFoto);
       if ($this->objPhoto->validatePicture()){
         return TRUE;
       } else {        
         $this->mensagem = $this->objPhoto->getErrorMessage();
         return false;
       }
       return false;
     }else{
       $this->objPhoto = null;
       return true;
     }
     
   }
 


  protected function createOrUpdatePessoa($pessoaId = null) {
    $pessoa        = new clsPessoa_();
    $pessoa->idpes = $pessoaId;
    $pessoa->nome  = addslashes($this->nm_pessoa);
    $pessoa->email = addslashes($this->email);

    $sql = "select 1 from cadastro.pessoa WHERE idpes = $1 limit 1";

    if (! $pessoaId || Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1) {
      $pessoa->tipo      = 'F';
      $pessoa->idpes_cad = $this->currentUserId();
      $pessoaId          = $pessoa->cadastra();
    }
    else {
      $pessoa->idpes_rev = $this->currentUserId();
      $pessoa->data_rev  = date('Y-m-d H:i:s', time());
      $pessoa->edita();
    }

    return $pessoaId;
  }

  protected function createOrUpdatePessoaFisica($pessoaId) {
    $fisica                     = new clsFisica();
    $fisica->idpes              = $pessoaId;
    $fisica->data_nasc          = Portabilis_Date_Utils::brToPgSQL($this->data_nasc);
    $fisica->sexo               = $this->sexo;
    $fisica->ref_cod_sistema    = 'NULL';
    $fisica->cpf                = $this->id_federal ? idFederal2int($this->id_federal) : 'NULL';
    $fisica->ideciv             = $this->estado_civil_id;
    $fisica->idpes_pai          = $this->pai_id ? $this->pai_id : "NULL";
    $fisica->idpes_mae          = $this->mae_id ? $this->mae_id : "NULL";
    $fisica->nacionalidade      = $_REQUEST['tipo_nacionalidade'];
    $fisica->idpais_estrangeiro = $_REQUEST['pais_origem_id'];
    $fisica->idmun_nascimento   = $_REQUEST['naturalidade_id'];

    $sql = "select 1 from cadastro.fisica WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $fisica->cadastra();
    else
      $fisica->edita();

    $this->createOrUpdateRaca($pessoaId, $this->cor_raca);
  }

  function createOrUpdateRaca($pessoaId, $corRaca) {
    $pessoaId = (int) $pessoaId;
    $corRaca  = (int) $corRaca;

    $raca = new clsCadastroFisicaRaca($pessoaId, $corRaca);

    if ($raca->existe())
      return $raca->edita();

    return $raca->cadastra();
  }

  protected function createOrUpdateDocumentos($pessoaId) {
    $documentos                             = new clsDocumento();
    $documentos->idpes                      = $pessoaId;


    // rg

    $documentos->rg                         = $_REQUEST['rg'];

    $documentos->data_exp_rg                = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_rg']
    );

    $documentos->idorg_exp_rg               = $_REQUEST['orgao_emissao_rg'];
    $documentos->sigla_uf_exp_rg            = $_REQUEST['uf_emissao_rg'];


    // certid�o civil


    // o tipo certid�o novo padr�o � apenas para exibi��o ao usu�rio,
    // n�o precisa ser gravado no banco
    //
    // quando selecionado um tipo diferente do novo formato,
    // � removido o valor de certidao_nascimento.
    //
    if ($_REQUEST['tipo_certidao_civil'] == 'certidao_nascimento_novo_formato') {
      $documentos->tipo_cert_civil     = null;
      $documentos->certidao_nascimento = $_REQUEST['certidao_nascimento'];
    }
    else {
      $documentos->tipo_cert_civil     = $_REQUEST['tipo_certidao_civil'];
      $documentos->certidao_nascimento = '';
    }

    $documentos->num_termo                  = $_REQUEST['termo_certidao_civil'];
    $documentos->num_livro                  = $_REQUEST['livro_certidao_civil'];
    $documentos->num_folha                  = $_REQUEST['folha_certidao_civil'];

    $documentos->data_emissao_cert_civil    = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_certidao_civil']
    );

    $documentos->sigla_uf_cert_civil        = $_REQUEST['uf_emissao_certidao_civil'];
    $documentos->cartorio_cert_civil        = addslashes($_REQUEST['cartorio_emissao_certidao_civil']);


    // carteira de trabalho

    $documentos->num_cart_trabalho          = $_REQUEST['carteira_trabalho'];
    $documentos->serie_cart_trabalho        = $_REQUEST['serie_carteira_trabalho'];

    $documentos->data_emissao_cart_trabalho = Portabilis_Date_Utils::brToPgSQL(
      $_REQUEST['data_emissao_carteira_trabalho']
    );

    $documentos->sigla_uf_cart_trabalho     = $_REQUEST['uf_emissao_carteira_trabalho'];


    // titulo de eleitor

    $documentos->num_tit_eleitor            = $_REQUEST['titulo_eleitor'];
    $documentos->zona_tit_eleitor           = $_REQUEST['zona_titulo_eleitor'];
    $documentos->secao_tit_eleitor          = $_REQUEST['secao_titulo_eleitor'];


    // Altera��o de documentos compativel com a vers�o anterior do cadastro,
    // onde era possivel criar uma pessoa, n�o informando os documentos,
    // o que n�o criaria o registro do documento, sendo assim, ao editar uma pessoa,
    // o registro do documento ser� criado, caso n�o exista.

    $sql = "select 1 from cadastro.documento WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $documentos->cadastra();
    else
      $documentos->edita();
  }

  protected function _createOrUpdatePessoaEndereco($pessoaId) {
    $endereco = new clsPessoaEndereco(
      $pessoaId,
      idFederal2Int($this->cep),
      $this->idlog,
      $this->idbai,
      $this->numero,
      addslashes($this->complemento),
      FALSE,
      addslashes($this->letra),
      addslashes($this->bloco),
      $this->apartamento,
      $this->andar
    );

    // for�ado exclus�o, assim ao cadastrar endereco_pessoa novamente,
    // ser� excluido endereco_externo (por meio da trigger fcn_aft_ins_endereco_pessoa).
    $endereco->exclui();
    $endereco->cadastra();
  }

  protected function _createOrUpdateEnderecoExterno($pessoaId) {
    $endereco = new clsEnderecoExterno(
      $pessoaId,
      '1',
      $this->idtlog,
      addslashes($this->logradouro),
      $this->numero,
      addslashes($this->letra),
      addslashes($this->complemento),
      addslashes($this->bairro),
      idFederal2int($this->cep_),
      addslashes($this->cidade),
      $this->sigla_uf,
      FALSE,
      addslashes($this->bloco),
      $this->apartamento,
      $this->andar,
      FALSE,
      FALSE,
      $this->zona_localizacao
    );

    // for�ado exclus�o, assim ao cadastrar endereco_externo novamente,
    // ser� excluido endereco_pessoa (por meio da trigger fcn_aft_ins_endereco_externo).
    $endereco->exclui();
    $endereco->cadastra();
  }

  protected function createOrUpdateEndereco($pessoaId) {
    $enderecoExterno = ! empty($this->cep_);

    if (! $enderecoExterno && $this->cep && $this->idbai && $this->idlog)
      $this->_createOrUpdatePessoaEndereco($pessoaId);

    elseif($enderecoExterno)
      $this->_createOrUpdateEnderecoExterno($pessoaId);
  }

  protected function createOrUpdateTelefones($pessoaId) {
    $telefones   = array();

    $telefones[] = new clsPessoaTelefone($pessoaId, 1, $this->telefone_1,   $this->ddd_telefone_1);
    $telefones[] = new clsPessoaTelefone($pessoaId, 2, $this->telefone_2,   $this->ddd_telefone_2);
    $telefones[] = new clsPessoaTelefone($pessoaId, 3, $this->telefone_mov, $this->ddd_telefone_mov);
    $telefones[] = new clsPessoaTelefone($pessoaId, 4, $this->telefone_fax, $this->ddd_telefone_fax);

    foreach ($telefones as $telefone)
      $telefone->cadastra();
  }

  // inputs usados em Gerar,
  // implementado estes metodos para n�o duplicar c�digo
  // uma vez que estes campos s�o usados v�rias vezes em Gerar.

  protected function inputTelefone($type, $typeLabel = '') {
    if (! $typeLabel)
      $typeLabel = "Telefone {$type}";

    // ddd

    $options = array(
      'required'    => false,
      'label'       => "(ddd) / {$typeLabel}",
      'placeholder' => 'ddd',
      'value'       => $this->{"ddd_telefone_{$type}"},
      'max_length'  => 3,
      'size'        => 3,
      'inline'      => true
    );

    $this->inputsHelper()->integer("ddd_telefone_{$type}", $options);


   // telefone

    $options = array(
      'required'    => false,
      'label'       => '',
      'placeholder' => $typeLabel,
      'value'       => $this->{"telefone_{$type}"},
      'max_length'  => 11
    );

    $this->inputsHelper()->integer("telefone_{$type}", $options);
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndex();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
