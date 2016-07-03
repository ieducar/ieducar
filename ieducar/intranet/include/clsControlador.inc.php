<?php

/*
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
 */

require_once 'include/clsBanco.inc.php';
require_once 'Portabilis/Messenger.php';
require_once 'Portabilis/Mailer.php';
require_once 'Portabilis/Utils/User.php';
require_once 'Portabilis/Utils/ReCaptcha.php';

/**
 * clsControlador class.
 *
 * @author   Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Classe dispon�vel desde a vers�o 1.0.0
 * @version  $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/intranet/include/clsControlador.inc.php 662 2009-11-17T18:28:48.404882Z eriksen  $
 */
class clsControlador
{

  /**
   * @var boolean
   */
  public $logado;

  /**
   * @var string
   */
  public $erroMsg;


  /**
   * Construtor.
   */
  public function clsControlador()
  {

    /*
      Desabilitado esta linha para usar o valor setado no php.ini > session.cookie_lifetime
      @session_set_cookie_params(200);
    */

    @session_start();

    if ('logado' == $_SESSION['itj_controle']) {
      $this->logado = TRUE;
    }
    else {
      $this->logado = FALSE;
    }

    // Controle dos menus
    if (isset($_GET['mudamenu']) && isset($_GET['categoria']) && isset($_GET['acao']))
    {
      if ($_GET['acao']) {
        $_SESSION['menu_opt'][$_GET['categoria']] = 1;
        $_SESSION['menu_atual'] = $_GET['categoria'];
      }
      else {
        // Est� apagando vari�vel session com o �ndice dado por $_GET
        unset($_SESSION['menu_opt'][$_GET['categoria']]);
        if ($_SESSION['menu_atual'] == $_GET['categoria']) {
          unset($_SESSION['menu_atual']);
        }
      }

      $db = new clsBanco();
      if (isset($_SESSION['id_pessoa'])) {
        $db->Consulta("UPDATE funcionario SET opcao_menu = '" . serialize( $_SESSION['menu_opt'] ) . "' WHERE ref_cod_pessoa_fj = '" . $_SESSION['id_pessoa'] . "'");
      }
    }

    session_write_close();

    $this->_maximoTentativasFalhas = 7;
    $this->messenger = new Portabilis_Messenger();
  }


  /**
   * Retorna TRUE para usu�rio logado
   * @return  boolean
   */
  public function Logado()
  {
    return $this->logado;
  }


  /**
   * Executa o login do usu�rio.
   */
  public function obriga_Login()
  {
    if (! $this->logado)
      $validateUserCredentials = false;

    elseif ($_POST['login'] && $_POST['senha'])
      $validateUserCredentials = true;

    $this->logar($validateUserCredentials);
  }


  // novo metodo login, logica quebrada em metodos menores
  public function Logar($validateUserCredentials) {
    if ($validateUserCredentials) {
      $user = $this->validateUserCredentials($username = @$_POST['login'], $password = md5(@$_POST['senha']));

      if ($this->canStartLoginSession($user)) {
        $this->startLoginSession($user);
        return null;
      }
    }

    $this->renderLoginPage();
  }


  // valida se o usu�rio e senha informados, existem no banco de dados.
  protected function validateUserCredentials($username, $password) {
    if (! $this->validateHumanAccess()) {
      $msg = "Voc� errou a senha muitas vezes, por favor, preencha o campo de " .
             "confirma��o visual ou <a class='light decorated' href='/module/Usuario/Rede" .
             "finirSenha'>redefina sua senha</a>.";
      $this->messenger->append($msg, "error", false, "error");
    }

    else {
      $user = Portabilis_Utils_User::loadUsingCredentials($username, $password);

      if (is_null($user)) {
        $this->messenger->append("Usu�rio ou senha incorreta.", "error");
        $this->incrementTentativasLogin();
      }
      else {
        $this->unsetTentativasLogin();
        return $user;
      }
    }

    return false;
  }


