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

require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

/**
 * ComponenteDataMapperTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class ComponenteDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new ComponenteCurricular_Model_ComponenteDataMapper();
  }

  public function testGetterDeAreaDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('AreaConhecimento_Model_AreaDataMapper', $this->_mapper->getAreaDataMapper());
  }

  public function testGetterDeAnoEscolarDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('ComponenteCurricular_Model_AnoEscolarDataMapper',
      $this->_mapper->getAnoEscolarDataMapper());
  }

  public function testFindAreaConhecimento()
  {
    // Valores de retorno
    $returnValue = array(new AreaConhecimento_Model_Area(array('id' => 1, 'nome' => 'Ci�ncias exatas')));

    // Mock para �rea de conhecimento
    $mock = $this->getCleanMock('AreaConhecimento_Model_AreaDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->will($this->returnValue($returnValue));

    // Substitui o data mapper padr�o pelo mock
    $this->_mapper->setAreaDataMapper($mock);
    $areas = $this->_mapper->findAreaConhecimento();

    $this->assertEquals($returnValue, $areas);
  }

  public function testFindComponenteCurricularAnoEscolar()
  {
    // Valores de retorno
    $returnValue = new ComponenteCurricular_Model_Componente(
      array('id' => 1, 'nome' => 'Ci�ncias exatas', 'cargaHoraria' => 100)
    );

    $returnAnoEscolar = new ComponenteCurricular_Model_AnoEscolar(array(
      'componenteCurricular' => 1, 'anoEscolar' => 1, 'cargaHoraria' => 100
    ));

    // Mock para Ano Escolar
    $mock = $this->getCleanMock('ComponenteCurricular_Model_AnoEscolarDataMapper');
    $mock->expects($this->once())
         ->method('find')
         ->with(array(1, 1))
         ->will($this->returnValue($returnAnoEscolar));

    // Mock para Componente, exclui um m�todo de ser mocked
    $mapper = $this->setExcludedMethods(array('findComponenteCurricularAnoEscolar'))
                   ->getCleanMock('ComponenteCurricular_Model_ComponenteDataMapper');

    // O m�todo find do mapper ser� chamado uma vez
    $mapper->expects($this->once())
           ->method('find')
           ->with(1)
           ->will($this->returnValue($returnValue));

    // Como um mock n�o mant�m estado, for�a o retorno do mapper AnoEscolarDataMapper mocked
    $mapper->expects($this->once())
           ->method('getAnoEscolarDataMapper')
           ->will($this->returnValue($mock));

    // Chama o m�todo
    $componenteCurricular = $mapper->findComponenteCurricularAnoEscolar(1, 1);

    $this->assertEquals($returnValue, $componenteCurricular);
  }
}