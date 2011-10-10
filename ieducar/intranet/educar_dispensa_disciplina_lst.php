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
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Dispensa Componente Curricular');
    $this->processoAp = 578;
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
class indice extends clsListagem
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $ref_cod_matricula;
  var $ref_cod_serie;
  var $ref_cod_escola;
  var $ref_cod_disciplina;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_tipo_dispensa;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $observacao;
  var $ref_sequencial;

  var $ref_cod_instituicao;
  var $ref_cod_turma;

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    // Helper para url
    $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

    $this->titulo = 'Dispensa Componente Curricular - Listagem';

    // passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    if (!$_GET['ref_cod_matricula']) {
      header('Location: educar_matricula_lst.php');
      die();
    }

    $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

    $obj_matricula = new clsPmieducarMatricula();
    $lst_matricula = $obj_matricula->lista($this->ref_cod_matricula);

    if (is_array($lst_matricula)) {
      $det_matricula             = array_shift($lst_matricula);
      $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
      $this->ref_cod_escola      = $det_matricula['ref_ref_cod_escola'];
      $this->ref_cod_serie       = $det_matricula['ref_ref_cod_serie'];

      $obj_matricula_turma = new clsPmieducarMatriculaTurma();
      $lst_matricula_turma = $obj_matricula_turma->lista($this->ref_cod_matricula,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, $this->ref_cod_serie, NULL,
        $this->ref_cod_escola);

      if (is_array($lst_matricula_turma)) {
        $det                  = array_shift($lst_matricula_turma);
        $this->ref_cod_turma  = $det['ref_cod_turma'];
        $this->ref_sequencial = $det['sequencial'];
      }
    }

    $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);

    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg',
      'Intranet');

    $this->addCabecalhos(array(
      'Disciplina',
      'Tipo Dispensa',
      'Data Dispensa'
    ));

    // Filtros de Foreign Keys
    $opcoes = array('' => 'Selecione');
      $objTemp = new clsPmieducarTipoDispensa();

    if ($this->ref_cod_instituicao) {
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1, $this->ref_cod_instituicao);
    }
    else {
      $lista = $objTemp->lista(NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
    }

    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $opcoes[$registro['cod_tipo_dispensa']] = $registro['nm_tipo'];
      }
    }

    $this->campoLista('ref_cod_tipo_dispensa', 'Motivo', $opcoes,
      $this->ref_cod_tipo_dispensa, '', FALSE, '', '', FALSE, FALSE);

    $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

    // outros Filtros
    $opcoes = array('' => 'Selecione');

    // Escola s�rie disciplina
    $componentes = App_Model_IedFinder::getComponentesTurma(
      $this->ref_cod_serie, $this->ref_cod_escola, $this->ref_cod_turma
    );

    foreach ($componentes as $componente) {
      $opcoes[$componente->id] = $componente->nome;
    }

    $this->campoLista('ref_cod_disciplina', 'Disciplina', $opcoes,
      $this->ref_cod_disciplina, '', FALSE, '', '', FALSE, FALSE);

    // Paginador
    $this->limite = 20;
    $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

    $obj_dispensa_disciplina = new clsPmieducarDispensaDisciplina();
    $obj_dispensa_disciplina->setOrderby('data_cadastro ASC');
    $obj_dispensa_disciplina->setLimite($this->limite, $this->offset);

    $lista = $obj_dispensa_disciplina->lista(
      $this->ref_cod_matricula,
      NULL,
      NULL,
      $this->ref_cod_disciplina,
      NULL,
      NULL,
      $this->ref_cod_tipo_dispensa,
      NULL,
      NULL,
      NULL,
      NULL,
      1
    );

    $total = $obj_dispensa_disciplina->_total;

    // Mapper de componente curricular
    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    // monta a lista
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        // muda os campos data
        $registro['data_cadastro_time'] = strtotime(substr($registro['data_cadastro'], 0, 16));
        $registro['data_cadastro_br']   = date('d/m/Y', $registro['data_cadastro_time']);

        // Tipo da dispensa
        $obj_ref_cod_tipo_dispensa = new clsPmieducarTipoDispensa($registro['ref_cod_tipo_dispensa']);
        $det_ref_cod_tipo_dispensa = $obj_ref_cod_tipo_dispensa->detalhe();
        $registro['ref_cod_tipo_dispensa'] = $det_ref_cod_tipo_dispensa['nm_tipo'];

        // Componente curricular
        $componente = $componenteMapper->find($registro['ref_cod_disciplina']);

        // Dados para a url
        $url     = 'educar_dispensa_disciplina_det.php';
        $options = array('query' => array(
          'ref_cod_matricula'  => $registro['ref_cod_matricula'],
          'ref_cod_serie'      => $registro['ref_cod_serie'],
          'ref_cod_escola'     => $registro['ref_cod_escola'],
          'ref_cod_disciplina' => $registro['ref_cod_disciplina']
        ));

        $this->addLinhas(array(
          $urlHelper->l($componente->nome, $url, $options),
          $urlHelper->l($registro['ref_cod_tipo_dispensa'], $url, $options),
          $urlHelper->l($registro['data_cadastro_br'], $url, $options)
        ));
      }
    }

    $this->addPaginador2('educar_dispensa_disciplina_lst.php', $total, $_GET,
      $this->nome, $this->limite);

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
      $this->array_botao_url[] = 'educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
      $this->array_botao[]     = 'Novo';
    }

    $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
    $this->array_botao[]     = 'Voltar';

    $this->largura = '100%';
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �  p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();