<?php

/**
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
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesFichaMedicaAluno.inc.php';
require_once 'include/modules/clsModulesUniformeAluno.inc.php';
require_once 'include/modules/clsModulesMoradiaAluno.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';
require_once 'Educacenso/Model/AlunoDataMapper.php';
require_once 'Transporte/Model/AlunoDataMapper.php';

require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';

require_once 'Portabilis/View/Helper/Application.php';


/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Aluno');
    $this->processoAp = 578;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  var $titulo;

  var $cod_aluno;
  var $ref_idpes_responsavel;
  var $idpes_pai;
  var $idpes_mae;
  var $ref_cod_pessoa_educ;
  var $ref_cod_aluno_beneficio;
  var $ref_cod_religiao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_idpes;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $nm_pai;
  var $nm_mae;
  var $ref_cod_raca;
  var $sus;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    // Verificação de permissão para cadastro.
    $this->obj_permissao = new clsPermissoes();

    $this->nivel_usuario = $this->obj_permissao->nivel_acesso($this->pessoa_logada);

    $this->titulo = 'Aluno - Detalhe';


    $this->cod_aluno = $_GET['cod_aluno'];

    $tmp_obj = new clsPmieducarAluno($this->cod_aluno);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
      header('Location: educar_aluno_lst.php');
      die();
    }
    else {
      foreach ($registro as $key => $value) {
        $this->$key = $value;
      }
    }

    if ($this->ref_idpes) {
      $obj_pessoa_fj = new clsPessoaFj($this->ref_idpes);
      $det_pessoa_fj = $obj_pessoa_fj->detalhe();

      $obj_fisica = new clsFisica($this->ref_idpes);
      $det_fisica = $obj_fisica->detalhe();

      $obj_fisica_raca = new clsCadastroFisicaRaca();
      $lst_fisica_raca = $obj_fisica_raca->lista( $this->ref_idpes );

      if ($lst_fisica_raca) {
        $det_fisica_raca = array_shift($lst_fisica_raca);

        $obj_raca = new clsCadastroRaca($det_fisica_raca['ref_cod_raca']);
        $det_raca = $obj_raca->detalhe();
      }

      $objFoto = new clsCadastroFisicaFoto($this->ref_idpes);
      $detalheFoto = $objFoto->detalhe();
      if ($detalheFoto)
        $caminhoFoto  = $detalheFoto['caminho'];

      $registro['nome_aluno'] = strtoupper($det_pessoa_fj['nome']);
      $registro['cpf']        = int2IdFederal($det_fisica['cpf']);
      $registro['data_nasc']  = dataToBrasil($det_fisica['data_nasc']);
      $registro['sexo']       = $det_fisica['sexo'] == 'F' ? 'Feminino' : 'Masculino';

      $obj_estado_civil       = new clsEstadoCivil();
      $obj_estado_civil_lista = $obj_estado_civil->lista();

      $lista_estado_civil = array();

      if ($obj_estado_civil_lista) {
        foreach ($obj_estado_civil_lista as $estado_civil) {
          $lista_estado_civil[$estado_civil['ideciv']] = $estado_civil['descricao'];
        }
      }

      $registro['ideciv'] = $lista_estado_civil[$det_fisica['ideciv']->ideciv];
      $registro['email']  = $det_pessoa_fj['email'];
      $registro['url']    = $det_pessoa_fj['url'];

      $registro['nacionalidade'] = $det_fisica['nacionalidade'];

      $registro['naturalidade']  = $det_fisica['idmun_nascimento']->detalhe();
      $registro['naturalidade']  = $registro['naturalidade']['nome'];

      $registro['pais_origem'] = $det_fisica['idpais_estrangeiro']->detalhe();
      $registro['pais_origem'] = $registro['pais_origem']['nome'];

      $registro['ref_idpes_responsavel'] = $det_fisica['idpes_responsavel'];

      $this->idpes_pai = $det_fisica['idpes_pai'];
      $this->idpes_mae = $det_fisica['idpes_mae'];

      $this->sus = $det_fisica['sus'];

      $this->nm_pai = $registro['nm_pai'];
      $this->nm_mae = $registro['nm_mae'];

      if ($this->idpes_pai) {
        $obj_pessoa_pai = new clsPessoaFj($this->idpes_pai);
        $det_pessoa_pai = $obj_pessoa_pai->detalhe();

        if ($det_pessoa_pai) {
          $registro['nm_pai'] = $det_pessoa_pai['nome'];

          // CPF
          $obj_cpf = new clsFisica($this->idpes_pai);
          $det_cpf = $obj_cpf->detalhe();

          if ($det_cpf['cpf']) {
            $this->cpf_pai = int2CPF($det_cpf['cpf']);
          }
        }
      }

      if ($this->idpes_mae) {
        $obj_pessoa_mae = new clsPessoaFj($this->idpes_mae);
        $det_pessoa_mae = $obj_pessoa_mae->detalhe();

        if ($det_pessoa_mae) {
          $registro['nm_mae'] = $det_pessoa_mae['nome'];

          // CPF
          $obj_cpf = new clsFisica($this->idpes_mae);
          $det_cpf = $obj_cpf->detalhe();

          if ($det_cpf['cpf']) {
            $this->cpf_mae = int2CPF($det_cpf['cpf']);
          }
        }
      }

      $registro['ddd_fone_1'] = $det_pessoa_fj['ddd_1'];
      $registro['fone_1']     = $det_pessoa_fj['fone_1'];

      $registro['ddd_fone_2'] = $det_pessoa_fj['ddd_2'];
      $registro['fone_2']     = $det_pessoa_fj['fone_2'];

      $registro['ddd_fax']  = $det_pessoa_fj['ddd_fax'];
      $registro['fone_fax'] = $det_pessoa_fj['fone_fax'];

      $registro['ddd_mov']  = $det_pessoa_fj['ddd_mov'];
      $registro['fone_mov'] = $det_pessoa_fj['fone_mov'];

      $obj_deficiencia_pessoa       = new clsCadastroFisicaDeficiencia();
      $obj_deficiencia_pessoa_lista = $obj_deficiencia_pessoa->lista($this->ref_idpes);

      if ($obj_deficiencia_pessoa_lista) {
        $deficiencia_pessoa = array();

        foreach ($obj_deficiencia_pessoa_lista as $deficiencia) {
          $obj_def = new clsCadastroDeficiencia($deficiencia['ref_cod_deficiencia']);
          $det_def = $obj_def->detalhe();

          $deficiencia_pessoa[$deficiencia['ref_cod_deficiencia']] = $det_def['nm_deficiencia'];
        }
      }

      $ObjDocumento = new clsDocumento($this->ref_idpes);
      $detalheDocumento = $ObjDocumento->detalhe();

      $registro['rg'] = $detalheDocumento['rg'];

      if ($detalheDocumento['data_exp_rg']) {
        $registro['data_exp_rg'] = date('d/m/Y',
          strtotime(substr($detalheDocumento['data_exp_rg'], 0, 19)));
      }

      $registro['sigla_uf_exp_rg'] = $detalheDocumento['sigla_uf_exp_rg'];
      $registro['tipo_cert_civil'] = $detalheDocumento['tipo_cert_civil'];
      $registro['num_termo']       = $detalheDocumento['num_termo'];
      $registro['num_livro']       = $detalheDocumento['num_livro'];
      $registro['num_folha']       = $detalheDocumento['num_folha'];

      if ($detalheDocumento['data_emissao_cert_civil']) {
        $registro['data_emissao_cert_civil'] = date('d/m/Y',
          strtotime(substr($detalheDocumento['data_emissao_cert_civil'], 0, 19)));
      }

      $registro['sigla_uf_cert_civil'] = $detalheDocumento['sigla_uf_cert_civil'];
      $registro['cartorio_cert_civil'] = $detalheDocumento['cartorio_cert_civil'];
      $registro['num_cart_trabalho']   = $detalheDocumento['num_cart_trabalho'];
      $registro['serie_cart_trabalho'] = $detalheDocumento['serie_cart_trabalho'];

      if ($detalheDocumento['data_emissao_cart_trabalho']) {
        $registro['data_emissao_cart_trabalho'] = date('d/m/Y',
          strtotime(substr($detalheDocumento['data_emissao_cart_trabalho'], 0, 19)));
      }

      $registro['sigla_uf_cart_trabalho'] = $detalheDocumento['sigla_uf_cart_trabalho'];
      $registro['num_tit_eleitor']        = $detalheDocumento['num_titulo_eleitor'];
      $registro['zona_tit_eleitor']       = $detalheDocumento['zona_titulo_eleitor'];
      $registro['secao_tit_eleitor']      = $detalheDocumento['secao_titulo_eleitor'];
      $registro['idorg_exp_rg']           = $detalheDocumento['ref_idorg_rg'];

      $obj_endereco = new clsPessoaEndereco($this->ref_idpes);

      if ($obj_endereco_det = $obj_endereco->detalhe()) {
        $registro['id_cep']        = $obj_endereco_det['cep']->cep;
        $registro['id_bairro']     = $obj_endereco_det['idbai']->idbai;
        $registro['id_logradouro'] = $obj_endereco_det['idlog']->idlog;
        $registro['numero']        = $obj_endereco_det['numero'];
        $registro['letra']         = $obj_endereco_det['letra'];
        $registro['complemento']   = $obj_endereco_det['complemento'];
        $registro['andar']         = $obj_endereco_det['andar'];
        $registro['apartamento']   = $obj_endereco_det['apartamento'];
        $registro['bloco']         = $obj_endereco_det['bloco'];
        $registro['nm_logradouro'] = $obj_endereco_det['logradouro'];
        $registro['cep_']          = int2CEP($registro['id_cep']);

        $obj_bairro     = new clsBairro($registro['id_bairro']);
        $obj_bairro_det = $obj_bairro->detalhe();

        if ($obj_bairro_det) {
          $registro['nm_bairro']= $obj_bairro_det['nome'];
        }

        $obj_log = new clsLogradouro($registro['id_logradouro']);
        $obj_log_det = $obj_log->detalhe();

        if ($obj_log_det) {
          $registro['nm_logradouro'] = $obj_log_det['nome'];
          $registro['idtlog']        = $obj_log_det['idtlog']->detalhe();
          $registro['idtlog']        = $registro['idtlog']['descricao'];

          $obj_mun = new clsMunicipio($obj_log_det['idmun']);
          $det_mun = $obj_mun->detalhe();

          if ($det_mun) {
            $registro['cidade'] = ucfirst(strtolower($det_mun['nome']));
          }
        }

        $obj_bairro = new clsBairro($registro["id_bairro"]);
        $obj_bairro_det = $obj_bairro->detalhe();

        if ($obj_bairro_det) {
          $registro['nm_bairro'] = $obj_bairro_det['nome'];
        }
      }
      else {
        $obj_endereco = new clsEnderecoExterno($this->ref_idpes);

        if ($obj_endereco_det = $obj_endereco->detalhe()) {
          $registro['id_cep']        = $obj_endereco_det['cep'];
          $registro['cidade']        = $obj_endereco_det['cidade'];
          $registro['nm_bairro']     = $obj_endereco_det['bairro'];
          $registro['nm_logradouro'] = $obj_endereco_det['logradouro'];
          $registro['numero']        = $obj_endereco_det['numero'];
          $registro['letra']         = $obj_endereco_det['letra'];
          $registro['complemento']   = $obj_endereco_det['complemento'];
          $registro['andar']         = $obj_endereco_det['andar'];
          $registro['apartamento']   = $obj_endereco_det['apartamento'];
          $registro['bloco']         = $obj_endereco_det['bloco'];
          $registro['idtlog']        = $obj_endereco_det['idtlog']->detalhe();
          $registro['idtlog']        = $registro['idtlog']['descricao'];

          $det_uf = $obj_endereco_det['sigla_uf']->detalhe();
          $registro['ref_sigla_uf'] = $det_uf['nome'];

          $registro['cep_'] = int2CEP($registro['id_cep']);
        }
      }
    }

    // Adiciona a informação de zona de localização junto ao bairro do
    // endereço.
    $zona = App_Model_ZonaLocalizacao::getInstance();
    $registro['nm_bairro'] = sprintf(
      '%s (Zona %s)',
      $registro['nm_bairro'], $zona->getValue($obj_endereco_det['zona_localizacao'])
    );

    if ($registro['cod_aluno']) {
      $this->addDetalhe(array('Código Aluno', $registro['cod_aluno']));
    }

    // código inep

    $alunoMapper = new Educacenso_Model_AlunoDataMapper();
    $alunoInep   = NULL;
    try {
      $alunoInep = $alunoMapper->find(array('aluno' => $this->cod_aluno));
      $this->addDetalhe(array('Código inep', $alunoInep->alunoInep));
    }
    catch(Exception $e) {
    }

    // código estado

    $this->addDetalhe(array('Código estado', $registro['aluno_estado_id']));

    if ($registro['caminho_foto']) {
      $this->addDetalhe(array(
        'Foto',
        sprintf(
          '<img src="arquivos/educar/aluno/small/%s" border="0">',
          $registro['caminho_foto']
        )
      ));
    }

    if ($registro['nome_aluno']) {
      if ($caminhoFoto!=null and $caminhoFoto!='')
        $this->addDetalhe(array('Nome Aluno', $registro['nome_aluno'].'<p><img height="117" src="'.$caminhoFoto.'"/></p>'));
      else
        $this->addDetalhe(array('Nome Aluno', $registro['nome_aluno']));
    }



    if (idFederal2int($registro['cpf'])) {
      $this->addDetalhe(array('CPF', $registro['cpf']));
    }

    if ($registro['data_nasc']) {
      $this->addDetalhe(array('Data de Nascimento', $registro['data_nasc']));
    }

    /**
     * Analfabeto.
     */
    $this->addDetalhe(array('Analfabeto', $registro['analfabeto'] == 0 ? 'Não' : 'Sim'));

    if ($registro['sexo']) {
      $this->addDetalhe(array('Sexo', $registro['sexo']));
    }

    if ($registro['ideciv']) {
      $this->addDetalhe(array('Estado Civil', $registro['ideciv']));
    }

    if ($registro['id_cep']) {
      $this->addDetalhe(array('CEP', $registro['cep_']));
    }

    if ($registro['ref_sigla_uf']) {
      $this->addDetalhe(array('UF', $registro['ref_sigla_uf']));
    }

    if ($registro['cidade']) {
      $this->addDetalhe(array('Cidade', $registro['cidade']));
    }

    if ($registro['nm_bairro']) {
      $this->addDetalhe(array('Bairro', $registro['nm_bairro']));
    }

    if ($registro['nm_logradouro']) {
      $logradouro = '';

      if ($registro['idtlog']) {
        $logradouro .= $registro['idtlog'] . ' ';
      }

      $logradouro .= $registro['nm_logradouro'];
      $this->addDetalhe(array('Logradouro', $logradouro));
    }

    if ($registro['numero']) {
      $this->addDetalhe(array('Número', $registro['numero']));
    }

    if ($registro['letra']) {
      $this->addDetalhe(array('Letra', $registro['letra']));
    }

    if ($registro['complemento']) {
      $this->addDetalhe(array('Complemento', $registro['complemento']));
    }

    if ($registro['bloco']) {
      $this->addDetalhe(array('Bloco', $registro['bloco']));
    }

    if ($registro['andar']) {
      $this->addDetalhe(array('Andar', $registro['andar']));
    }

    if ($registro['apartamento']) {
      $this->addDetalhe(array('Apartamento', $registro['apartamento']));
    }

    if ($registro['naturalidade']) {
      $this->addDetalhe(array('Naturalidade', $registro['naturalidade']));
    }

    if ($registro['nacionalidade']) {
      $lista_nacionalidade = array(
        'NULL' => 'Selecione',
        1      => 'Brasileiro',
        2      => 'Naturalizado Brasileiro',
        3      => 'Estrangeiro'
      );

      $registro['nacionalidade'] = $lista_nacionalidade[$registro['nacionalidade']];
      $this->addDetalhe(array('Nacionalidade', $registro['nacionalidade']));
    }

    if ($registro['pais_origem']) {
      $this->addDetalhe(array('País de Origem', $registro['pais_origem']));
    }

    $responsavel = $tmp_obj->getResponsavelAluno();

    if ($responsavel) {
      $this->addDetalhe(array('Responsável Aluno', $responsavel['nome_responsavel']));
    }

    if ($registro['ref_idpes_responsavel']) {
      $obj_pessoa_resp = new clsPessoaFj($registro['ref_idpes_responsavel']);
      $det_pessoa_resp = $obj_pessoa_resp->detalhe();

      if ($det_pessoa_resp) {
        $registro['ref_idpes_responsavel'] = $det_pessoa_resp['nome'];
      }

      $this->addDetalhe(array('Responsável', $registro['ref_idpes_responsavel']));
    }

    if ($registro['nm_pai']) {
      $this->addDetalhe(array('Pai', $registro['nm_pai']));
    }

    if ($registro["nm_mae"]) {
      $this->addDetalhe(array('Mãe', $registro['nm_mae']));
    }

    if ($registro['fone_1']) {
      if ($registro['ddd_fone_1']) {
        $registro['ddd_fone_1'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_1']);
      }

      $this->addDetalhe(array('Telefone 1', $registro['ddd_fone_1'] . $registro['fone_1']));
    }

    if ($registro['fone_2']) {
      if ($registro['ddd_fone_2']) {
        $registro['ddd_fone_2'] = sprintf('(%s)&nbsp;', $registro['ddd_fone_2']);
      }

      $this->addDetalhe(array('Telefone 2', $registro['ddd_fone_2'] . $registro['fone_2']));
    }

    if ($registro['fone_mov']) {
      if ($registro['ddd_mov']) {
        $registro['ddd_mov'] = sprintf('(%s)&nbsp;', $registro['ddd_mov']);
      }

      $this->addDetalhe(array('Celular', $registro['ddd_mov'] . $registro['fone_mov']));
    }

    if ($registro['fone_fax']) {
      if($registro['ddd_fax']) {
        $registro['ddd_fax'] = sprintf('(%s)&nbsp;', $registro['ddd_fax']);
      }

      $this->addDetalhe(array('Fax', $registro['ddd_fax'] . $registro['fone_fax']));
    }

    if ($registro['email']) {
      $this->addDetalhe(array('E-mail', $registro['email']));
    }

    if ($registro['url']) {
      $this->addDetalhe(array('Página Pessoal', $registro['url']));
    }

    if ($registro['ref_cod_aluno_beneficio']) {
      $obj_beneficio     = new clsPmieducarAlunoBeneficio($registro['ref_cod_aluno_beneficio']);
      $obj_beneficio_det = $obj_beneficio->detalhe();

      $this->addDetalhe(array('Benefício', $obj_beneficio_det['nm_beneficio']));
    }

    if ($registro['ref_cod_religiao']) {
      $obj_religiao     = new clsPmieducarReligiao($registro['ref_cod_religiao']);
      $obj_religiao_det = $obj_religiao->detalhe();

      $this->addDetalhe(array('Religião', $obj_religiao_det['nm_religiao']));
    }

    if ($det_raca['nm_raca']) {
      $this->addDetalhe(array('Raça', $det_raca['nm_raca']));
    }

    if ($deficiencia_pessoa) {
      $tabela = '<table border="0" width="300" cellpadding="3"><tr bgcolor="#A1B3BD" align="center"><td>Deficiências</td></tr>';
      $cor    = '#D1DADF';

      foreach ($deficiencia_pessoa as $indice => $valor) {
        $cor = $cor == '#D1DADF' ? '#E4E9ED' : '#D1DADF';

        $tabela .= sprintf('<tr bgcolor="%s" align="center"><td>%s</td></tr>',
          $cor, $valor);
      }

      $tabela .= '</table>';

      $this->addDetalhe(array('Deficiências', $tabela));
    }

    if ($registro['rg']) {
      $this->addDetalhe(array('RG', $registro['rg']));
    }

    if ($registro['data_exp_rg']) {
      $this->addDetalhe(array('Data de Expedição RG', $registro['data_exp_rg']));
    }

    if ($registro['idorg_exp_rg']) {
      $this->addDetalhe(array('Órgão Expedição RG', $registro['idorg_exp_rg']));
    }

    if ($registro['sigla_uf_exp_rg']) {
      $this->addDetalhe(array('Estado Expedidor', $registro['sigla_uf_exp_rg']));
    }

    /**
     * @todo CoreExt_Enum?
     */
    if ($registro['tipo_cert_civil']) {
      $lista_tipo_cert_civil       = array();
      $lista_tipo_cert_civil["0"] = 'Selecione';
      $lista_tipo_cert_civil[91]  = 'Nascimento';
      $lista_tipo_cert_civil[92]  = 'Casamento';

      $this->addDetalhe(array('Tipo Certificado Civil', $registro['tipo_cert_civil']));
    }

    if ($registro['num_termo']) {
      $this->addDetalhe(array('Termo', $registro['num_termo']));
    }

    if ($registro['num_livro']) {
      $this->addDetalhe(array('Livro', $registro['num_livro']));
    }

    if ($registro['num_folha']) {
      $this->addDetalhe(array('Folha', $registro['num_folha']));
    }

    if ($registro['data_emissao_cert_civil']) {
      $this->addDetalhe(array('Emissão Certidão Civil', $registro['data_emissao_cert_civil']));
    }

    if ($registro['sigla_uf_cert_civil']) {
      $this->addDetalhe(array('Sigla Certidão Civil', $registro['sigla_uf_cert_civil']));
    }

    if ($registro['cartorio_cert_civil']) {
      $this->addDetalhe(array('Cartório', $registro['cartorio_cert_civil']));
    }

    if ($registro['num_tit_eleitor']) {
      $this->addDetalhe(array('Título de Eleitor', $registro['num_tit_eleitor']));
    }

    if ($registro['zona_tit_eleitor']) {
      $this->addDetalhe(array('Zona', $registro['zona_tit_eleitor']));
    }

    if ($registro['secao_tit_eleitor']) {
      $this->addDetalhe(array('Seção', $registro['secao_tit_eleitor']));
    }

    // Transporte escolar.
    $transporteMapper = new Transporte_Model_AlunoDataMapper();
    $transporteAluno  = NULL;
    try {
      $transporteAluno = $transporteMapper->find(array('aluno' => $this->cod_aluno));
    }
    catch (Exception $e) {
    }

    $this->addDetalhe(array('Transporte escolar', isset($transporteAluno) && $transporteAluno->responsavel!='Não utiliza'  ? 'Sim' : 'Não'));
    if ($transporteAluno && $transporteAluno->responsavel!='Não utiliza') {
      $this->addDetalhe(array('Responsável transporte', $transporteAluno->responsavel));
    }

    if ($this->obj_permissao->permissao_cadastra(578, $this->pessoa_logada, 7)) {
      $this->url_novo   = '/module/Cadastro/aluno';
      $this->url_editar = '/module/Cadastro/aluno?id=' . $registro['cod_aluno'];

      $this->array_botao = array('Nova matrícula', 'Atualizar Histórico');
      $this->array_botao_url_script = array(
        sprintf('go("educar_matricula_cad.php?ref_cod_aluno=%d");', $registro['cod_aluno']),
        sprintf('go("educar_historico_escolar_lst.php?ref_cod_aluno=%d");', $registro['cod_aluno'])
      );
    }

    $objFichaMedica       = new clsModulesFichaMedicaAluno($this->cod_aluno);
    $reg                  = $objFichaMedica->detalhe();

    if($reg){

      $this->addDetalhe(array('<span id="fmedica"></span>Altura/metro', $reg['altura']));
      if (trim($reg['peso'])!='') $this->addDetalhe(array('Peso/kg', $reg['peso']));
      if (trim($reg['grupo_sanguineo'])!='') $this->addDetalhe(array('Grupo sanguíneo', $reg['grupo_sanguineo']));
      if (trim($reg['fator_rh'])!='') $this->addDetalhe(array('Fator RH', $reg['fator_rh']));
      if (trim($this->sus)!='') $this->addDetalhe(array('Número do cartão do SUS', $this->sus));
      $this->addDetalhe(array('Possui alergia a algum medicamento', ($reg['alergia_medicamento'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_alergia_medicamento'])!='') $this->addDetalhe(array('Quais', $reg['desc_alergia_medicamento']));
      $this->addDetalhe(array('Possui alergia a algum alimento', ($reg['alergia_alimento'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_alergia_alimento'])!='') $this->addDetalhe(array('Quais', $reg['desc_alergia_alimento']));
      $this->addDetalhe(array('Possui alguma doenca congênita', ($reg['doenca_congenita'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_doenca_congenita'])!='') $this->addDetalhe(array('Quais', $reg['desc_doenca_congenita']));
      $this->addDetalhe(array('É fumante', ($reg['fumante'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Já contraiu caxumba', ($reg['doenca_caxumba'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Já contraiu sarampo', ($reg['doenca_sarampo'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Já contraiu rubeola', ($reg['doenca_rubeola'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Já contraiu catapora', ($reg['doenca_catapora'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Já contraiu escarlatina', ($reg['doenca_escarlatina'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Já contraiu coqueluche', ($reg['doenca_coqueluche'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['doenca_outras'])!='') $this->addDetalhe(array('Outras doenças que o aluno já contraiu', $reg['doenca_outras']));
      $this->addDetalhe(array('Epilético', ($reg['epiletico'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Está em tratamento', ($reg['epiletico_tratamento'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Hemofílico', ($reg['hemofilico'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Hipertenso', ($reg['hipertenso'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Asmático', ($reg['asmatico'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Diabético', ($reg['diabetico'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Depende de insulina', ($reg['insulina'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('Faz tratamento médico', ($reg['tratamento_medico'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_tratamento_medico'])!='') $this->addDetalhe(array('Qual', $reg['desc_tratamento_medico']));
      $this->addDetalhe(array('Ingere medicação específica', ($reg['medicacao_especifica'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_medicacao_especifica'])!='') $this->addDetalhe(array('Qual', $reg['desc_medicacao_especifica']));
      $this->addDetalhe(array('Acompanhamento médico ou psicológico', ($reg['acomp_medico_psicologico'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_acomp_medico_psicologico'])!='') $this->addDetalhe(array('Motivo', $reg['desc_acomp_medico_psicologico']));
      $this->addDetalhe(array('Restrição para atividades físicas', ($reg['restricao_atividade_fisica'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_restricao_atividade_fisica'])!='') $this->addDetalhe(array('Qual', $reg['desc_restricao_atividade_fisica']));
      $this->addDetalhe(array('Teve alguma fratura ou trauma', ($reg['fratura_trauma'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_fratura_trauma'])!='') $this->addDetalhe(array('Qual', $reg['desc_fratura_trauma']));
      $this->addDetalhe(array('Tem plano de saúde', ($reg['plano_saude'] == 'S' ? 'Sim': 'Não') ));
      if (trim($reg['desc_plano_saude'])!='') $this->addDetalhe(array('Qual', $reg['desc_plano_saude']));
      $this->addDetalhe(array('<span id="tr_tit_dados_hospital">Em caso de emergência, levar para hospital ou clínica</span>'));
      $this->addDetalhe(array('Nome', $reg['hospital_clinica']));
      $this->addDetalhe(array('Endereço', $reg['hospital_clinica_endereco']));
      $this->addDetalhe(array('Telefone', $reg['hospital_clinica_telefone']));
      $this->addDetalhe(array('<span id="tr_tit_dados_responsavel">Em caso de emergência, se não for possível contatar os responsáveis, comunicar</span>'));
      $this->addDetalhe(array('Nome', $reg['responsavel']));
      $this->addDetalhe(array('Parentesco', $reg['responsavel_parentesco']));
      $this->addDetalhe(array('Telefone', $reg['responsavel_parentesco_telefone']));
      $this->addDetalhe(array('Celular', $reg['responsavel_parentesco_celular']));

    }

    $objUniforme       = new clsModulesUniformeAluno($this->cod_aluno);
    $reg               = $objUniforme->detalhe();

    if($reg){
      $this->addDetalhe(array('<span id="funiforme"></span>Recebeu uniforme escolar', ($reg['recebeu_uniforme'] == 'S' ? 'Sim': 'Não') ));
      $this->addDetalhe(array('<span class="tit_uniforme">Camiseta</span>'));
      $this->addDetalhe(array('Quantidade', $reg['quantidade_camiseta']));
      $this->addDetalhe(array('Tamanho', $reg['tamanho_camiseta']));
      $this->addDetalhe(array('<span class="tit_uniforme">Blusa/Jaqueta</span>'));
      $this->addDetalhe(array('Quantidade', $reg['quantidade_blusa_jaqueta']));
      $this->addDetalhe(array('Tamanho', $reg['tamanho_blusa_jaqueta']));
      $this->addDetalhe(array('<span class="tit_uniforme">Bermuda</span>'));
      $this->addDetalhe(array('Quantidade', $reg['quantidade_bermuda']));
      $this->addDetalhe(array('Tamanho', $reg['tamanho_bermuda']));
      $this->addDetalhe(array('<span class="tit_uniforme">Calça</span>'));
      $this->addDetalhe(array('Quantidade', $reg['quantidade_calca']));
      $this->addDetalhe(array('Tamanho', $reg['tamanho_calca']));
      $this->addDetalhe(array('<span class="tit_uniforme">Saia</span>'));
      $this->addDetalhe(array('Quantidade', $reg['quantidade_saia']));
      $this->addDetalhe(array('Tamanho', $reg['tamanho_saia']));
      $this->addDetalhe(array('<span class="tit_uniforme">Calçado</span>'));
      $this->addDetalhe(array('Quantidade', $reg['quantidade_calcado']));
      $this->addDetalhe(array('Tamanho', $reg['tamanho_calcado']));
      $this->addDetalhe(array('<span class="tit_uniforme">Meia</span>'));
      $this->addDetalhe(array('Quantidade', $reg['quantidade_meia']));
      $this->addDetalhe(array('Tamanho', $reg['tamanho_meia']));
    }

    $objMoradia        = new clsModulesMoradiaAluno($this->cod_aluno);
    $reg               = $objMoradia->detalhe();

    if($reg){

      $moradia = '';
      switch ($reg['moradia']) {
        case 'A':
          $moradia = 'Apartamento';
          break;
        case 'C':
          $moradia = 'Casa';
            switch ($reg['material']) {
              case 'A':
                $moradia.= ' de alvenaria';
                break;
              case 'M':
                $moradia.= ' de madeira';
                break;
              case 'I':
                $moradia.= ' mista';
                break;
            }
          break;
        case 'O':
          $moradia = 'Outra: '.$reg['casa_outra'];
          break;
        default:
          $moradia = 'Não informado';
      }

      $this->addDetalhe(array('<span id="fmoradia"></span>Moradia', $moradia ));
      $situacao;
      switch ($reg['moradia_situacao']) {
        case 1:
          $situacao = 'Alugado';
          break;
        case 2:
          $situacao = 'Próprio';
          break;
        case 3:
          $situacao = 'Cedido';
          break;
        case 4:
          $situacao = 'Financiado';
          break;
        case 5:
          $situacao = 'Outra';
          break;
      }
      $this->addDetalhe(array('Situação', $situacao));
      $this->addDetalhe(array('Quantidade de quartos', $reg['quartos']));
      $this->addDetalhe(array('Quantidade de salas', $reg['sala']));
      $this->addDetalhe(array('Quantidade de copas', $reg['copa']));
      $this->addDetalhe(array('Quantidade de banheiros', $reg['banheiro']));
      $this->addDetalhe(array('Quantidade de garagens', $reg['garagem']));
      $this->addDetalhe(array('Possui empregada doméstica', $reg['empregada_domestica']));
      $this->addDetalhe(array('Possui automóvel', $reg['automovel']));
      $this->addDetalhe(array('Possui motocicleta', $reg['motocicleta']));
      $this->addDetalhe(array('Possui computador', $reg['computador']));
      $this->addDetalhe(array('Possui geladeira', $reg['geladeira']));
      $this->addDetalhe(array('Possui fogão', $reg['fogao']));
      $this->addDetalhe(array('Possui máquina de lavar', $reg['maquina_lavar']));
      $this->addDetalhe(array('Possui microondas', $reg['microondas']));
      $this->addDetalhe(array('Possui vídeo/dvd', $reg['video_dvd']));
      $this->addDetalhe(array('Possui televisão', $reg['televisao']));
      $this->addDetalhe(array('Possui celular', $reg['celular']));
      $this->addDetalhe(array('Possui telefone', $reg['telefone']));
      $this->addDetalhe(array('Quantidade de pessoas', $reg['quant_pessoas']));
      $this->addDetalhe(array('Renda familiar', 'R$ '.$reg['renda']));
      $this->addDetalhe(array('Possui água encanada', $reg['agua_encanada']));
      $this->addDetalhe(array('Possui poço', $reg['poco']));
      $this->addDetalhe(array('Possui energia elétrica', $reg['energia']));
      $this->addDetalhe(array('Possui tratamento de esgoto', $reg['esgoto']));
      $this->addDetalhe(array('Possui fossa', $reg['fossa']));
      $this->addDetalhe(array('Possui coleta de lixo', $reg['lixo']));

    }

    $this->url_cancelar = 'educar_aluno_lst.php';
    $this->largura      = '100%';

    $this->addDetalhe("<input type='hidden' id='escola_id' name='aluno_id' value='{$registro['ref_cod_escola']}' />");
    $this->addDetalhe("<input type='hidden' id='aluno_id' name='aluno_id' value='{$registro['cod_aluno']}' />");

    // js

    Portabilis_View_Helper_Application::loadJQueryLib($this);

    $scripts = array(
      '/modules/Portabilis/Assets/Javascripts/Utils.js',
      '/modules/Portabilis/Assets/Javascripts/ClientApi.js',
      '/modules/Cadastro/Assets/Javascripts/AlunoShow.js?version=3'
      );

    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

    $styles = array ('/modules/Cadastro/Assets/Stylesheets/Aluno.css');

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
  }
}

// Instancia o objeto da página
$pagina = new clsIndexBase();

// Instancia o objeto de conteúdo
$miolo = new indice();

// Passa o conteúdo para a página
$pagina->addForm($miolo);

// Gera o HTML
$pagina->MakeAll();
