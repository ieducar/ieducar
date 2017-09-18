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
 * @package     Core
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.0.1
 * @version     $Id$
 */

require_once 'include/pmieducar/clsPmieducarClienteSuspensao.inc.php';

/**
 * clsBancoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Core
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.0.1
 * @todo        Subclassificar classe como IntegrationBaseTest
 * @version     @@package_version@@
 */
class ClsBancoTest extends UnitBaseTest
{
  public function testDoCountFromObj()
  {
    $db = new clsBanco();
    $db->Conecta();

    $obj = new clsPmieducarClienteSuspensao();
    $this->assertNotEquals(TRUE, is_null($db->doCountFromObj($obj)));
  }

  public function testConexao()
  {
    $db = new clsBanco();
    $db->Conecta();

    $this->assertTrue((bool) $db->bLink_ID);
  }

  public function testFormatacaoDeValoresBooleanos()
  {
    $data = array(
      'id' => 1,
      'hasChild' => TRUE
    );

    $db = new clsBanco();
    $formatted = $db->formatValues($data);
    $this->assertSame('t', $formatted['hasChild']);

    $data['hasChild'] = FALSE;
    $formatted = $db->formatValues($data);
    $this->assertSame('f', $formatted['hasChild']);
  }

  public function testOpcaoDeLancamentoDeExcecaoEFalsePorPadrao()
  {
    $db = new clsBanco();
    $this->assertFalse($db->getThrowException());
  }

  public function testConfiguracaoDeOpcaoDeLancamentoDeExcecao()
  {
    $db = new clsBanco();
    $db->setThrowException(TRUE);
    $this->assertTrue($db->getThrowException());
  }

  public function testFetchTipoArrayDeResultadosDeUmaQuery()
  {
    $db = new clsBanco();

    $db->Consulta("SELECT spcname, spcowner, spclocation, spcacl FROM pg_tablespace");
    $row = $db->ProximoRegistro();
    $row = $db->Tupla();
    $this->assertNotNull($row[0]);
    $this->assertNotNull($row['spcname']);
  }

  public function testFetchTipoAssocDeResultadosDeUmaQuery()
  {
    $db = new clsBanco(array('fetchMode' => clsBanco::FETCH_ASSOC));

    $db->Consulta("SELECT spcname, spcowner, spclocation, spcacl FROM pg_tablespace");
    $row = $db->ProximoRegistro();
    $row = $db->Tupla();
    $this->assertFalse(array_key_exists(0, $row));
    $this->assertNotNull($row['spcname']);
  }
}