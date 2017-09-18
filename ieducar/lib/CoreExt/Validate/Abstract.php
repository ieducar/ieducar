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
 * @package   CoreExt_Validate
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Validate/Interface.php';

/**
 * CoreExt_Validate_Abstract abstract class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_Validate
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
abstract class CoreExt_Validate_Abstract implements CoreExt_Validate_Interface
{
  /**
   * Op��es de configura��o geral da classe.
   * @var array
   */
  protected $_options = array(
    'required' => TRUE,
    'trim' => TRUE,
  );

  /**
   * Valor n�o sanitizado que foi informado ao validador.
   * @var mixed
   */
  protected $_value = NULL;

  /**
   * Valor sanitizado.
   * @var mixed
   */
  protected $_sanitized = NULL;

  /**
   * Mensagem padr�o para erros de valor obrigat�rio.
   * @var string
   */
  protected $_requiredMessage = 'Obrigat�rio.';

  /**
   * Mensagem padr�o para erros de invalidez.
   * @var string
   */
  protected $_invalidMessage = 'Inv�lido.';

  /**
   * Construtor.
   *
   * Pode receber array com op��es de configura��o da classe.
   *
   * @param array $options
   */
  public function __construct(array $options = array())
  {
    $this->_options = array_merge($this->getOptions(), $this->_getDefaultOptions());
    $this->setOptions($options);
  }

  /**
   * Configura as op��es do validador.
   *
   * M�todo de checagem de op��es inspirado na t�cnica empregada no
   * {@link http://www.symfony-project.org symfony framework}.
   *
   * @param  array $options
   * @throws InvalidArgumentException Lan�a exce��o n�o verificada caso alguma
   *   op��o passada ao m�todo n�o exista na defini��o da classe
   */
  public function setOptions(array $options = array())
  {
    $defaultOptions = array_keys($this->getOptions());
    $passedOptions  = array_keys($options);

    if (0 < count(array_diff($passedOptions, $defaultOptions))) {
      throw new InvalidArgumentException(
        sprintf('A classe %s n�o suporta as op��es: %s.', get_class($this), implode(', ', $passedOptions))
      );
    }

    $this->_options = array_merge($this->getOptions(), $options);
  }

  /**
   * @see CoreExt_Validate_Interface#getOptions()
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
    return
      isset($this->_options[$key]) &&
      !$this->_isEmpty($this->_options[$key]);
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
   * Permite que uma classe que estenda CoreExt_Validate_Abstract a definir
   * valores de op��es pr�-definidos adequados ao caso espec�fico.
   *
   * @return array
   */
  protected abstract function _getDefaultOptions();

  /**
   * @see CoreExt_Validate_Interface#isValid($value)
   */
  public function isValid($value)
  {
    $this->_value = $value;
    $value = $this->_sanitize($value);

    if (TRUE == $this->getOption('trim')) {
      $value = trim($value);
    }

    $this->_sanitized = $value;

    if (TRUE == $this->getOption('required') && $this->_isEmpty($value)) {
      throw new Exception($this->_requiredMessage);
    }

    return $this->_validate($value);
  }

  /**
   * Toda classe que estende CoreExt_Validate_Abstract deve implementar esse
   * m�todo com a l�gica de valida��o adequada.
   *
   * @param  string $value
   * @return bool
   */
  protected abstract function _validate($value);

  /**
   * Realiza uma sanitiza��o
   * @param mixed $value
   * @return mixed
   */
  protected function _sanitize($value)
  {
    return $value;
  }

  /**
   * Verifica se um dado valor est� vazio.
   *
   * Como vazio, entende-se string vazia (''), array sem itens (array()), o
   * valor NULL e zero (0) num�rico.
   *
   * @param  mixed $value
   * @return bool
   */
  protected function _isEmpty($value)
  {
    return in_array(
      $value, array('', array(), NULL), TRUE
    );
  }

  /**
   * Retorna uma mensagem de erro configurada em $_options.
   *
   * A mensagem de erro pode ser uma string ou um array. Se for uma string,
   * ocorrer� a substitui��o dos placeholders. Se for um array, dever� ser
   * especificado duas mensagens de erro, uma para a forma singular e outra
   * para o plural. O placeholder @value ser� verificado para definir se a
   * mensagem deve ser formatada no plural ou no singular.
   *
   * Exemplo de array de mensagem de erro que usa variante de n�mero:
   *
   * <code>
   * <?php
   * $message = array(
   *   array(
   *     'singular' => '@value problema encontrado.'
   *     'plural'   => '@value problemas encontrados.'
   *   )
   * );
   *
   * // Iria imprimir:
   * // singular (@value = 1): 1 problema encontrado
   * // plural (@value = 4): 4 problemas encontrados
   * </code>
   *
   * @param  array|string  $key      O identificador da mensagem no array $_options
   * @param  array         $options  Array associativo para substitui��o de valores
   * @return string
   * @todo   Implementar substitui��o com formata��o padr�o, semelhante ao
   *   a fun��o Drupal {@link http://api.drupal.org/t t()}.
   * @todo   Implementar formata��o singular/plural em uma classe diferente,
   *         como m�todo p�blico, permitindo realizar o teste.
   */
  protected function _getErrorMessage($key, array $options = array())
  {
    $message = $this->getOption($key);

    if (is_array($message)) {
      // Verifica o tipo de @value para determinar a quantidade de $count
      if (is_array($options['@value'])) {
        $count = count($options['@value']);
      }
      elseif (is_numeric($options['@value'])) {
        $count = count($options['@value']);
      }
      else {
        $count = 1;
      }

      if (1 < $count) {
        $message = $message['plural'];
        $options['@value'] = implode(', ', $options['@value']);
      }
      else {
        $message = $message['singular'];
        $options['@value'] = array_shift($options['@value']);
      }
    }

    return strtr($message, $options);
  }

  /**
   * @see CoreExt_Validate_Interface#getValue()
   */
  public function getValue()
  {
    return $this->_value;
  }

  /**
   * @see CoreExt_Validate_Interface#getSanitizedValue()
   */
  public function getSanitizedValue()
  {
    return $this->_sanitized;
  }
}