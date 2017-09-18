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
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
require_once 'Avaliacao/Service/Boletim.php';

/**
 * NotaController class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @todo        Criar interface alternativa a Core_Controller_Page_EditController
 *   j� que nem todos os formul�rios mapearam 1:1 a inst�ncias CoreExt_DataMapper.
 * @version     @@package_version@@
 */
class NotaController extends Core_Controller_Page_EditController
{
  protected $_dataMapper        = 'Avaliacao_Model_NotaComponenteDataMapper';
  protected $_titulo            = 'Avalia��o do aluno | Nota';
  protected $_processoAp        = 642;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption        = TRUE;
  protected $_deleteOption      = FALSE;

  /**
   * @var Avaliacao_Service_Boletim
   */
  protected $_service = NULL;

  /**
   * @var RegraAvaliacao_Model_Regra
   */
  protected $_regra = NULL;

  /**
   * @var int
   */
  protected $_matricula = NULL;

  /**
   * @var int
   */
  protected $_componenteCurricular = NULL;

  /**
   * @var string
   */
  protected $_etapa = NULL;

  /**
   * @var Avaliacao_Model_NotaComponente
   */
  protected $_nota = NULL;

  /**
   * @var Avaliacao_Model_FaltaAbstract
   */
  protected $_falta   = NULL;

  /**
   * @var Avaliacao_Model_ParecerDescritivoAbstract
   */
  protected $_parecer = NULL;

  /**
   * @see Core_Controller_Page_EditController#_preConstruct()
   */
  protected function _preConstruct()
  {
    // Id do usu�rio na session
    $usuario = $this->getSession()->id_pessoa;

    $this->_options = array(
      'new_success'         => 'boletim',
      'new_success_params'  => array('matricula' => $this->getRequest()->matricula),
      'edit_success'        => 'boletim',
      'edit_success_params' => array('matricula' => $this->getRequest()->matricula),
    );

    $this->_service = new Avaliacao_Service_Boletim(array(
      'matricula' => $this->getRequest()->matricula,
      'usuario'   => $usuario
    ));

    $this->_regra = $this->_service->getRegra();
  }

  /**
   * @see Core_Controller_Page_EditController#_initNovo()
   */
  protected function _initNovo()
  {
    $this->_etapa = $this->getRequest()->etapa;
    $this->_matricula = $this->getRequest()->matricula;
    $this->_componenteCurricular = $this->getRequest()->componenteCurricular;

    if (isset($this->_etapa) && isset($this->_matricula) && isset($this->_componenteCurricular)) {
      return FALSE;
    }

    // Determina a etapa atual.
    $this->_etapa = 1;
    $notas = $this->_service->getNotasComponentes();
    if (isset($notas[$this->_componenteCurricular])) {
      $this->_etapa = count($notas[$this->_componenteCurricular]) + 1;
    }

    return TRUE;
  }

  /**
   * @see Core_Controller_Page_EditController#_initEditar()
   */
  protected function _initEditar()
  {
    $this->_nota    = $this->_service->getNotaComponente($this->_componenteCurricular, $this->_etapa);
    $this->_falta   = $this->_service->getFalta($this->_etapa, $this->_componenteCurricular);
    $this->_parecer = $this->_service->getParecerDescritivo($this->_etapa, $this->_componenteCurricular);
    return TRUE;
  }

  /**
   * @see clsCadastro#Gerar()
   */
  public function Gerar()
  {
    $this->campoOculto('matricula', $this->_matricula);
    $this->campoOculto('componenteCurricular', $this->_componenteCurricular);
    $this->campoOculto('etapa', $this->_etapa);

    $matricula = $this->_service->getOption('matriculaData');

    $this->campoRotulo('1nome', 'Nome', $matricula['nome']);
    $this->campoRotulo('2curso', 'Curso', $matricula['curso_nome']);
    $this->campoRotulo('3serie', 'S�rie', $matricula['serie_nome']);
    $this->campoRotulo('4turma', 'Turma', $matricula['turma_nome']);
    $this->campoRotulo('5etapa', 'Etapa', $this->_etapa == 'Rc' ? 'Exame' : $this->_etapa);

    $componentes = $this->_service->getComponentes();
    $this->campoRotulo('6componente_curricular', 'Componente curricular', $componentes[$this->getRequest()->componenteCurricular]);

    // Valores de arredondamento
    $valoresArredondamento = $this->_service->getRegra()->tabelaArredondamento->findTabelaValor();

    $valores = array();
    foreach ($valoresArredondamento as $valor) {
      if ($this->_service->getRegra()->get('tipoNota') == RegraAvaliacao_Model_Nota_TipoValor::NUMERICA) {
        $valores[(string) $valor->nome] = $valor->nome;
      }
      else {
        $valores[(string) $valor->valorMaximo] = $valor->nome . ' (' . $valor->descricao .  ')';
      }
    }

    $this->campoLista('nota', 'Nota', $valores, urldecode($this->_nota->nota));

    // Caso a falta seja calculada por componente
    if ($this->_regra->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
      $this->campoLista('falta', 'Falta', range(0, 100, 1), $this->_falta->quantidade);
    }

    // Caso o parecer seja por etapa e por componente
    if ($this->_regra->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE) {
      $this->campoMemo('parecer', 'Parecer', $this->_parecer, 40, 10, false);
    }
  }

  /**
   * @see Core_Controller_Page_EditController#_save()
   */
  protected function _save()
  {
    $nota = new Avaliacao_Model_NotaComponente(array(
      'componenteCurricular' => $this->getRequest()->componenteCurricular,
      'nota' => urldecode($this->getRequest()->nota),
      'etapa' => $this->getRequest()->etapa
    ));

    $this->_service->addNota($nota);

    if ($this->_regra->get('tipoPresenca') == RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE) {
      $quantidade = 0 < $this->getRequest()->falta ? (int) $this->getRequest()->falta : 0;
      $falta = new Avaliacao_Model_FaltaComponente(array(
        'componenteCurricular' => $this->getRequest()->componenteCurricular,
        'quantidade' => $quantidade,
        'etapa' => $this->getRequest()->etapa
      ));
      $this->_service->addFalta($falta);
    }

    if (trim($this->getRequest()->parecer) != '' && $this->_regra->get('parecerDescritivo') == RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE) {
      $parecer = new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => $this->getRequest()->componenteCurricular,
        'parecer'              => $this->getRequest()->parecer,
        'etapa'                => $this->getRequest()->etapa
      ));
      $this->_service->addParecer($parecer);
    }

    try {
      $this->_service->save();
    }
    catch (CoreExt_Service_Exception $e) {
      // Ok. N�o pode promover por se tratar de progress�o manual ou por estar em andamento
    }
    catch (Exception $e) {
      $this->mensagem = 'Erro no preenchimento do formul�rio. ';
      return FALSE;
    }

    return TRUE;
  }
}
