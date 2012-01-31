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
require_once 'lib/Portabilis/Message.php';

/**
 * clsControlador class.
 *
 * @author   Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Classe dispon�vel desde a vers�o 1.0.0
 * @version  $Id$
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
    @session_set_cookie_params(1200);
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
    $this->messages = new Message();
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
    if ($_POST['login'] && $_POST['senha']) {
      $this->logar(TRUE);
    }
    if (!$this->logado) {
      $this->logar(FALSE);
    }
  }

  // novo metodo login, logica quebrada em metodos menores
  public function Logar($validateCredentials) {
    if ($validateCredentials) {
      $username = @$_POST['login'];
      $password = md5(@$_POST['senha']);
      $userId = $this->validateUser($username, $password);

      if ($this->canStartLoginSession($userId))
        $this->startLoginSession($userId);
      else {
        $this->renderLoginPage();
      }
    }
    else
      $this->renderLoginPage();
  }

  // renderiza o template de login, com as mensagens adicionadas durante valida��es
  protected function renderLoginPage() {
    $this->destroyLoginSession();

    $templateName = 'templates/nvp_htmlloginintranet.tpl';
    $templateFile  = fopen($templateName, "r");
    $templateText = fread($templateFile, filesize($templateName));
    $templateText = str_replace( "<!-- #&ERROLOGIN&# -->", $this->messages->toHtml('p'), $templateText);

    fclose($templateFile);
    die($templateText);
  }

  // valida se o usu�rio e senha informados, existem no banco de dados.
  protected function validateUser($username, $password) {
    $sql = "SELECT ref_cod_pessoa_fj FROM portal.funcionario WHERE matricula = $1 and senha = $2";
    $userId = $this->fetchPreparedQuery($sql, array($username, $password), true, 'first-field');

    if (! is_numeric($userId))
      $this->messages->append("Usu�rio ou senha incorreta.", "error");
    else
      return $userId;

    return false;
  }


  // valida se o usu�rio, pode acessar o sistema.
  public function canStartLoginSession($userId) {

    if (! $this->messages->hasMsgWithType("error")) {
      $sql = "SELECT ativo, proibido, tempo_expira_conta, data_reativa_conta, ip_logado " .
             "as ip_ultimo_acesso, data_login FROM portal.funcionario WHERE ref_cod_pessoa_fj = $1";

      $user = $this->fetchPreparedQuery($sql, $userId, true, 'first-line');

      if ($user['ativo'] != '1') {
        $this->messages->append("Aparentemente sua conta de usu�rio esta inativa (expirada), por favor, " .
                                "entre em contato com o administrador do sistema.", "error", false, "error");
      }

      if ($user['proibido'] != '0') {
        $this->messages->append("Aparentemente sua conta n�o pode acessar o sistema, " .
                                "por favor, entre em contato com o administrador do sistema.", 
                                "error", false, "error");
      }

      /* considera como expirado caso data_reativa_conta + tempo_expira_conta <= now
         obs: ao salvar drh > cadastro funcionario, seta data_reativa_conta = now */
      $contaExpirou = ! empty($user['tempo_expira_conta']) && ! empty($user['data_reativa_conta']) &&
                      time() - strtotime($user['data_reativa_conta']) > $user['tempo_expira_conta'] * 60 * 60 * 24;

      if($contaExpirou) {
        $sql = "UPDATE funcionario SET ativo = 0 WHERE ref_cod_pessoa_fj = $1";
        $this->fetchPreparedQuery($sql, $userId, true);

        $this->messages->append("Aparentemente a conta de usu�rio expirou, por favor, " .
                                "entre em contato com o administrador do sistema.", "error", false, "error");
      }

      // considera como acesso multiplo, acesso em diferentes IPs em menos de $tempoMultiploAcesso minutos
      $tempoMultiploAcesso = 10;
      $tempoEmEspera = abs(time() - strftime("now") - strtotime($user['data_login'])) / 60;

      $multiploAcesso = $tempoEmEspera <= $tempoMultiploAcesso &&
                        $user['ip_ultimo_acesso'] != $this->getClientIP();
    
      if ($multiploAcesso) {
        $minutosEmEspera = round($tempoMultiploAcesso - $tempoEmEspera) + 1;
        $this->messages->append("Aparentemente sua conta foi acessada em outro computador nos �ltimos " .
                                "$tempoMultiploAcesso minutos, caso n�o tenha sido voc�, " . 
                                "por favor, altere sua senha ou tente novamente em $minutosEmEspera minutos",
                                "error", false, "error");
      }
      #TODO verificar se conta nunca usada (exibir "Sua conta n&atilde;o est&aacute; ativa. Use a op&ccedil;&atilde;o 'Nunca usei a intrenet'." ?)
    }
    return ! $this->messages->hasMsgWithType("error");
  }


  public function startLoginSession($userId, $redirectTo = '') {
    $sql = "SELECT ref_cod_pessoa_fj, opcao_menu, ref_cod_setor_new, tipo_menu, email, status_token FROM funcionario WHERE ref_cod_pessoa_fj = $1";
    $record = $this->fetchPreparedQuery($sql, $userId, true, 'first-line');

    @session_start();
    $_SESSION = array();
    $_SESSION['itj_controle'] = 'logado';
    $_SESSION['id_pessoa']    = $record['ref_cod_pessoa_fj'];
    $_SESSION['pessoa_setor'] = $record['ref_cod_setor_new'];
    $_SESSION['menu_opt']     = unserialize($record['opcao_menu']);
    $_SESSION['tipo_menu']    = $record['tipo_menu'];
    @session_write_close();

    $this->logado = true;
    $this->messages->append("Usu�rio logado com sucesso.", "success");

    $this->logAccess($userId);

    //redireciona para usu�rio informar email, caso este seja inv�lido
    if (! filter_var($record['email'], FILTER_VALIDATE_EMAIL))
       header("Location: /module/Usuario/AlterarEmail");
    elseif(! empty($redirectTo))
       header("Location: $redirectTo");
  }


  protected function destroyLoginSession($addMsg = false) {
    @session_start();
    $_SESSION = array();
    @session_destroy();

    if ($addMsg)
      $this->messages->append("Usu�rio deslogado com sucesso.", "success");
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


  protected function logAccess($userId) {
    $sql = "UPDATE funcionario SET ip_logado = '{$this->getClientIP()}', data_login = NOW() WHERE ref_cod_pessoa_fj = $1";
    $this->fetchPreparedQuery($sql, $userId, true);
  }


  // wrapper para $db->execPreparedQuery($sql, $params)
  protected function fetchPreparedQuery($sql, $params = array(), $hideExceptions = true, $returnOnly = '') {
    try{    
      $result = array();
      $db = new clsBanco();
      if ($db->execPreparedQuery($sql, $params) != false) {

        while ($db->ProximoRegistro())
          $result[] = $db->Tupla();

        if ($returnOnly == 'first-line' and isset($result[0]))
          $result = $result[0];
        elseif ($returnOnly == 'first-field' and isset($result[0]) and isset($result[0][0]))
          $result = $result[0][0];
      }
    }
    catch(Exception $e) 
    {
      if (! $hideExceptions)
        $this->messages->append($e->getMessage(), "error", true);
    }
    return $result;
  }
}
