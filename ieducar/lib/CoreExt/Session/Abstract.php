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
 * @package   CoreExt_Session
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Configurable.php';

/**
 * CoreExt_Session_Abstract abstract class.
 *
 * Componente de gerenciamento de session PHP. Implementa as interfaces
 * ArrayAccess, Countable e Iterator do Standard PHP Library (SPL), tornando
 * poss�vel o acesso simples aos dados da sess�o atrav�s da interface array
 * ou orientada a objeto.
 *
 * A persist�ncia da session � implementada por uma classe adapter, uma
 * subclasse de CoreExt_Session_Storage_Interface. Isso torna simples a
 * reposi��o do storage de session: basta criar um novo adapter e passar como
 * argumento ao construtor dessa classe.
 *
 * As op��es de configura��o da classe s�o:
 * - sessionStorage: inst�ncia de CoreExt_Session_Storage_Interface
 * - session_auto_start: bool, se � para iniciar automaticamente a session
 *
 * Como mencionado, esta classe possui diversas formas de acesso aos dados
 * persistidos na session:
 *
 * <code>
 * <?php
 * $session = new CoreExt_Session();
 *
 * // Acesso OO dos dados da session
 * $session->foo = 'bar';
 * $session->bar = 'foo';
 *
 * // Acesso array dos dados da session
 * $session->foo2 = 'bar2';
 * $session->bar2 = 'foo2';
 *
 * // � poss�vel iterar o objeto CoreExt_Session
 * foreach ($session as $key => $value) {
 *   print $key . ': ' . $value . PHP_EOL;
 * }
 *
 * // Imprime:
 * // foo: bar
 * // bar: foo
 * // foo2: bar2
 * // bar2: foo2
 * </code>
 *
 * A classe se encarrega de fechar a sess�o no final da execu��o do PHP.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Session
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @todo      Implementar chamada a regenerateId de CoreExt_Session_Storage_Interface
 * @todo      Implementar funcionalidade "remeber me"
 * @version   @@package_version@@
 */
abstract class CoreExt_Session_Abstract
  implements CoreExt_Configurable, ArrayAccess, Countable, Iterator
{
  /**
   * Op��es de configura��o geral da classe.
   * @var array
   */
  protected $_options = array(
    'sessionstorage'     => NULL,
    'session_auto_start' => TRUE
  );

  /**
   * @var CoreExt_Session_Storage_Interface
   */
  protected $_sessionStorage = NULL;

  /**
   * @var array
   */
  protected $_sessionData = array();

  /**
   * Construtor.
   * @param array $options
   */
  public function __construct(array $options = array())
  {
    $this->setOptions($options);
  }

  /**
   * @see CoreExt_Configurable#setOptions($options)
   */
  public function setOptions(array $options = array())
  {
    $options = array_change_key_case($options, CASE_LOWER);

    $defaultOptions = array_keys($this->getOptions());
    $passedOptions  = array_keys($options);

    if (0 < count(array_diff($passedOptions, $defaultOptions))) {
      require_once 'CoreExt/Exception/InvalidArgumentException.php';
      throw new CoreExt_Exception_InvalidArgumentException(
        sprintf('A classe %s n�o suporta as op��es: %s.', get_class($this), implode(', ', $passedOptions))
      );
    }

    if (isset($options['sessionstorage'])) {
      $this->setSessionStorage($options['sessionstorage']);
    }

    $this->_options = array_merge($this->getOptions(), $options);
    return $this;
  }

  /**
   * @see CoreExt_Configurable#getOptions()
   */
  public function getOptions()
  {
    return $this->_options;
  }

  /**
   * Verifica se uma op��o est� setada.
   *
   * @param string $key
   * @return bool
   */
  protected function _hasOption($key)
  {
    return array_key_exists($key, $this->_options);
  }

  /**
   * Retorna um valor de op��o de configura��o ou NULL caso a op��o n�o esteja
   * setada.
   *
   * @param string $key
   * @return mixed|NULL
   */
  public function getOption($key)
  {
    return $this->_hasOption($key) ? $this->_options[$key] : NULL;
  }

  /**
   * Setter.
   * @param CoreExt_Session_Storage_Interface $storage
   */
  public function setSessionStorage(CoreExt_Session_Storage_Interface $storage)
  {
    $this->_sessionStorage = $storage;
  }

  /**
   * Getter.
   * @return CoreExt_Session_Storage_Interface
   */
  public function getSessionStorage()
  {
    if (is_null($this->_sessionStorage)) {
      require_once 'CoreExt/Session/Storage/Default.php';
      $this->setSessionStorage(new CoreExt_Session_Storage_Default(array(
        'session_auto_start' => $this->getOption('session_auto_start')
      )));
    }
    return $this->_sessionStorage;
  }

  /**
   * Getter.
   *
   * Retorna o array de dados gerenciados por CoreExt_Session_Storage_Interface,
   * atualizando o atributo $_sessionData quando este diferir do valor retornado.
   *
   * @return array
   * @see current()
   */
  public function getSessionData()
  {
    if ($this->_sessionData != $this->getSessionStorage()->getSessionData()) {
      $this->_sessionData = $this->getSessionStorage()->getSessionData();
    }
    return $this->_sessionData;
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetexists.php
   */
  public function offsetExists($offset)
  {
    $value = $this->getSessionStorage()->read($offset);
    return isset($value);
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetget.php
   */
  public function offsetGet($offset)
  {
    if ($this->offsetExists($offset)) {
      return $this->getSessionStorage()->read($offset);
    }
    return NULL;
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetset.php
   */
  public function offsetSet($offset, $value)
  {
    $this->getSessionStorage()->write((string) $offset, $value);
  }

  /**
   * @link http://br.php.net/manual/en/arrayaccess.offsetunset.php
   */
  public function offsetUnset($offset)
  {
    $this->getSessionStorage()->remove($offset);
  }

  /**
   * Implementa o m�todo m�gico __set().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   * @param mixed $val
   */
  public function __set($key, $value)
  {
    $this->offsetSet($key, $value);
  }

  /**
   * Implementa o m�todo m�gico __get().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->offsetGet($key);
  }

  /**
   * Implementa o m�todo m�gico __isset().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   * @return bool
   */
  public function __isset($key)
  {
    return $this->offsetExists($key);
  }

  /**
   * Implementa o m�todo m�gico __unset().
   * @link  http://php.net/manual/en/language.oop5.overloading.php
   * @param string|int $key
   */
  public function __unset($key)
  {
    $this->offsetUnset($key);
  }

  /**
   * @link http://br.php.net/manual/en/countable.count.php
   * @return int
   */
  public function count()
  {
    return $this->getSessionStorage()->count();
  }

  /**
   * Implementa o m�todo Iterator::current(). Chama m�todo getSessionData()
   * para atualizar o atributo $_sessionData, permitindo a a��o da fun��o
   * {@link http://br.php.net/current current()}.
   *
   * @link http://br.php.net/manual/en/iterator.current.php
   */
  public function current()
  {
    $this->getSessionData();
    return current($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.key.php
   */
  public function key()
  {
    $data = $this->getSessionData();
    return key($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.next.php
   */
  public function next()
  {
    $data = $this->getSessionData();
    next($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.rewind.php
   */
  public function rewind()
  {
    $data = $this->getSessionData();
    reset($this->_sessionData);
  }

  /**
   * @link http://br.php.net/manual/en/iterator.valid.php
   */
  public function valid()
  {
    $key = key($this->_sessionData);
    return isset($key) ? TRUE : FALSE;
  }
}