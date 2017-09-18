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

require_once 'CoreExt/Controller/Abstract.php';
require_once 'Core/Controller/Page/Interface.php';
require_once 'Core/Controller/Page/Exception.php';
require_once 'CoreExt/Configurable.php';
require_once 'CoreExt/Exception/InvalidArgumentException.php';

/**
 * Core_Controller_Page_Abstract abstract class.
 *
 * Prov� uma implementa��o b�sica de um
 * {@link http://martinfowler.com/eaaCatalog/pageController.html page controller}.
 *
 * Sua funcionalidade est� integrada com o uso dos componentes
 * CoreExt_Entity e CoreExt_DataMapper.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_Controller
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
abstract class Core_Controller_Page_Abstract
  extends CoreExt_Controller_Abstract
  implements Core_Controller_Page_Interface
{
  /**
   * Op��es de configura��o geral da classe.
   * @var array
   */
  protected $_options = array(
    'id_usuario'            => NULL,
    'new_success'           => 'index',
    'new_success_params'    => array(),
    'edit_success'          => 'view',
    'edit_success_params'   => array(),
    'delete_success'        => 'index',
    'delete_success_params' => array(),
    'url_cancelar'          => NULL,
  );

  /**
   * Cole��o de mensagens de erros retornados pelos validadores de
   * CoreExt_Entity.
   * @var array
   */
  protected $_errors = array();

  /**
   * Inst�ncia de Core_View
   * @var Core_View
   */
  protected $_view = NULL;

  /**
   * Inst�ncia de CoreExt_DataMapper
   * @var CoreExt_DataMapper
   */
  protected $_dataMapper = NULL;

  /**
   * Inst�ncia de CoreExt_Entity
   * @var CoreExt_Entity
   */
  protected $_entity = NULL;

  /**
   * Identificador do n�mero de processo para verifica��o de autoriza��o.
   * @see clsBase#verificaPermissao()
   * @var int
   */
  protected $_processoAp = NULL;

  /**
   * T�tulo a ser utilizado na barra de t�tulo.
   * @see clsBase#MakeHeadHtml()
   * @var string
   */
  protected $_titulo = NULL;

  /**
   * Array com labels para bot�es, inseridos no HTML via RenderHTML(). Marcado
   * como public para manter compatibilidade com as classes cls(Cadastro|Detalhe|
   * Listagem) que acessam o array diretamente.
   * @var array|NULL
   */
  public $array_botao = NULL;

  /**
   * Array com labels para bot�es, inseridos no HTML via RenderHTML(). Marcado
   * como public para manter compatibilidade com as classes cls(Cadastro|Detalhe|
   * Listagem) que acessam o array diretamente.
   * @var array|NULL
   */
  public $array_botao_url = NULL;

  /**
   * @var string
   */
  public $url_cancelar = NULL;

  /**
   * @var array
   */
  private $_output = array();

  /**
   * Construtor.
   */
  public function __construct()
  {
    $this->_options['id_usuario'] = $this->getSession()->id_pessoa;
  }

  /**
   * @see CoreExt_Configurable#setOptions($options)
   */
  public function setOptions(array $options = array())
  {
    $options = array_change_key_case($options, CASE_LOWER);

    if (isset($options['datamapper'])) {
      $this->setDataMapper($options['datamapper']);
      unset($options['datamapper']);
    }

    if (isset($options['processoap'])) {
      $this->setBaseProcessoAp($options['processoap']);
      unset($options['processoap']);
    }

    if (isset($options['titulo'])) {
      $this->setBaseTitulo($options['titulo']);
      unset($options['titulo']);
    }

    $defaultOptions = array_keys($this->getOptions());
    $passedOptions  = array_keys($options);

    if (0 < count(array_diff($passedOptions, $defaultOptions))) {
      throw new CoreExt_Exception_InvalidArgumentException(
        sprintf('A classe %s n�o suporta as op��es: %s.', get_class($this), implode(', ', $passedOptions))
      );
    }

    $this->_options = array_merge($this->getOptions(), $options);
    return $this;
  }

  /**
   * @see CoreExt_Configurable#getOptions()
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Setter.
   * @param CoreExt_Controller|string $dataMapper
   * @return Core_Controller_Page_Interface Prov� interface flu�da
   * @throws Core_Controller_Page_Exception|CoreExt_Exception_InvalidArgumentException
   */
  public function setDataMapper($dataMapper)
  {
    if (is_string($dataMapper)) {
      if (class_exists($dataMapper)) {
        $this->_dataMapper = new $dataMapper();
      }
      else {
        throw new Core_Controller_Page_Exception('A classe "'. $dataMapper .'" n�o existe.');
      }
    }
    elseif ($dataMapper instanceof CoreExt_DataMapper) {
      $this->_dataMapper = $dataMapper;
    }
    else {
      throw new CoreExt_Exception_InvalidArgumentException('Argumento inv�lido. S�o aceitos apenas argumentos do tipo string e CoreExt_DataMapper');
    }
    return $this;
  }

  /**
   * Getter.
   *
   * Facilita a subclassifica��o ao permitir heran�a tanto via configura��o do
   * atributo $_dataMapper ou da sobrescri��o de setDataMapper().
   *
   * @see Core_Controller_Page_Interface#getDataMapper()
   */
  public function getDataMapper()
  {
    if (is_string($this->_dataMapper)) {
      $this->setDataMapper($this->_dataMapper);
    }
    elseif (is_null($this->_dataMapper)) {
      throw new Core_Controller_Page_Exception('� necess�rio especificar um nome de classe para a propriedade "$_dataMapper" ou sobrescrever o m�todo "getDataMapper()".');
    }
    return $this->_dataMapper;
  }

  /**
   * Setter.
   * @param CoreExt_Entity $entity
   * @return CoreExt_Controller_Page_Abstract Prov� interface flu�da
   */
  public function setEntity(CoreExt_Entity $entity)
  {
    $this->_entity = $entity;
    return $this;
  }

  /**
   * Getter.
   *
   * Se nenhuma inst�ncia CoreExt_Entity existir, tenta instanciar uma atrav�s
   * de CoreExt_DataMapper.
   *
   * @return CoreExt_Entity|NULL
   */
  public function getEntity()
  {
    if (is_null($this->_entity)) {
      $this->setEntity($this->getDataMapper()->createNewEntityInstance());
    }
    return $this->_entity;
  }

  /**
   * @see CoreExt_Entity#hasError($key)
   */
  public function hasError($key)
  {
    return $this->getEntity()->hasError($key);
  }

  /**
   * @see CoreExt_Entity#hasErrors()
   */
  public function hasErrors()
  {
    return $this->getEntity()->hasErrors();
  }

  /**
   * @see CoreExt_Entity#getError($key)
   */
  public function getError($key)
  {
    return $this->getEntity()->getError($key);
  }

  /**
   * @see CoreExt_Entity#getErrors()
   */
  public function getErrors()
  {
    return $this->getEntity()->getErrors();
  }

  /**
   * Setter.
   * @param int $processoAp
   * @return Core_Controller_Page_Abstract
   */
  public function setBaseProcessoAp($processoAp)
  {
    $this->_processoAp = (int) $processoAp;
    return $this;
  }

  /**
   * Getter.
   *
   * Facilita a subclassifica��o ao permitir heran�a tanto via configura��o do
   * atributo $_processoAp ou da sobrescri��o de setBaseProcessoAp().
   *
   * @return int
   * @see Core_Controller_Page_Interface#getBaseProcessoAp()
   */
  public function getBaseProcessoAp()
  {
    if (is_null($this->_processoAp)) {
      throw new Core_Controller_Page_Exception('� necess�rio especificar um valor num�rico para a propriedade "$_processoAp" ou sobrescrever o m�todo "getBaseProcessoAp()".');
    }
    return $this->_processoAp;
  }

  /**
   * Setter.
   * @see Core_Controller_Page_Interface#setBaseTitulo($titulo)
   */
  public function setBaseTitulo($titulo)
  {
    $this->_titulo = (string) $titulo;
    return $this;
  }

  /**
   * Getter.
   *
   * Facilita a subclassifica��o ao permitir heran�a tanto via configura��o do
   * atributo $_titulo ou da sobrescri��o de setBaseTitulo().
   *
   * @return string
   * @see Core_Controller_Page_Interface#getBaseTitulo()
   */
  public function getBaseTitulo()
  {
    if (is_null($this->_titulo)) {
      throw new Core_Controller_Page_Exception('� necess�rio especificar uma string para a propriedade "$_titulo" ou sobrescrever o m�todo "getBaseTitulo()".');
    }
    return $this->_titulo;
  }

  /**
   * Adiciona uma entrada nos arrays de bot�es (renderizado por RenderHTML(),
   * nas classes cls(Cadastro|Detalhe|Listagem)).
   *
   * @param string $label
   * @param string $url
   * @return Core_Controller_Page_Abstract Prov� interface flu�da
   */
  public function addBotao($label, $url)
  {
    $this->array_botao[]     = $label;
    $this->array_botao_url[] = $url;
    return $this;
  }

  /**
   * Configura bot�es padr�o de clsCadastro
   * @return Core_Controller_Page_Abstract Prov� interface flu�da
   */
  public function configurarBotoes()
  {
    // Bot�o Cancelar (clsDetalhe e clsCadastro)
    if ($this->_hasOption('url_cancelar')) {
      $config = $this->getOption('url_cancelar');
      if (is_string($config)) {
        $this->url_cancelar = $config;
      }
      elseif (is_array($config)) {
        $this->url_cancelar = CoreExt_View_Helper_UrlHelper::url(
          $config['path'], $config['options']
        );
      }
    }
    return $this;
  }

  /**
   * Hook de pr�-execu��o do m�todo RenderHTML().
   * @return Core_Controller_Page_Abstract Prov� interface flu�da
   */
  protected function _preRender()
  {
    return $this->configurarBotoes();
  }

  /**
   * Adiciona conte�do HTML ap�s o conte�do gerado por um
   * Core_Controller_Page_Abstract.
   *
   * @param string $data A string HTML a ser adiciona ap�s o conte�do.
   * @return Core_Controller_Page_Abstract Prov� interface flu�da
   */
  public function appendOutput($data)
  {
    if (!empty($data) && is_string($data)) {
      $this->_output['append'][] = $data;
    }
    return $this;
  }

  /**
   * Retorna todo o conte�do acrescentado como uma string.
   * @return string O conte�do a ser acrescentado separado com uma quebra de linha.
   * @see clsBase#MakeBody()
   */
  public function getAppendedOutput()
  {
    return $this->_getOutput('append');
  }

  /**
   * Adiciona conte�do HTML antes do conte�do HTML gerado por um
   * Core_Controller_Page_Abstract.
   *
   * @param string $data A string HTML a ser adiciona ap�s o conte�do.
   * @return Core_Controller_Page_Abstract Prov� interface flu�da
   */
  public function prependOutput($data)
  {
    if (!empty($data) && is_string($data)) {
      $this->_output['prepend'][] = $data;
    }
    return $this;
  }

  /**
   * Retorna todo o conte�do prefixado como uma string.
   * @return string O conte�do a ser prefixado separado com uma quebra de linha.
   * @see clsBase#MakeBody()
   */
  public function getPrependedOutput()
  {
    return $this->_getOutput('prepend');
  }

  /**
   * Retorna o conte�do a ser adicionado a sa�da de acordo com a regi�o.
   * @param string $pos Regi�o para retornar o conte�do a ser adicionado na sa�da.
   * @return string|NULL Conte�do da regi�o separado por uma quebra de linha ou
   *   NULL caso a regi�o n�o exista.
   */
  private function _getOutput($pos = 'prepend')
  {
    if (isset($this->_output[$pos])) {
      return implode(PHP_EOL, $this->_output[$pos]);
    }
    return NULL;
  }

  /**
   * @see CoreExt_Controller_Interface#dispatch()
   */
  public function dispatch()
  {
    return $this;
  }

  /**
   * @see Core_Controller_Page_Interface#generate($instance)
   */
  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    require_once 'Core/View.php';
    Core_View::generate($instance);
  }
}