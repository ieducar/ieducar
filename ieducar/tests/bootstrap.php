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

/**
 * Arquivo de bootstrap para os testes do projeto. Configura o include_path e
 * inclui alguns arquivos necess�rios nos testes unit�rios e funcionais.
 */

error_reporting(E_ALL ^ E_STRICT);

// Diret�rio raiz do projeto.
$root = realpath(dirname(__FILE__) . '/../');

// Adiciona os diret�rios tests/unit, tests/functional, tests/, ./ e intranet/
// ao include_path.
$paths = array();
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'tests', 'unit'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'tests', 'functional'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'tests'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, '.'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'intranet'));

// Configura o include_path.
set_include_path(join(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());

// Altera as configura��es de session para os testes. N�o usa cookies para
// permitir o teste de CoreExt_Session, j� que o test runner do PHPUnit faz
// sa�da de buffer.
ini_set('session.use_cookies', 0);

// Arquivos em intranet/ usam includes com caminho relativo, muda diret�rio
// atual para evitar warnings e errors.
chdir($root . '/intranet');
unset($root, $paths);

require_once 'include/clsBanco.inc.php';
require_once 'CustomPdo.php';
require_once 'TestCollector.php';
require_once 'UnitBaseTest.class.php';
require_once 'IntegrationBaseTest.php';
require_once 'FunctionalBaseTest.class.php';