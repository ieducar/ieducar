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
 * @package     AreaConhecimento
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'AreaConhecimento/Model/Area.php';
require_once 'AreaConhecimento/Model/AreaDataMapper.php';
require_once 'include/pmieducar/clsPmieducarInstituicao.inc.php';

/**
 * ComponenteCurricular_AreaTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     AreaConhecimento
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class AreaConhecimento_AreaTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new AreaConhecimento_Model_Area();
  }

  public function testInstanciaDeAreaRetornaOValorDeNOmeEmContextoDeImpressao()
  {
    $this->_entity->nome = 'Ci�ncias exatas';
    $this->assertEquals('Ci�ncias exatas', $this->_entity->__toString());
  }

  public function testEntityValidators()
  {
    // Valores de retorno
    $returnValue = array(array('cod_instituicao' => 1, 'nm_instituicao' => 'Institui��o'));

    // Mock para institui��o
    $mock = $this->getCleanMock('clsPmieducarInstituicao');
    $mock->expects($this->once())
         ->method('lista')
         ->will($this->returnValue($returnValue));

    // Adiciona o mock no reposit�rio de classe est�tico
    $this->_entity->addClassToStorage('clsPmieducarInstituicao', $mock);

    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_Choice', $validators['instituicao']);
    $this->assertType('CoreExt_Validate_String', $validators['nome']);
  }
}