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
 * Avaliacao_Service_FaltaAlunoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaAlunoTest extends Avaliacao_Service_TestCommon
{
  public function testCriaNovaInstanciaDeFaltaAluno()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    $faltaSave  = clone $faltaAluno;
    $faltaSave->id = NULL;

    // Configura mock para Avaliacao_Model_FaltaAlunoDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaAlunoDataMapper');
    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('matricula' => $this->_getConfigOption('matricula', 'cod_matricula')))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($faltaSave)
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('findAll')
         ->with(array(), array('matricula' => $this->_getConfigOption('matricula', 'cod_matricula')))
         ->will($this->returnValue(array($faltaAluno)));

    $this->_setFaltaAlunoDataMapperMock($mock);

    $service = $this->_getServiceInstance();
  }
}