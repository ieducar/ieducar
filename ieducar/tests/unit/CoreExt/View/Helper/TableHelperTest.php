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

require_once 'CoreExt/View/Helper/TableHelper.php';

/**
 * CoreExt_View_TableHelperTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_View
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_View_TableHelperTest extends UnitBaseTest
{
  protected $_headerExpected = '
<thead>
  <tr>
    <td>Title</td>
    <td colspan="2">Item A</td>
    <td colspan="2">Item B</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Subitem A1</td>
    <td>Subitem A2</td>
    <td>Subitem B1</td>
    <td>Subitem B2</td>
  </tr>
</thead>';

  protected $_bodyExpected = '
<tbody>
  <tr>
    <td>Label 1</td>
    <td>1</td>
    <td>2</td>
    <td>3</td>
    <td>4</td>
  </tr>
  <tr>
    <td>Label 2</td>
    <td>1</td>
    <td>2</td>
    <td>3</td>
    <td>4</td>
  </tr>
</tbody>';

  protected $_footerExpected = '
<tfooter>
  <tr>
    <td colspan="3">&nbsp;</td>
    <td class="tdd" colspan="2">1</td>
  </tr>
</tfooter>';

  protected $_tableExpected = '
<table>
  <tbody>
    <tr>
      <td>Example data</td>
    </tr>
  </tbody>
</table>
';

  protected $_tableRowAttributesExpected = '
<table>
  <tbody>
    <tr class="trr">
      <td>Example data</td>
    </tr>
  </tbody>
</table>
';

  protected function setUp()
  {
  }

  public function testCreateHeader()
  {
    $table = CoreExt_View_Helper_TableHelper::getInstance();

    $cols = array(
      array('data' => 'Title'),
      array('data' => 'Item A', 'colspan' => 2),
      array('data' => 'Item B', 'colspan' => 2)
    );

    $cols2 = array(
      array(),
      array('data' => 'Subitem A1'),
      array('data' => 'Subitem A2'),
      array('data' => 'Subitem B1'),
      array('data' => 'Subitem B2')
    );

    $table->addHeaderRow($cols);
    $table->addHeaderRow($cols2);

    $header = $table->createHeader();
    $this->assertEquals(trim($this->_headerExpected), $header);
  }

  public function testCreateBody()
  {
    $table = CoreExt_View_Helper_TableHelper::getInstance();

    $cols = array(
      array('data' => 'Label 1'),
      array('data' => '1'),
      array('data' => '2'),
      array('data' => '3'),
      array('data' => '4')
    );

    $cols2 = array(
      array('data' => 'Label 2'),
      array('data' => '1'),
      array('data' => '2'),
      array('data' => '3'),
      array('data' => '4')
    );

    $table->addBodyRow($cols);
    $table->addBodyRow($cols2);

    $body = $table->createBody();
    $this->assertEquals(trim($this->_bodyExpected), $body);
  }

  public function testCreateFooter()
  {
    $table = CoreExt_View_Helper_TableHelper::getInstance();

    $cols = array(
      array('colspan' => 3),
      array('data' => '1', 'colspan' => 2, 'attributes' => array('class' => 'tdd')),
    );

    $table->addFooterRow($cols);

    $footer = $table->createFooter();
    $this->assertEquals(trim($this->_footerExpected), $footer);
  }

  public function testCreateTable()
  {
    $table = CoreExt_View_Helper_TableHelper::getInstance();
    $table->addBodyRow(array(array('data' => 'Example data')));
    $table = $table->createTable();
    $this->assertEquals(trim($this->_tableExpected), $table);
  }

  public function testRowAttributes()
  {
    $table = CoreExt_View_Helper_TableHelper::getInstance();
    $table->addBodyRow(array(array('data' => 'Example data')), array('class' => 'trr'));
    $table = $table->createTable();
    $this->assertEquals(trim($this->_tableRowAttributesExpected), $table);
  }
}