  public function startLoginSession($user, $redirectTo = '') {
    // unsetting login attempts here, because when the password is recovered the login attempts should be reseted.
    $this->unsetTentativasLogin();

    @session_start();
    $_SESSION                 = array();
    $_SESSION['itj_controle'] = 'logado';
    $_SESSION['id_pessoa']    = $user['id'];
    $_SESSION['pessoa_setor'] = $user['ref_cod_setor_new'];
    $_SESSION['menu_opt']     = unserialize($user['opcao_menu']);
    $_SESSION['tipo_menu']    = $user['tipo_menu'];
    @session_write_close();

    Portabilis_Utils_User::logAccessFor($user['id'], $this->getClientIP());
    Portabilis_Utils_User::destroyStatusTokenFor($user['id'], 'redefinir_senha');

    $this->logado = true;
    $this->messenger->append("Usu�rio logado com sucesso.", "success");

    // solicita email para recupera��o de senha, caso usu�rio ainda n�o tenha informado.
    if (! filter_var($user['email'], FILTER_VALIDATE_EMAIL))
      header("Location: /module/Usuario/AlterarEmail");

    elseif($user['expired_password'])
      header("Location: /module/Usuario/AlterarSenha");

    elseif(! empty($redirectTo))
      header("Location: $redirectTo");
  }


  public function canStartLoginSession($user) {
    if (! $this->messenger->hasMsgWithType("error")) {
      $this->checkForDisabledAccount($user);
      $this->checkForBannedAccount($user);
      $this->checkForExpiredAccount($user);
      $this->checkForMultipleAccess($user);
      // #TODO verificar se conta nunca usada (exibir "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intrenet'." ?)
    }

    return ! $this->messenger->hasMsgWithType("error");
  }


  // renderiza o template de login, com as mensagens adicionadas durante valida��es
  protected function renderLoginPage() {
    $this->destroyLoginSession();

    // Nome padr�o da template
    $templateName = 'templates/nvp_htmlloginintranet.tpl';
    // Caso esteja definido no arquivo de configura��o ...
    if ($GLOBALS['coreExt']['Config']->app->template->loginpage) {
    	// ... e o arquivo existir e conseguirmos ler ...
    	if (file_exists($GLOBALS['coreExt']['Config']->app->template->loginpage) &&
    		is_readable($GLOBALS['coreExt']['Config']->app->template->loginpage)) {
    			// ... o substitu�mos.
    			$templateName = $GLOBALS['coreExt']['Config']->app->template->loginpage;
    		}
    }
    
    $templateFile = fopen($templateName, "r");
    $templateText = fread($templateFile, filesize($templateName));
    $templateText = str_replace( "<!-- #&ERROLOGIN&# -->", $this->messenger->toHtml('p'), $templateText);

    $requiresHumanAccessValidation = $GLOBALS['coreExt']['Config']->app->recaptcha->enabled
                                     && isset($_SESSION['tentativas_login_falhas']) 
    								 && is_numeric($_SESSION['tentativas_login_falhas']) 
                                     && $_SESSION['tentativas_login_falhas'] >= $this->_maximoTentativasFalhas;

    if ($requiresHumanAccessValidation)
      $templateText = str_replace( "<!-- #&RECAPTCHA&# -->", Portabilis_Utils_ReCaptcha::getWidget(), $templateText);

    fclose($templateFile);
    die($templateText);
  }


  protected function destroyLoginSession($addMsg = false) {
    $tentativasLoginFalhas = $_SESSION['tentativas_login_falhas'];

    @session_start();
    $_SESSION = array();
    @session_destroy();

    //mantem tentativas_login_falhas, at� que senha senha informada corretamente
    @session_start();
    $_SESSION['tentativas_login_falhas'] = $tentativasLoginFalhas;
    @session_write_close();

    if ($addMsg)
      $this->messenger->append("Usu�rio deslogado com sucesso.", "success");
  }


