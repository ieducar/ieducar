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
 * @package   Tests
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Runner/IncludePathTestCollector.php';

/**
 * TestCollector abstract class.
 *
 * Classe abstrata que prov� um ponto de extens�o para classes de defini��o de
 * su�te de testes do PHPUnit (veja {@link Orga})
 *
 * Ao estender corretamente essa classe, todos as classes de teste do diret�rio
 * da classe de defini��o de su�te de testes ser�o adicionados � su�te,
 * tornando desnecess�rio a nessidade de usar os construtores de linguagem
 * require e include para incluir esses arquivos.
 *
 * Para estender essa classe, basta informar o caminho para o arquivo da classe
 * de defini��o da su�te na vari�vel protegida $_file, exemplo:
 *
 * <code>
 * class App_Model_AllTests extends TestCollector
 * {
 *   protected $_file = __FILE__;
 * }
 * </code>
 *
 * Isso � o suficiente para conseguir coletar todos os arquivos do diret�rio.
 * Para criar uma su�te de testes com todas as classes de teste do diret�rio,
 * basta criar uma inst�ncia da classe e chamar o m�todo addDirectoryTests():
 *
 * <code>
 * public static function suite()
 * {
 *   $instance = new self();
 *   return $instance->createTestSuite('App_Model: testes unit�rios')
 *                   ->addDirectoryTests();
 * }
 * </code>
 *
 * Se a vari�vel de inst�ncia $_name estiver sobrescrita, ela ser� utilizada
 * por padr�o caso o m�todo createTestSuite() seja chamado sem o par�metro nome.
 * Dessa forma, basta chamar o m�todo addDirectoryTests():
 *
 * <code>
 * protected $_name = 'App_model: testes unit�rios';
 *
 * public static function suite()
 * {
 *   $instance = new self();
 *   return $instance->addDirectoryTests();
 * }
 * </code>
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Tests
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
abstract class TestCollector
{
  /**
   * Caminho completo do arquivo da classe que estende TestCollector.
   * @var string
   */
  protected $_file = NULL;

  /**
   * Diret�rio onde residem os arquivos com as classes de teste.
   * @var array
   */
  protected $_directory = array();

  /**
   * Nome da su�te de testes.
   * @var string
   */
  protected $_name  = '';

  /**
   * Uma inst�ncia de PHPUnit_Framework_TestSuite.
   * @var PHPUnit_Framework_TestSuite
   */
  protected $_suite = NULL;

  /**
   * Construtor.
   * @return TestCollector
   */
  public function __construct()
  {
    $this->_defineCurrentDirectory();
  }

  /**
   * Cria um objeto PHPUnit_Framework_TestSuite com o nome passado como
   * argumento ou usando a vari�vel de inst�ncia $_name.
   *
   * @param   string  $name  O nome para a su�te de testes
   * @return  TestCollector  Interface flu�da
   * @throws  InvalidArgumentException
   */
  public function createTestSuite($name = '')
  {
    if ((trim((string) $name)) == '' && $this->_name == '') {
      throw new InvalidArgumentException('A classe concreta deve sobrescrever a '
                . 'vari�vel "$_name" ou passar um nome v�lido ao chamar o m�todo'
                . 'createTestSuite().');
    }
    if (trim((string) $name) != '') {
      $name = $this->_name;
    }

    $this->_suite = new PHPUnit_Framework_TestSuite($name);
    return $this;
  }

  /**
   * Adiciona os testes do diret�rio da classe de defini��o de su�te.
   *
   * @param   PHPUnit_Framework_TestSuite  $suite
   * @return  PHPUnit_Framework_TestSuite
   */
  public function addDirectoryTests(PHPUnit_Framework_TestSuite $suite = NULL)
  {
    // Se n�o existir um objeto PHPUnit_Framework_TestSuite, cria um com o nome
    // do arquivo da classe de defini��o da su�te
    if ($this->_suite == NULL && $suite == NULL) {
      $this->createTestSuite();
    }
    if ($suite == NULL) {
      $suite = $this->_suite;
    }

    $suite->addTestFiles($this->_collectTests());
    return $suite;
  }

  /**
   * Retorna um PHPUnit_Util_FilterIterator que cont�m as regras de inclus�o
   * de testes do diret�rio definido por $_fir.
   *
   * @return PHPUnit_Util_FilterIterator
   */
  protected function _collectTests()
  {
    $testCollector = new PHPUnit_Runner_IncludePathTestCollector($this->_directory);
    return $testCollector->collectTests();
  }

  /**
   * Define o diret�rio atual da classe que estende TestCollector. O diret�rio �
   * definido pela vari�vel de inst�ncia $_file.
   *
   * @throws  Exception  Lan�a exce��o
   * @todo    Refatorar o c�digo para utilizar {@link http://php.net/lsb Late static binding}
   *          quando a vers�o do PHP for a 5.3.
   */
  protected function _defineCurrentDirectory()
  {
    if ($this->_file === NULL) {
      throw new Exception('A classe concreta deve sobrescrever a vari�vel "$_file".');
    }
    $directory = $this->_getDirectoryPath($this->_file);
    if (!array_search($directory, $this->_directory)) {
      $this->_directory[] = $directory;
    }
  }

  /**
   * Pega o caminho do diret�rio que ser� varrido para a inclus�o de testes.
   * @param  string $path
   * @return string
   */
  protected function _getDirectoryPath($path)
  {
    $directory = realpath(dirname($path));
    if (!is_dir($directory)) {
      throw new Exception('The path "'. $directory .'" is not a valid directory');
    }
    return $directory;
  }

  public function addDirectory($directory)
  {
    $this->_directory[] = $this->_getDirectoryPath($directory);
  }
}