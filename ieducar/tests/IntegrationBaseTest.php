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
 * @package   IntegrationTests
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'CoreExt/DataMapper.php';

/**
 * IntegrationBaseTest abstract class.
 *
 * Cria um ambiente de testes de integra��o com um banco de dados sqlite em
 * mem�ria. �til para os testes dos novos componentes de dom�nio.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   IntegrationTests
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
abstract class IntegrationBaseTest extends PHPUnit_Extensions_Database_TestCase
{
  /**
   * Objeto de conex�o com o banco de dados que ser� utilizado tanto pelas
   * classes da aplica��o quanto pelos testes de integra��o.
   *
   * @var CustomPdo
   */
  protected $db = NULL;

  /**
   * Construtor.
   */
  public function __construct()
  {
    $this->db = new CustomPdo('sqlite::memory:');
  }

  /**
   * Usa o setUp() para configurar a todas as inst�ncias de CoreExt_DataMapper
   * que usem o adapter de banco dessa classe.
   */
  protected function setUp()
  {
    parent::setUp();
    CoreExt_DataMapper::setDefaultDbAdapter($this->getDbAdapter());
  }

  /**
   * Getter.
   * @return CustomPdo
   */
  protected function getDbAdapter()
  {
    return $this->db;
  }

  /**
   * Retorna a conex�o usada pelos testes de integra��o do DbUnit. Note que
   * a conex�o � criada com o objeto PDO encapsulado em CustomPdo.
   *
   * @return PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
   */
  protected function getConnection()
  {
    return $this->createDefaultDBConnection($this->db->getPdo(), 'testdb');
  }

  /**
   * Retorna o caminho absoluto para um arquivo fixture em unit/CoreExt/.
   *
   * @param string $filename
   * @return string
   */
  public function getFixture($filename)
  {
    $path = dirname(__FILE__);
    return $path . '/unit/CoreExt/_fixtures/' . $filename;
  }

/**
   * Retorna o caminho absoluto para um arquivo fixture dentro do diret�rio
   * _tests de um m�dulo.
   *
   * @param  string $filename
   * @return string
   */
  public function getFixtureForModule($filename, $module)
  {
    $path = PROJECT_ROOT . DS . 'modules' . DS . $module . DS . '_tests';
    return $path . DS . $filename;
  }
}