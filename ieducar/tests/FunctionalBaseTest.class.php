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
 * @package   FunctionalTests
 * @since     Arquivo dispon�vel desde a vers�o 1.0.1
 * @version   $Id$
 */

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

/**
 * FunctionBaseTest class.
 *
 * Cont�m as configura��es de acesso ao servidor Selenium RC, a conta de usu�rio
 * a ser utilizada no teste e alguns m�todos auxiliares.
 *
 * Abstrai o PHPUnit, diminuindo a depend�ncia de seu uso. Inclui a classe
 * de banco de dados para facilitar no tearDown de dados de teste.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   FunctionalTests
 * @since     Classe dispon�vel desde a vers�o 1.0.1
 * @version   @@package_version@@
 */
abstract class FunctionalBaseTest extends PHPUnit_Extensions_SeleniumTestCase
{
  // Configura��es do Selenium RC.
  static protected
    $slBrowserUrl = 'http://ieducar.local',
    $slBrowser    = '*firefox',
    $slPort       = 4444,
    $slHost       = 'localhost';

  // Conta de usu�rio para testes funcionais.
  protected
    $slUsuarioLogin = 'admin',
    $slUsuarioSenha = 'admin';

  protected function setUp()
  {
    $this->setBrowser(self::$slBrowser);
    $this->setHost(self::$slHost);
    $this->setPort(self::$slPort);
    $this->setBrowserUrl(self::$slBrowserUrl);
  }

  protected function doLogin()
  {
    $this->open('/intranet');
    $this->type('login', $this->slUsuarioLogin);
    $this->type('senha', $this->slUsuarioSenha);
    $this->clickAndWait("//input[@value='Entrar']");
  }

  protected function doLogout()
  {
    $this->click("//img[@alt='Logout']");
  }
}