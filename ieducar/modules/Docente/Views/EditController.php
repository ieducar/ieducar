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
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Docente
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'Educacenso/Model/CursoSuperiorDataMapper.php';
require_once 'Educacenso/Model/IesDataMapper.php';
require_once 'Docente/Model/LicenciaturaDataMapper.php';

require_once 'include/public/clsPublicUf.inc.php';

/**
 * EditController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Docente
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class EditController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'Docente_Model_LicenciaturaDataMapper';
  protected $_titulo            = 'Cadastro de Curso Superior/Licenciatura';
  protected $_processoAp        = 635;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = TRUE;

  protected $_formMap = array(
    'servidor' => array(
      'label'  => '',
      'help'   => '',
      'entity' => 'servidor'
    ),
    'licenciatura' => array(
      'label'  => 'Licenciatura',
      'help'   => '',
      'entity' => 'licenciatura'
    ),
    'curso' => array(
      'label'  => 'Curso',
      'help'   => '',
      'entity' => 'curso'
    ),
    'anoConclusao' => array(
      'label'  => 'Ano conclus�o',
      'help'   => '',
      'entity' => 'anoConclusao'
    ),
    'ies' => array(
      'label'  => 'IES',
      'help'   => '',
      'entity' => 'ies'
    ),
    'user' => array(
      'label'  => '',
      'help'   => '',
      'entity' => 'user'
    ),
    'created_at' => array(
      'label'  => '',
      'help'   => '',
      'entity' => 'created_at'
    )
  );

  protected function _preConstruct()
  {
    $params = array(
      'id'          => $this->getRequest()->id,
      'servidor'    => $this->getRequest()->servidor,
      'instituicao' => $this->getRequest()->instituicao
    );
    $this->setOptions(array('new_success_params'  => $params));
    $this->setOptions(array('edit_success_params' => $params));

    unset($params['id']);
    $this->setOptions(array('delete_success_params' => $params));
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    global $coreExt;

    $this->campoOculto('id', $this->getEntity()->id);
    $this->campoOculto('servidor', $this->getRequest()->servidor);

    $cursoSuperiorMapper = new Educacenso_Model_CursoSuperiorDataMapper();
    $cursos = $cursoSuperiorMapper->findAll(array(), array(), array('id' => 'ASC', 'nome' => 'ASC'));

    // Licenciatura
    $licenciatura = $this->getEntity()->get('licenciatura') ?
      $this->getEntity()->get('licenciatura') : 0;

    $this->campoRadio('licenciatura', $this->_getLabel('licenciatura'),
      array(1 => 'Sim', 0 => 'N�o'), $licenciatura);

    // Curso
    $opcoes = array();
    foreach ($cursos as $curso) {
      $opcoes[$curso->id] = $curso->nome;
    }

    $this->campoLista(
      'curso', $this->_getLabel('curso'), $opcoes, $this->getEntity()->get('curso')
    );

    // Ano conclus�o
    $opcoes = range(1960, date('Y'));
    rsort($opcoes);
    $opcoes = array_combine($opcoes, $opcoes);
    $this->campoLista(
      'anoConclusao', $this->_getLabel('anoConclusao'), $opcoes, $this->getEntity()->anoConclusao
    );

    // UF da IES.
    $ufs = new clsPublicUf();
    $ufs = $ufs->lista();

    $opcoes = array();
    foreach ($ufs as $uf) {
      $opcoes[$uf['sigla_uf']] = $uf['sigla_uf'];
    }
    ksort($opcoes);

    // Caso n�o seja uma inst�ncia persistida, usa a UF do locale.
    $uf = $this->getEntity()->ies->uf ?
      $this->getEntity()->ies->uf : $coreExt['Config']->app->locale->province;

    $this->campoLista('uf', 'UF', $opcoes, $uf, 'getIes()');

    // IES.
    $opcoes = array();
    $iesMapper = new Educacenso_Model_IesDataMapper();
    $iesUf = $iesMapper->findAll(array(), array('uf' => $uf));

    foreach ($iesUf as $ies) {
      $opcoes[$ies->id] = $ies->nome;
    }

    // Adiciona a institui��o "N�o cadastrada".
    $ies = $iesMapper->find(array('ies' => 9999999));
    $opcoes[$ies->id] = $ies->nome;

    $this->campoLista(
      'ies', $this->_getLabel('ies'), $opcoes, $this->getEntity()->ies->id
    );

    $this->url_cancelar = sprintf(
      'index?servidor=%d&instituicao=%d',
      $this->getRequest()->servidor, $this->getRequest()->instituicao
    );

    // Javascript para Ajax.
    echo
<<<EOT
      <script type="text/javascript">
      function getIes()
      {
        var ies = document.getElementById('ies').value;
        var uf  = document.getElementById('uf').value;

        var url  = '/modules/Educacenso/Views/IesAjaxController.php';
        var pars = '?uf=' + uf;

        var xml1 = new ajax(getIesXml);
        xml1.envia(url + pars);
      }

      function getIesXml(xml)
      {
        var ies = document.getElementById('ies');

        ies.length     = 1;
        ies.options[0] = new Option('Selecione uma IES', '', false, false);

        var iesItems = xml.getElementsByTagName('ies');

        for (var i = 0; i < iesItems.length; i++) {
          ies.options[ies.options.length] = new Option(
            iesItems[i].firstChild.nodeValue, iesItems[i].getAttribute('id'), false, false
          );
        }

        if (ies.length == 1) {
          ies.options[0] = new Option(
            'A UF n�o possui IES.', '', false, false
          );
        }
      }
      </script>
EOT;
  }

  public function Novo()
  {
    $_POST['user']       = $this->getOption('id_usuario');
    $_POST['created_at'] = 'NOW()';
    parent::Novo();
  }
}