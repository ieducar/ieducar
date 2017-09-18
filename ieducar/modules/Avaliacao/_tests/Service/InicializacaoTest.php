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
 * Avaliacao_Service_InicializacaoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_InicializacaoTest extends Avaliacao_Service_TestCommon
{
  /**
   * @expectedException CoreExt_Service_Exception
   */
  public function testInstanciaLancaExcecaoCasoCodigoDeMatriculaNaoSejaInformado()
  {
    new Avaliacao_Service_Boletim();
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testInstanciaLancaExcecaoComOpcaoNaoAceitaPelaClasse()
  {
    new Avaliacao_Service_Boletim(array('matricula' => 1, 'foo' => 'bar'));
  }

  public function testDadosDeMatriculaInicializados()
  {
    $service = $this->_getServiceInstance();
    $options = $service->getOptions();

    $this->assertEquals($this->_getConfigOption('usuario', 'cod_usuario'),
      $options['usuario']);

    $this->assertEquals($this->_getConfigOption('matricula', 'aprovado'),
      $options['aprovado']);

    $this->assertEquals($this->_getConfigOption('curso', 'hora_falta'),
      $options['cursoHoraFalta']);

    $this->assertEquals($this->_getConfigOption('curso', 'carga_horaria'),
      $options['cursoCargaHoraria']);

    $this->assertEquals($this->_getConfigOption('serie', 'carga_horaria'),
      $options['serieCargaHoraria']);

    $this->assertEquals(count($this->_getConfigOptions('anoLetivoModulo')),
      $options['etapas']);

    $this->assertEquals($this->_getConfigOptions('componenteCurricular'),
      $service->getComponentes());
  }

  public function testInstanciaRegraDeAvaliacaoAtravesDeUmNumeroDeMatricula()
  {
    $service = $this->_getServiceInstance();
    $this->assertType('RegraAvaliacao_Model_Regra', $service->getRegra());

    // TabelaArredondamento_Model_Tabela � recuperada atrav�s da inst�ncia de
    // RegraAvaliacao_Model_Regra
    $this->assertType('TabelaArredondamento_Model_Tabela', $service->getTabelaArredondamento());
  }
}