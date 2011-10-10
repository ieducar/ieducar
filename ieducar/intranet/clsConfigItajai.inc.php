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
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/constants.inc.php';

/**
 * clsConfig class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsConfig
{

  /**
   * Tempo em segundos para relatar uma query SQL.
   */
  const CLS_CONFIG_SQL_TEMPO    = 3;

  /**
   * Tempo em segundos para relatar um carregamento de p�gina.
   */
  const CLS_CONFIG_PAGINA_TEMPO = 5;

  /**
   * Array com os par�metros de configura��o
   */
  public $arrayConfig = array();

  /**
   * Switch para habilitar depura��o.
   * @var  int
   */
  public $depurar = 0;


  /**
   * Construtor.
   *
   * Atribui os valores padr�es das diretivas de configura��o.
   */
  public function __construct() {
    $this->setArrayConfig();
  }

  /**
   * Configura o array $arrayConfig com as diretivas passadas pelo ieducar.ini.
   */
  private function setArrayConfig()
  {
    global $coreExt;
    $config = $coreExt['Config'];

    $config = $coreExt['Config']->app;

    // Nome da institui��o
    $this->_instituicao = $config->template->vars->instituicao . ' - ';

    // E-mails dos administradores para envio de relat�rios de performance
    $emails = $config->admin->reports->emails->toArray();
    $this->arrayConfig['ArrStrEmailsAdministradores'] = $emails;

    // Diret�rio dos templates de e-mail
    $this->arrayConfig['strDirTemplates'] = "templates/";

    // Quantidade de segundos para relatar uma query SQL
    $segundosSQL = $config->get($config->admin->reports->sql_tempo,
      self::CLS_CONFIG_SQL_TEMPO);
    $segundosSQL = $segundosSQL > 0 ?
      $segundosSQL : self::CLS_CONFIG_SQL_TEMPO;

    $this->arrayConfig['intSegundosQuerySQL'] = $segundosSQL;

    // Quantidade de segundos para relatar o tempo de carregamento de p�gina
    $segundosPagina = $config->get($config->admin->reports->pagina_tempo,
      self::CLS_CONFIG_PAGINA_TEMPO);
    $segundosPagina = $segundosPagina > 0 ?
      $segundosPagina : self::CLS_CONFIG_PAGINA_TEMPO;

    $this->arrayConfig['intSegundosProcessaPagina'] = $segundosPagina;
  }


  /**
   * Reliza um var_dump da vari�vel passada.
   * @see  http://php.net/var_dump
   * @param  mixed  $msg
   */
  protected function Depurar($msg)
  {
    if ($this->depurar)
    {
      if ($this->depurar == 1)
        echo "\n\n<!--";

      echo "<pre>";

      if (is_array($msg))
        var_dump($msg);
      else
        echo $msg;

      echo "</pre>";

      if ($this->depurar == 1)
        echo "-->\n\n";
    }
  }
}