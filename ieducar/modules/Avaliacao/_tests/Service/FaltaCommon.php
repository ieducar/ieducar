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
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'Avaliacao/_tests/Service/TestCommon.php';

/**
 * Avaliacao_Service_FaltaCommon abstract class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
abstract class Avaliacao_Service_FaltaCommon extends Avaliacao_Service_TestCommon
{
  /**
   * @return Avaliacao_Model_FaltaComponente
   */
  protected abstract function _getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim();

  /**
   * @return Avaliacao_Model_FaltaComponente
   */
  protected abstract function _getFaltaTestAdicionaFaltaNoBoletim();

  /**
   * Realiza asser��es espec�ficas para os validadores de uma inst�ncia de
   * Avaliacao_Model_FaltaAbstract
   */
  protected abstract function _testAdicionaFaltaNoBoletimVerificaValidadores(Avaliacao_Model_FaltaAbstract $falta);

  /**
   * @see Avaliacao_Service_FaltaCommon#_getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
   */
  public function testInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim()
  {
    $service = $this->_getServiceInstance();

    $falta = $this->_getFaltaTestInstanciaDeFaltaERegistradaApenasUmaVezNoBoletim();

    // Atribui��o simples
    $service->addFalta($falta)
            ->addFalta($falta);

    $this->assertEquals(1, count($service->getFaltas()));

    // Via atribui��o em lote
    $falta = clone $falta;
    $service->addFaltas(array($falta, $falta, $falta));

    $this->assertEquals(2, count($service->getFaltas()));
  }

  /**
   * @see Avaliacao_Service_FaltaCommon#_getFaltaTestAdicionaFaltaNoBoletim()
   * @see Avaliacao_Service_FaltaCommon#_testAdicionaFaltaNoBoletimVerificaValidadores()
   */
  public function testAdicionaFaltaNoBoletim()
  {
    $service = $this->_getServiceInstance();

    $falta = $this->_getFaltaTestAdicionaFaltaNoBoletim();

    $faltaOriginal = clone $falta;
    $service->addFalta($falta);

    $faltas = $service->getFaltas();
    $serviceFalta = array_shift($faltas);

    // Valores declarados explicitamente, verifica��o expl�cita
    $this->assertEquals($faltaOriginal->quantidade, $serviceFalta->quantidade);

    // Valores populados pelo service
    $this->assertNotEquals($faltaOriginal->etapa, $serviceFalta->etapa);

    // Validadores injetados no objeto
    $this->_testAdicionaFaltaNoBoletimVerificaValidadores($serviceFalta);
  }
}