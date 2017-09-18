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
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';

/**
 * AnoEscolarDataMapperTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class AnoEscolarDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper($this->getDbMock());
  }

  public function testGetterDeComponenteCurricularMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('ComponenteCurricular_Model_ComponenteDataMapper', $this->_mapper->getComponenteDataMapper());
  }

  public function testFindComponentePorCurso()
  {
    // Valores de retorno
    $expected = array(
      new ComponenteCurricular_Model_Componente(array('id' => 1, 'nome' => 'Matem�tica')),
      new ComponenteCurricular_Model_Componente(array('id' => 2, 'nome' => 'Portugu�s'))
    );

    // Valores de retorno para o mock do adapter
    $returnValues = array(
      0 => array('componente_curricular_id' => 1),
      1 => array('componente_curricular_id' => 2)
    );

    // Configura mock para retornar um array de IDs de componentes
    $dbMock = $this->getDbMock();

    $dbMock->expects($this->any())
           ->method('ProximoRegistro')
           ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $dbMock->expects($this->any())
           ->method('Tupla')
           ->will($this->onConsecutiveCalls($returnValues[0], $returnValues[1]));

    // Mock para �rea de conhecimento
    $mock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
    $mock->expects($this->any())
         ->method('find')
         ->will($this->onConsecutiveCalls($expected[0], $expected[1]));

    // Substitui o data mapper padr�o pelo mock
    $this->_mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper($dbMock);
    $this->_mapper->setComponenteDataMapper($mock);
    $componentes = $this->_mapper->findComponentePorCurso(1);

    $this->assertEquals($expected, $componentes);
  }

  public function testFindComponentePorSerie()
  {
    // Valores de retorno
    $expected = array(
      1 => new ComponenteCurricular_Model_Componente(array('id' => 1, 'nome' => 'Matem�tica')),
      2 => new ComponenteCurricular_Model_Componente(array('id' => 2, 'nome' => 'Portugu�s'))
    );

    // Valores de retorno para o mock do adapter
    $returnValues = array(
      0 => array('componente_curricular_id' => 1, 'ano_escolar_id' => 1),
      1 => array('componente_curricular_id' => 2, 'ano_escolar_id' => 1)
    );

    // Configura mock para retornar um array de IDs de componentes
    $dbMock = $this->getDbMock();

    $dbMock->expects($this->any())
           ->method('ProximoRegistro')
           ->will($this->onConsecutiveCalls(TRUE, TRUE, FALSE));

    $dbMock->expects($this->any())
           ->method('Tupla')
           ->will($this->onConsecutiveCalls($returnValues[0], $returnValues[1]));

    // Mock para �rea de conhecimento
    $mock = $this->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');
    $mock->expects($this->any())
         ->method('find')
         ->will($this->onConsecutiveCalls($expected[1], $expected[2]));

    // Substitui o data mapper padr�o pelo mock
    $this->_mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper($dbMock);
    $this->_mapper->setComponenteDataMapper($mock);
    $componentes = $this->_mapper->findComponentePorSerie(1);

    $this->assertEquals($expected, $componentes);
  }
}