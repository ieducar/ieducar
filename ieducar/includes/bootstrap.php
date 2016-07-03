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
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

/*
 * Verifica se o PHP instalado � maior ou igual a 5.2.0
 */
if (! version_compare('5.2.0', PHP_VERSION, '<=')) {
  die('O i-Educar requer o PHP na vers�o 5.2. A vers�o instalada de seu PHP (' . PHP_VERSION . ') n�o � suportada.');
}

/**
 * Alias para DIRECTORY_SEPARATOR
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * Diret�rio raiz do projeto.
 */
$root = realpath(dirname(__FILE__) . '/../');
define('PROJECT_ROOT', $root);

/**
 * Diret�rio raiz da aplica��o (intranet/).
 */
define('APP_ROOT', $root . DS . 'intranet');

/*
 * Altera o include_path, adicionando o caminho a CoreExt, tornando mais
 * simples o uso de require e include para as novas classes.
 */
$paths = array();
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'intranet'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'lib'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, 'modules'));
$paths[] = join(DIRECTORY_SEPARATOR, array($root, '.'));

// Configura o include_path.
set_include_path(join(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());

/*
 * Define o ambiente de configura��o desejado. Verifica se existe uma vari�vel
 * de ambiente configurada ou define 'production' como padr�o.
 */
if (getenv('CORE_EXT_CONFIGURATION_ENV')) {
  define('CORE_EXT_CONFIGURATION_ENV', getenv('CORE_EXT_CONFIGURATION_ENV'));
}
else {
  define('CORE_EXT_CONFIGURATION_ENV', 'production');
}

// por padr�o busca uma configura��o para o ambiente atual definido em CORE_EXT_CONFIGURATION_ENV
$configFile = realpath(dirname(__FILE__) . '/../') . '/configuration/' . CORE_EXT_CONFIGURATION_ENV . '.ini';

// caso n�o exista o ini para o ambiente atual, usa o arquivo padr�o ieducar.ini
if (! file_exists($configFile))
  $configFile = realpath(dirname(__FILE__) . '/../') . '/configuration/ieducar.ini';

// Classe de configura��o
require_once 'CoreExt/Config.class.php';
require_once 'CoreExt/Config/Ini.class.php';
require_once 'CoreExt/Locale.php';

// Array global de objetos de classes do pacote CoreExt
global $coreExt;
$coreExt = array();

// Localiza��o para pt_BR
$locale = CoreExt_Locale::getInstance();
$locale->setCulture('pt_BR')->setLocale();

// Instancia objeto CoreExt_Configuration
$coreExt['Config'] = new CoreExt_Config_Ini($configFile, CORE_EXT_CONFIGURATION_ENV);
$coreExt['Locale'] = $locale;

// Timezone
date_default_timezone_set($coreExt['Config']->app->locale->timezone);

$tenantEnv = $_SERVER['HTTP_HOST'];

// tenta carregar as configura��es da se��o especifica do tenant,
// ex: ao acessar http://tenant.ieducar.com.br ser� carregado a se��o tenant.ieducar.com.br caso exista
if ($coreExt['Config']->hasEnviromentSection($tenantEnv))
  $coreExt['Config']->changeEnviroment($tenantEnv);

/**
 * Altera o diret�rio da aplica��o. chamadas a fopen() na aplica��o n�o
 * verificam em que diret�rio est�, assumindo sempre uma requisi��o a
 * intranet/.
 */
chdir($root . DS . 'intranet');
unset($root, $paths);
