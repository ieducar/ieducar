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
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'Core/View/Tabulable.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'Portabilis/View/Helper/Application.php';

/**
 * Core_Controller_Page_ViewController abstract class.
 *
 * Prov� um controller padr�o para a visualiza��o de um registro.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class Core_Controller_Page_ViewController extends clsDetalhe implements Core_View_Tabulable
{
  /**
   * Mapeia um nome descritivo a um atributo de CoreExt_Entity retornado pela
   * inst�ncia CoreExt_DataMapper retornada por getDataMapper().
   *
   * Para uma inst�ncia de CoreExt_Entity que tenha os seguintes atributos:
   * <code>
   * <?php
   * $_data = array(
   *   'nome' => NULL
   *   'idade' => NULL,
   *   'data_validacao' => NULL
   * );
   * </code>
   *
   * O mapeamento poderia ser feito da seguinte forma:
   * <code>
   * <?php
   * $_tableMap = array(
   *   'Nome' => 'nome',
   *   'Idade (anos)' => 'idade'
   * );
   * </code>
   *
   * Se um atributo n�o for mapeado, ele n�o ser� exibido por padr�o durante
   * a gera��o de HTML na execu��o do m�todo Gerar().
   *
   * @var array
   */
  protected $_tableMap = array();

  /**
   * Construtor.
   * @todo Criar interface de hooks semelhante ao controller Edit.
   */
  public function __construct()
  {
    $this->titulo  = $this->getBaseTitulo();
    $this->largura = "100%";
  }

  /**
   * Getter.
   * @see Core_View_Tabulable#getTableMap()
   */
  public function getTableMap()
  {
    return $this->_tableMap;
  }

  /**
   * Configura a URL padr�o para a a��o de Edi��o de um registro.
   *
   * Por padr�o, cria uma URL "edit/id", onde id � o valor do atributo "id"
   * de uma inst�ncia CoreExt_Entity.
   *
   * @param CoreExt_Entity $entry A inst�ncia atual recuperada
   *   ViewController::Gerar().
   */
  public function setUrlEditar(CoreExt_Entity $entry)
  {
    $this->url_editar = CoreExt_View_Helper_UrlHelper::url(
      'edit', array('query' => array('id' => $entry->id))
    );
  }

  /**
   * Configura a URL padr�o para a a��o Cancelar da tela de Edi��o de um
   * registro.
   *
   * Por padr�o, cria uma URL "index".
   *
   * @param CoreExt_Entity $entry A inst�ncia atual recuperada
   *   ViewController::Gerar().
   */
  public function setUrlCancelar(CoreExt_Entity $entry)
  {
    $this->url_cancelar = CoreExt_View_Helper_UrlHelper::url('index');
  }

  /**
   * Implementa��o padr�o para as subclasses que estenderem essa classe. Cria
   * uma tela de apresenta��o de dados simples utilizando o mapeamento de
   * $_tableMap.
   *
   * @see Core_Controller_Page_ViewController#$_tableMap
   * @see clsDetalhe#Gerar()
   */
  public function Gerar()
  {
    $headers = $this->getTableMap();
    $mapper  = $this->getDataMapper();

    $this->titulo  = $this->getBaseTitulo();
    $this->largura = "100%";

    try {
      $entry = $mapper->find($this->getRequest()->id);
    } catch (Exception $e) {
      $this->mensagem = $e;
      return FALSE;
    }

    foreach ($headers as $label => $attr) {
      $value = $entry->$attr;
      if (!is_null($value)) {
        $this->addDetalhe(array($label, $value));
      }
    }

    $this->setUrlEditar($entry);
    $this->setUrlCancelar($entry);
  }
}