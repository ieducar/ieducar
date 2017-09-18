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

/**
 * CoreExt_Config_Ini class.
 *
 * Essa classe torna poss�vel o uso de um arquivo .ini como meio de configura��o
 * da aplica��o. O parsing do arquivo � feito atrav�s da fun��o PHP nativa
 * parse_ini_file. Possibilita o uso de heran�a simples e separa��o de
 * namespaces no arquivo ini, tornando simples a cria��o de diferentes espa�os
 * de configura��o para o uso em ambientes diversos como produ��o,
 * desenvolvimento, testes e outros.
 *
 * Para o uso dessa classe, � necess�rio que o arquivo ini tenha no m�nimo uma
 * se��o de configura��o. A se��o padr�o a ser usada � a production mas isso
 * n�o impede que voc� a nomeie como desejar.
 *
 * Essa classe foi fortemente baseada na classe Zend_Config_Ini do Zend
 * Framework.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     CoreExt
 * @subpackage  Config
 * @see         lib/CoreExt/Config.class.php
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */
class CoreExt_Config_Ini extends CoreExt_Config
{

  /**
   * Caractere de heran�a das se��es do arquivo ini.
   */
  const COREEXT_CONFIG_INI_INHERITANCE_SEP = ':';

  /**
   * Caractere de namespace das diretivas do arquivo ini.
   */
  const COREEXT_CONFIG_INI_NAMESPACE_SEP   = '.';

  /**
   * Array contendo as diretivas de configura��o separadas por namespace.
   * @var array
   */
  protected $iniArr = array();

  /**
   * Construtor.
   *
   * @param  $filename  Caminho para o arquivo ini
   * @param  $section   Se��o desejada para o carregamento das configura��es
   */
  public function __construct($filename, $section = 'production')
  {
    require_once 'CoreExt/Config.class.php';

    $this->iniArr = $this->loadFile($filename);
    parent::__construct($this->iniArr[$section]);
  }

  /**
   * Carrega as configura��es para o ambiente desejado (se��o do arquivo ini)
   * @param  string  $section
   */
  public function changeEnviroment($section = 'production') {
    $this->changeSection($section);
  }

  /**
   * Verifica se possui a se��o desejada.
   * @param  string  $section
   */
  function hasEnviromentSection($section) {
    return is_array($this->iniArr[$section]);
  }

  /**
   * Carrega as configura��o da se��o desejada.
   * @param  string  $section
   */
  protected function changeSection($section = 'production') {
    parent::__construct($this->iniArr[$section]);
  }

  /**
   * Parsing do arquivo ini.
   *
   * Realiza o parsing do arquivo ini, separando as se��es, criando as rela��es
   * de heran�a e separando cada diretiva do arquivo em um array
   * multidimensional.
   *
   * @link    http://php.net/parse_ini_file  Documenta��o da fun��o parse_ini_file
   * @param   string  $filename
   * @throws  Exception
   * @return  array
   */
  private function loadFile($filename)
  {
    $iniArr = array();

    // Faz o parsing separando as se��es (par�metro TRUE)
    // Supress�o simples dificulta os unit test. Altera o error handler para
    // que use um m�todo da classe CoreExt_Config.
    set_error_handler(array($this, 'configErrorHandler'), E_ALL);
    $config = parse_ini_file($filename, TRUE);
    restore_error_handler();

    /*
     * No PHP 5.2.7 o array vem FALSE quando existe um erro de sintaxe. Antes
     * o retorno vinha como array vazio.
     * @link  http://php.net/parse_ini_file#function.parse-ini-file.changelog  Changelog da fun��o parse_ini_file
     */
    if (count($this->errors) > 0) {
      throw new Exception('Arquivo ini com problemas de sintaxe. Verifique a sintaxe arquivo \''. $filename .'\'.');
    }

    foreach ($config as $section => $data) {
      $index = $section;

      if (FALSE !== strpos($section, self::COREEXT_CONFIG_INI_INHERITANCE_SEP)) {
        $sections = explode(self::COREEXT_CONFIG_INI_INHERITANCE_SEP, $section);
        // Apenas uma heran�a por se��o � permitida
        if (count($sections) > 2) {
          throw new Exception('N�o � poss�vel herdar mais que uma se��o.');
        }

        // Armazena se��o atual e se��o de heran�a
        $section = trim($sections[0]);
        $extends = trim($sections[1]);
      }

      // Processa as diretivas da se��o atual para separarar os namespaces
      $iniArr[$section] = $this->processSection($config[$index]);

      /*
       * Verifica se a se��o atual herda de alguma outra se��o. Se a se��o de
       * heran�a n�o existir, lan�a uma exce��o.
       */
      if (isset($extends)) {
        if (!array_key_exists($extends, $iniArr)) {
          $message = sprintf('N�o foi poss�vel estender %s, se��o %s n�o existe',
            $section, $extends);
          throw new Exception($message);
        }

        // Mescla recursivamente os dois arrays. Os valores definidos na se��o
        // atual n�o s�o sobrescritos
        $iniArr[$section] = $this->arrayMergeRecursiveDistinct($iniArr[$extends], $iniArr[$section]);
        unset($extends);
      }
    }

    return $iniArr;
  }

  /**
   * Processa uma se��o de um array de arquivo ini.
   *
   * Processa a se��o, inclusive as diretivas da se��o, separando-as em
   * um array em namespace. Dessa forma, uma diretiva que era, por exemplo,
   * app.database.dbname = ieducardb ir� se tornar:
   * <code>
   * app => array(database => array(dbname => ieducardb))
   * </code>
   *
   * Diretivas no mesmo namespace viram novas chaves no mesmo array:
   * app.database.hostname => localhost
   * <code>
   * app => array(
   *   database => array(
   *     dbname   => ieducardb
   *     hostname => localhost
   *   )
   * )
   * </code>
   *
   * @param   array  $data  Array contendo as diretivas de uma se��o do arquivo ini
   * @return  array
   */
  private function processSection(array $data)
  {
    $entries = $data;
    $config  = array();

    foreach ($entries as $key => $value) {
      if (FALSE !== strpos($key, self::COREEXT_CONFIG_INI_NAMESPACE_SEP)) {
        $keys = explode(self::COREEXT_CONFIG_INI_NAMESPACE_SEP, $key);
      }
      else {
        $keys = (array) $key;
      }

      $config = $this->processDirectives($value, $keys, $config);
    }

    return $config;
  }

  /**
   * Cria recursivamente um array aninhado (namespaces) usando os �ndices
   * progressivamente.
   *
   * Exemplo:
   * <code>
   * $value  = ieducardb
   * $keys   = array('app', 'database', 'dbname');
   *
   * $config['app'] => array(
   *   'database' => array(
   *     'dbname' => 'ieducardb'
   *   )
   * );
   * </code>
   *
   * @param   mixed  $value   O valor da diretiva parseada por parse_ini_file
   * @param   array  $keys    O array contendo as chaves das diretivas (0 => app, 1 => database, 2 => dbname)
   * @param   array  $config  O array cont�iner com as chaves em suas respectivas dimens�es
   * @return  array
   */
  private function processDirectives($value, $keys, $config = array())
  {
    $key = array_shift($keys);

    if (count($keys) > 0) {
      if (!array_key_exists($key, $config)) {
        $config[$key] = array();
      }

      $config[$key] = $this->processDirectives($value, $keys, $config[$key]);
    }
    else {
      $config[$key] = $value;
    }

    return $config;
  }

}