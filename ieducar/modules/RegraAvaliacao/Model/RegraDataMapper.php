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
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/DataMapper.php';
require_once 'RegraAvaliacao/Model/Regra.php';
require_once 'FormulaMedia/Model/TipoFormula.php';

/**
 * RegraAvaliacao_Model_RegraDataMapper class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class RegraAvaliacao_Model_RegraDataMapper extends CoreExt_DataMapper
{
  protected $_entityClass = 'RegraAvaliacao_Model_Regra';
  protected $_tableName   = 'regra_avaliacao';
  protected $_tableSchema = 'modules';

  protected $_attributeMap = array(
    'instituicao'          => 'instituicao_id',
    'tipoNota'             => 'tipo_nota',
    'tipoProgressao'       => 'tipo_progressao',
    'tabelaArredondamento' => 'tabela_arredondamento_id',
    'formulaMedia'         => 'formula_media_id',
    'formulaRecuperacao'   => 'formula_recuperacao_id',
    'porcentagemPresenca'  => 'porcentagem_presenca',
    'parecerDescritivo'    => 'parecer_descritivo',
    'tipoPresenca'         => 'tipo_presenca',
    'mediaRecuperacao'     => 'media_recuperacao',
  );

  /**
   * @var FormulaMedia_Model_FormulaDataMapper
   */
  protected $_formulaDataMapper = NULL;

  /**
   * @var TabelaArredondamento_Model_TabelaDataMapper
   */
  protected $_tabelaDataMapper = NULL;

  /**
   * Setter.
   * @param FormulaMedia_Model_FormulaDataMapper $mapper
   * @return RegraAvaliacao_Model_RegraDataMapper
   */
  public function setFormulaDataMapper(FormulaMedia_Model_FormulaDataMapper $mapper)
  {
    $this->_formulaDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return FormulaMedia_Model_FormulaDataMapper
   */
  public function getFormulaDataMapper()
  {
    if (is_null($this->_formulaDataMapper)) {
      require_once 'FormulaMedia/Model/FormulaDataMapper.php';
      $this->setFormulaDataMapper(new FormulaMedia_Model_FormulaDataMapper());
    }
    return $this->_formulaDataMapper;
  }

  /**
   * Setter.
   * @param TabelaArredondamento_Model_TabelaDataMapper $mapper
   * @return CoreExt_DataMapper Prov� interface flu�da
   */
  public function setTabelaDataMapper(TabelaArredondamento_Model_TabelaDataMapper $mapper)
  {
    $this->_tabelaDataMapper = $mapper;
    return $this;
  }

  /**
   * Getter.
   * @return TabelaArredondamento_Model_TabelaDataMapper
   */
  public function getTabelaDataMapper()
  {
    if (is_null($this->_tabelaDataMapper)) {
      require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
      $this->setTabelaDataMapper(new TabelaArredondamento_Model_TabelaDataMapper());
    }
    return $this->_tabelaDataMapper;
  }

  /**
   * Finder.
   * @return array Array de objetos FormulaMedia_Model_Formula
   */
  public function findFormulaMediaFinal($where = array())
  {
    return $this->_findFormulaMedia(array(
      $this->_getTableColumn('tipoFormula') => FormulaMedia_Model_TipoFormula::MEDIA_FINAL)
    );
  }

  /**
   * Finder.
   * @return array Array de objetos FormulaMedia_Model_Formula
   */
  public function findFormulaMediaRecuperacao($where = array())
  {
    return $this->_findFormulaMedia(array(
      $this->_getTableColumn('tipoFormula') => FormulaMedia_Model_TipoFormula::MEDIA_RECUPERACAO)
    );
  }

  /**
   * Finder gen�rico para FormulaMedia_Model_Formula.
   * @param array $where
   * @return array Array de objetos FormulaMedia_Model_Formula
   */
  protected function _findFormulaMedia(array $where = array())
  {
    return $this->getFormulaDataMapper()->findAll(array('nome'), $where);
  }

  /**
   * Finder para inst�ncias de TabelaArredondamento_Model_Tabela. Utiliza
   * o valor de institui��o por inst�ncias que referenciem a mesma institui��o.
   *
   * @param RegraAvaliacao_Model_Regra $instance
   * @return array
   */
  public function findTabelaArredondamento(RegraAvaliacao_Model_Regra $instance)
  {
    $where = array();

    if (isset($instance->instituicao)) {
      $where['instituicao'] = $instance->instituicao;
    }
    if (isset($instance->tipoNota)) {
      $where['tipoNota'] = $instance->get('tipoNota');
    }

    return $this->getTabelaDataMapper()->findAll(array(), $where);
  }
}
