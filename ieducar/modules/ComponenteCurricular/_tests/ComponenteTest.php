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

require_once 'ComponenteCurricular/Model/Componente.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';
require_once 'include/pmieducar/clsPmieducarSerie.inc.php';

/**
 * ComponenteTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     ComponenteCurricular
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class ComponenteTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new ComponenteCurricular_Model_Componente();
  }

  public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('ComponenteCurricular_Model_ComponenteDataMApper', $this->_entity->getDataMapper());
  }

  public function testEntityValidators()
  {
    // Valores de retorno
    $returnValue = array(array('cod_instituicao' => 1, 'nm_instituicao' => 'Institui��o'));
    $areaReturnValue = array(new AreaConhecimento_Model_Area(array('id' => 1, 'nome' => 'Ci�ncias exatas')));

    // Mock para institui��o
    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->once())
         ->method('lista')
         ->will($this->returnValue($returnValue));

    // Mock para �rea de conhecimento
    $areaConhecimentoMock = $this->getCleanMock('AreaConhecimento_Model_AreaDataMapper');
    $areaConhecimentoMock->expects($this->once())
                         ->method('findAll')
                         ->will($this->returnValue($areaReturnValue));

    // Registra a inst�ncia no reposit�rio de classes de CoreExt_Entity
    $instance = ComponenteCurricular_Model_Componente::addClassToStorage(
      'clsPmieducarInstituicao', $mock);

    // Substitui o data mapper padr�o pelo mock
    $this->_entity->getDataMapper()->setAreaDataMapper($areaConhecimentoMock);

    // Recupera os objetos CoreExt_Validate
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_Choice', $validators['instituicao']);
    $this->assertType('CoreExt_Validate_String', $validators['nome']);
    $this->assertType('CoreExt_Validate_String', $validators['abreviatura']);
    $this->assertType('CoreExt_Validate_Choice', $validators['tipo_base']);
    $this->assertType('CoreExt_Validate_Choice', $validators['area_conhecimento']);
  }
}