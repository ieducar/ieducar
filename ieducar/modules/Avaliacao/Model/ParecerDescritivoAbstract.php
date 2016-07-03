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
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Avaliacao/Model/Etapa.php';

/**
 * Avaliacao_Model_ParecerDescritivoAbstract abstract class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
abstract class Avaliacao_Model_ParecerDescritivoAbstract extends Avaliacao_Model_Etapa
{
  protected $_data = array(
    'parecerDescritivoAluno' => NULL,
    'parecer'                => NULL
  );

  protected $_references = array(
    'parecerDescritivoAluno' => array(
      'value' => NULL,
      'class' => 'Avaliacao_Model_ParecerDescritivoAluno',
      'file'  => 'Avaliacao/Model/ParecerDescritivoAluno.php'
    )
  );

  /**
   * @see CoreExt_Entity_Validatable#getDefaultValidatorCollection()
   */
  public function getDefaultValidatorCollection()
  {
    $etapa  = $this->getValidator('etapa');
    $etapas = $etapa->getOption('choices') + array('An');

    $etapa->setOptions(array('choices' => $etapas));

    return array(
      'etapa'   => $etapa,
      'parecer' => new CoreExt_Validate_String()
    );
  }

  /**
   * Implementa m�todo m�gico __toString().
   * @link http://br.php.net/__toString
   * @return string
   */
  public function __toString()
  {
    return (string)$this->parecer;
  }
}
