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
 * @package     App_Model
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id: /ieducar/branches/1.1.0-avaliacao/ieducar/tests/unit/App/Model/IedFinderTest.php 1046 2009-12-21T17:30:49.663282Z eriksencosta  $
 */

require_once 'App/Model/Matricula.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';

/**
 * App_Model_MatriculaTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     App_Model
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class App_Model_MatriculaTest extends UnitBaseTest
{
  public function testAtualizaMatricula()
  {
    $matricula = $this->getCleanMock('clsPmieducarMatricula');
    $matricula->expects($this->once())
              ->method('edita')
              ->will($this->returnValue(TRUE));

    // Guarda no reposit�rio est�tico de classes
    CoreExt_Entity::addClassToStorage('clsPmieducarMatricula', $matricula,
      NULL, TRUE);

    App_Model_Matricula::atualizaMatricula(1, 1, TRUE);
  }
}