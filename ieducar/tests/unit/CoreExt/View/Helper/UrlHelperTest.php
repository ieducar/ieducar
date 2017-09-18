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
 * @package     CoreExt_View
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * CoreExt_View_UrlHelperTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_View
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_View_UrlHelperTest extends UnitBaseTest
{
  protected function setUp()
  {
    CoreExt_View_Helper_UrlHelper::setBaseUrl('');
  }

  public function testCriaUrlRelativa()
  {
    $expected = 'index.php';
    $this->assertEquals($expected, CoreExt_View_Helper_UrlHelper::url('index.php'));
  }

  public function testCriaUrlRelativaComQuerystring()
  {
    $expected = 'index.php?param1=value1';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array('query' => array('param1' => 'value1'))
      )
    );
  }

  public function testCriaUrlRelativaComFragmento()
  {
    $expected = 'index.php#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array('fragment' => 'fragment')
      )
    );
  }

  public function testCriaUrlRelativaComQuerystringEFragmento()
  {
    $expected = 'index.php?param1=value1#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array(
          'query' => array('param1' => 'value1'),
          'fragment' => 'fragment'
        )
      )
    );
  }

  public function testCriaUrlAbsolutaComHostnameConfigurado()
  {
    CoreExt_View_Helper_UrlHelper::setBaseUrl('localhost');
    $expected = 'http://localhost/index.php?param1=value1#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'index.php', array(
          'query' => array('param1' => 'value1'),
          'fragment' => 'fragment',
          'absolute' => TRUE
        )
      )
    );
  }

  public function testCriaUrlAbsolutaComHostnameImplicito()
  {
    $expected = 'http://localhost/index.php?param1=value1#fragment';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'http://localhost/index.php', array(
          'query' => array('param1' => 'value1'),
          'fragment' => 'fragment',
        )
      )
    );
  }

  public function testUrlRetornaApenasSchemeEHost()
  {
    $expected = 'http://www.example.com';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'http://www.example.com/controller/name',
        array(
          'absolute' => TRUE,
          'components' => CoreExt_View_Helper_UrlHelper::URL_SCHEME +
            CoreExt_View_Helper_UrlHelper::URL_HOST
        )
      )
    );
  }

  public function testUrlRetornaComPath()
  {
    $expected = 'http://www.example.com/controller';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::url(
        'http://www.example.com/controller',
        array(
          'absolute' => TRUE,
          'components' => CoreExt_View_Helper_UrlHelper::URL_PATH
        )
      )
    );
  }

  public function testCriaLinkComUrlRelativa()
  {
    $expected = '<a href="index.php?param1=value1">Index</a>';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::l(
        'Index',
        'index.php',
        array('query' => array('param1' => 'value1'))
      )
    );
  }

  public function testCriaLinkComUrlAbsolutaImplicita()
  {
    $expected = '<a href="http://localhost/index.php?param1=value1">Index</a>';
    $this->assertEquals(
      $expected,
      CoreExt_View_Helper_UrlHelper::l(
        'Index',
        'http://localhost/index.php',
        array('query' => array('param1' => 'value1'))
      )
    );
  }
}