  protected function getClientIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
      // pega o (ultimo) IP real caso o host esteja atr�s de um proxy
      $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      $ip = trim(array_pop($ip));
    }
    else
      $ip = $_SERVER['REMOTE_ADDR'];

    return $ip;
  }


  protected function validateHumanAccess() {
    $result = false;
    
    if ( !($GLOBALS['coreExt']['Config']->app->recaptcha->enabled) ) {
    	$result = true;
    } 
    elseif (! $this->atingiuTentativasLogin()) {
      $result = true;  
    } 
    elseif (Portabilis_Utils_ReCaptcha::getWidget()->validate()) {
      $this->unsetTentativasLogin();
      $result = true;
    }

    return $result;
  }


  protected function atingiuTentativasLogin() {
    return isset($_SESSION['tentativas_login_falhas']) &&
                 is_numeric($_SESSION['tentativas_login_falhas']) &&
                 $_SESSION['tentativas_login_falhas'] >= $this->_maximoTentativasFalhas;
  }


  protected function incrementTentativasLogin() {
    @session_start();
    if (! isset($_SESSION['tentativas_login_falhas']) or ! is_numeric($_SESSION['tentativas_login_falhas']))
      $_SESSION['tentativas_login_falhas'] = 1;
    else
      $_SESSION['tentativas_login_falhas'] += 1;
    @session_write_close();
  }


  protected function unsetTentativasLogin() {
    @session_start();
    unset($_SESSION['tentativas_login_falhas']);
    @session_write_close();
  }


  protected function checkForDisabledAccount($user) {
    if ($user['ativo'] != '1') {
      $this->messenger->append("Sua conta de usu�rio foi desativada ou expirou, por favor, " .
                              "entre em contato com o respons�vel pelo sistema do seu munic�pio.", "error", false, "error");
    }
  }


  protected function checkForBannedAccount($user) {
    if ($user['proibido'] != '0') {
      $this->messenger->append("Sua conta de usu�rio n�o pode mais acessar o sistema, " .
                              "por favor, entre em contato com o respons�vel pelo sistema do seu munic�pio.",
                              "error", false, "error");
    }
  }


  protected function checkForExpiredAccount($user) {
    if($user['expired_account']) {

      if ($user['ativo'] == 1)
        Portabilis_Utils_User::disableAccount($user['id']);

      $this->messenger->append("Sua conta de usu�rio expirou, por favor, " .
                              "entre em contato com o respons�vel pelo sistema do seu munic�pio.", "error", false, "error");
    }
  }


  protected function checkForMultipleAccess($user) {
    // considera como acesso multiplo, acesso em diferentes IPs em menos de $tempoMultiploAcesso minutos
    $tempoMultiploAcesso = 10;
    $tempoEmEspera       = abs(time() - strftime("now") - strtotime($user['data_login'])) / 60;

    $multiploAcesso = $tempoEmEspera <= $tempoMultiploAcesso &&
                      $user['ip_ultimo_acesso'] != $this->getClientIP();

    if ($multiploAcesso and $user['super']) {

      // #TODO mover l�gica email, para mailer especifico

      $subject = "Conta do super usu�rio {$_SERVER['HTTP_HOST']} acessada em mais de um local";

      $message = ("Aparentemente a conta do super usu�rio {$user['matricula']} foi acessada em " .
                  "outro computador nos �ltimos $tempoMultiploAcesso " .
                  "minutos, caso n�o tenha sido voc�, por favor, altere sua senha.\n\n" .
                  "Endere�o IP �ltimo acesso: {$user['ip_ultimo_acesso']}\n".
                  "Endere�o IP acesso atual: {$this->getClientIP()}");

      $mailer = new Portabilis_Mailer();
      $mailer->sendMail($user['email'], $subject, $message);
    }
    elseif ($multiploAcesso) {
      $minutosEmEspera = round($tempoMultiploAcesso - $tempoEmEspera) + 1;
      $this->messenger->append("Aparentemente sua conta foi acessada em outro computador nos �ltimos " .
                              "$tempoMultiploAcesso minutos, caso n�o tenha sido voc�, " .
                              "por favor, altere sua senha ou tente novamente em $minutosEmEspera minutos",
                              "error", false, "error");
    }
  }

}
