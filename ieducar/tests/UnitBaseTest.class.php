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
 * @package   UnitTests
 * @since     Arquivo dispon�vel desde a vers�o 1.0.1
 * @version   $Id$
 */

/**
 * UnitBaseTest abstract class.
 *
 * Abstrai o PHPUnit, diminuindo a depend�ncia de seu uso.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   UnitTests
 * @since     Classe dispon�vel desde a vers�o 1.0.1
 * @version   @@package_version@@
 */
abstract class UnitBaseTest extends PHPUnit_Framework_TestCase
{
  /**
   * M�todos a serem exclu�dos da lista de m�todos a serem mockados por
   * getCleanMock().
   *
   * @var array
   */
  protected $_excludedMethods = array();

  /**
   * Setter para o atributo $_excludedMethods.
   *
   * @param array $methods
   * @return PHPUnit_Framework_TestCase Prov� interface flu�da
   */
  public function setExcludedMethods(array $methods)
  {
    $this->_excludedMethods = $methods;
    return $this;
  }

  /**
   * Getter para o atributo $_excludedMethods.
   * @return array
   */
  public function getExcludedMethods()
  {
    return $this->_excludedMethods;
  }

  /**
   * Reseta o valor do atributo $_excludedMethods.
   *
   * @return PHPUnit_Framework_TestCase Prov� interface flu�da
   */
  public function resetExcludedMethods()
  {
    $this->_excludedMethods = array();
    return $this;
  }

  /**
   * Remove os m�todos indicados por setExcludedMethods() da lista de m�todos
   * a serem mockados.
   *
   * @param array $methods
   * @return array
   */
  protected function _cleanMockMethodList(array $methods)
  {
    foreach ($methods as $key => $method) {
      if (FALSE !== array_search($method, $this->getExcludedMethods())) {
        unset($methods[$key]);
      }
    }
    $this->resetExcludedMethods();
    return $methods;
  }

  /**
   * Retorna um objeto mock do PHPUnit, alterando os valores padr�es dos
   * par�metros $call* para FALSE.
   *
   * Faz uma limpeza da lista de m�todos a serem mockados ao chamar
   * _cleanMockMethodList().
   *
   * @param  string  $className
   * @param  array   $mockMethods
   * @param  array   $args
   * @param  string  $mockName
   * @param  bool    $callOriginalConstructor
   * @param  bool    $callOriginalClone
   * @param  bool    $callOriginalAutoload
   * @return PHPUnit_Framework_MockObject_MockObject
   */
  public function getCleanMock($className, array $mockMethods = array(),
    array $args = array(), $mockName = '', $callOriginalConstructor = FALSE,
    $callOriginalClone = FALSE, $callOriginalAutoload = FALSE)
  {
    if (0 == count($mockMethods)) {
      $reflectiveClass = new ReflectionClass($className);
      $methods = $reflectiveClass->getMethods();
      $mockMethods = array();

      foreach ($methods as $method) {
        if (!$method->isFinal() && !$method->isAbstract() && !$method->isPrivate()) {
          $mockMethods[] = $method->name;
        }
      }
    }

    $mockMethods = $this->_cleanMockMethodList($mockMethods);

    if ($mockName == '') {
      $mockName = $className . '_Mock_' . substr(md5(uniqid()), 0, 6);
    }

    return $this->getMock($className, $mockMethods, $args, $mockName,
      $callOriginalConstructor, $callOriginalClone, $callOriginalAutoload);
  }

  /**
   * Retorna um mock da classe de conex�o clsBanco.
   * @return clsBanco
   */
  public function getDbMock()
  {
    // Cria um mock de clsBanco, preservando o c�digo do m�todo formatValues
    return $this->setExcludedMethods(array('formatValues'))
                ->getCleanMock('clsBanco');
  }

  /**
   * Controla o buffer de sa�da.
   * @param  bool $enable
   * @return bool|string
   */
  public function outputBuffer($enable = TRUE)
  {
    if (TRUE == $enable) {
      ob_start();
      return TRUE;
    }
    else {
      $contents = ob_get_contents();
      ob_end_clean();
      return $contents;
    }
  }
}