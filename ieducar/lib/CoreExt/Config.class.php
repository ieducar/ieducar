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
 * CoreExt_Config class.
 *
 * Transforma um array comum em um objeto CoreExt_Config, permitindo que esse
 * array seja acessado com o operador ->, assim como vari�veis de inst�ncia.
 * Dessa forma, um array como:
 *
 * <code>
 * $array = array(
 *   'app' => array(
 *     'database' => array(
 *       'dbname'   => 'ieducar',
 *       'hostname' => 'localhost',
 *       'password' => 'ieducar',
 *       'username' => 'ieducar',
 *     )
 *   ),
 *   'CoreExt' => '1'
 * )
 * </code>
 *
 * Pode ser acessado dessa forma:
 *
 * <code>
 * $config = new CoreExt_Config($array);
 * print $config->app->database->dbname;
 * </code>
 *
 * Essa classe foi fortemente baseada na classe Zend_Config do Zend Framework s�
 * que implementa menos funcionalidades.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     CoreExt
 * @subpackage  Config
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */
class CoreExt_Config implements Countable, Iterator
{

  /**
   * Array de sobre sobrecarga
   * @var array
   */
  protected $config;

  /**
   * Array com mensagens de erro causadas por fun��es PHP.
   * @var array
   */
  protected $errors = array();

  /**
   * �ndice interno do array para a implementa��o da interface Iterator.
   * @var int
   */
  private $_index = 0;

  /**
   * Quantidade de items do array de sobrecarga $config para a implementa��o da interface Countable.
   * @var int
   */
  private $_count = 0;

  /**
   * Construtor da classe.
   *
   * @param  $array  Array associativo com as diretivas de configura��o.
   */
  public function __construct($array)
  {
    foreach ($array as $key => $val) {
      if (is_array($val)) {
        $this->config[$key] = new self($val);
      }
      else {
        $this->config[$key] = $val;
      }
    }

    $this->_count = count($this->config);
  }

  /**
   * Retorna o valor do array de sobrecarga $config.
   *
   * Este m�todo deve ser usado toda vez que a vari�vel de configura��o puder
   * ser sobrescrita por um storage de configura��o externa ao c�digo, como o
   * arquivo ini. Um exemplo seria para a cria��o de um arquivo on the fly no
   * filesystem. No c�digo pode ser assumido que o local padr�o ser�
   * intranet/tmp mas, se esse valor puder ser sobrescrito pelo ini, esse m�todo
   * dever� ser utilizado:
   * <code>
   * $dir = $config->get($config->app->filesystem->tmp_dir, 'intranet/tmp');
   * </code>
   *
   * Se a vari�vel de configura��o n�o for sobrescrita por um arquivo ini ou
   * array de configura��o, o valor padr�o (segundo par�metro) ser� utilizado.
   *
   * @param   mixed  $value1  Valor retornado pelo array de configura��o sobrecarregado
   * @param   mixed  $value2  Valor padr�o caso n�o exista uma configura��o sobrecarregada
   * @return  mixed
   */
  public function get($value1, $value2 = NULL)
  {
    if (NULL != $value1) {
      return $value1;
    }

    if (NULL == $value2) {
      throw new Exception('O segundo par�metro deve conter algum valor n�o nulo.');
    }

    return $value2;
  }

  /**
   * Retorna o valor armazenado pelo �ndice no array de sobrecarga $config.
   *
   * @param   $key    �ndice (nome) da vari�vel criada por sobrecarga
   * @param   $value  Valor padr�o caso o �ndice n�o exista
   * @return  mixed   O valor armazenado em
   */
  private function getFrom($key, $value = NULL)
  {
    if (array_key_exists($key, $this->config)) {
      $value = $this->config[$key];
    }

    return $value;
  }

  /**
   * Implementa��o do m�todo m�gico __get().
   *
   * @param $key
   * @return unknown_type
   */
  public function __get($key) {
    return $this->getFrom($key);
  }

  /**
   * Retorna o conte�do do array de sobrecarga em um array associativo simples.
   * @return  array
   */
  public function toArray()
  {
    $array = array();
    foreach ($this->config as $key => $value) {
      $array[$key] = $value;
    }
    return $array;
  }

  /**
   * Implementa��o do m�todo count() da interface Countable.
   */
  public function count() {
    return $this->_count;
  }

  /**
   * Implementa��o do m�todo next() da interface Iterator.
   */
  public function next() {
    next($this->config);
    ++$this->_index;
  }

  /**
   * Implementa��o do m�todo next() da interface Iterator.
   */
  public function rewind() {
    reset($this->config);
    $this->_index = 0;
  }

  /**
   * Implementa��o do m�todo current() da interface Iterator.
   */
  public function current() {
    return current($this->config);
  }

  /**
   * Implementa��o do m�todo key() da interface Iterator.
   */
  public function key() {
    return key($this->config);
  }

  /**
   * Implementa��o do m�todo valid() da interface Iterator.
   */
  public function valid() {
    return $this->_index < $this->_count && $this->_index > -1;
  }

  /**
   * Merge recursivo mantendo chaves distintas.
   *
   * Realiza um merge recursivo entre dois arrays. � semelhante a fun��o PHP
   * {@link http://php.net/array_merge_recursive array_merge_recursive} exceto
   * pelo fato de que esta mant�m apenas um valor de uma chave do array ao inv�s
   * de criar m�ltiplos valores para a mesma chave como na fun��o original.
   *
   * @author  Daniel Smedegaard Buus <daniel@danielsmedegaardbuus.dk>
   * @link    http://www.php.net/manual/pt_BR/function.array-merge-recursive.php#89684  C�digo fonte original
   * @param   array  $arr1
   * @param   array  $arr2
   * @return  array
   */
  protected function &arrayMergeRecursiveDistinct(&$arr1, &$arr2)
  {
    $merged = $arr1;

    if (is_array($arr2)) {
      foreach ($arr2 as $key => $val) {
        if (is_array($arr2[$key])) {
          $merged[$key] = is_array($merged[$key]) ?
            $this->arrayMergeRecursiveDistinct($merged[$key], $arr2[$key]) : $arr2[$key];
        }
        else {
          $merged[$key] = $val;
        }
      }
    }

    return $merged;
  }

  /**
   * M�todo callback para a fun��o set_error_handler().
   *
   * Handler para os erros internos da classe. Dessa forma, � poss�vel usar
   * os blocos try/catch para lan�ar exce��es.
   *
   * @see  http://php.net/set_error_handler
   * @param  $errno
   * @param  $errstr
   */
  protected function configErrorHandler($errno, $errstr) {
    $this->errors[] = array($errno => $errstr);
  }

}