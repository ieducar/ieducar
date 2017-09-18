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
 * @package     CoreExt
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Config/Ini.class.php';

/**
 * CoreExt_Config_IniTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Config_IniTest extends UnitBaseTest
{
  public function testParsedIni()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar.ini');
    $this->assertNotNull($ini->app);
  }

  public function testChangeEnviroment()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar.ini');
    $this->assertEquals(FALSE, (bool) $ini->php->display_errors);

    $ini->changeEnviroment('development');
    $this->assertEquals(TRUE, (bool) $ini->php->display_errors);

    $ini->changeEnviroment('testing');
    $this->assertEquals(TRUE, (bool) $ini->php->display_errors);

    $ini->changeEnviroment();
    $this->assertEquals(FALSE, (bool) $ini->php->display_errors);
  }

  /**
   * @expectedException Exception
   */
  public function testInvalidIniFile()
  {
    // Tentando carregar configura��o do blackhole!
    $ini = new CoreExt_Config_Ini('/dev/null');
  }

  /**
   * @expectedException Exception
   */
  public function testSectionExtendsMoreThanOne()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar-extends-broken.ini');
  }

  /**
   * @expectedException Exception
   */
  public function testIniSyntaxError()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar-syntax-broken.ini');
  }

  /**
   * @expectedException Exception
   */
  public function testSectionInheritanceNotExist()
  {
    $ini = new CoreExt_Config_Ini('../tests/fixtures/configuration/ieducar-inheritance-broken.ini');
  }
}