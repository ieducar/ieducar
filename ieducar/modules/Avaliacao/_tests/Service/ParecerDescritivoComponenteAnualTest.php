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

require_once 'Avaliacao/_tests/Service/ParecerDescritivoCommon.php';

/**
 * Avaliacao_Service_ParecerDescritivoComponenteAnualTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_ParecerDescritivoComponenteAnualTest extends Avaliacao_Service_ParecerDescritivoCommon
{
  protected function setUp()
  {
    $this->_setRegraOption('parecerDescritivo', RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE);
    parent::setUp();
  }

  protected function _getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim()
  {
    return new Avaliacao_Model_ParecerDescritivoComponente(array(
      'componenteCurricular' => 1,
      'parecer'              => 'Ok.'
    ));
  }

  protected function _getTestAdicionaParecerNoBoletim()
  {
    return new Avaliacao_Model_ParecerDescritivoComponente(array(
      'componenteCurricular' => 1,
      'parecer'              => 'N/D.'
    ));
  }

  protected function _getTestSalvarPareceresNoBoletimInstanciasDePareceres()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 1,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      )),
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 2,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      )),
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 3,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      )),
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 4,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      ))
    );
  }

  protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 1,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      )),
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 2,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      ))
    );
  }

  protected function _getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'id'                   => 1,
        'componenteCurricular' => 1,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      ))
    );
  }

  protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 1,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      )),
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'componenteCurricular' => 2,
        'parecer'              => 'N/D.',
      ))
    );
  }

  protected function _getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas()
  {
    return array(
      new Avaliacao_Model_ParecerDescritivoComponente(array(
        'id'                   => 1,
        'componenteCurricular' => 1,
        'parecer'              => 'N/D.',
        'etapa'                => 'An'
      )),
    );
  }

  protected function _testAdicionaParecerNoBoletimVerificaValidadores(Avaliacao_Model_ParecerDescritivoAbstract $parecer)
  {
    $this->assertEquals(1, $parecer->get('componenteCurricular'));
    $this->assertEquals('An', $parecer->etapa);
    $this->assertEquals('N/D.', $parecer->parecer);

    $validators = $parecer->getValidatorCollection();

    $this->assertEquals($this->_getEtapasPossiveisParecer(), $validators['etapa']->getOption('choices'));

    $this->assertEquals(
      $this->_getComponentesCursados(),
      array_values($validators['componenteCurricular']->getOption('choices'))
    );
  }
}