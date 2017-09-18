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
 * Avaliacao_Service_FaltaSituacaoCommon class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_FaltaSituacaoCommon extends Avaliacao_Service_TestCommon
{
  protected function _setUpFaltaAbstractDataMapperMock(
    Avaliacao_Model_FaltaAluno $faltaAluno, array $faltas)
  {
    // Configura mock para notas
    $mock = $this->getCleanMock('Avaliacao_Model_FaltaAbstractDataMapper');

    $mock->expects($this->any())
         ->method('findAll')
         ->with(array(), array('faltaAluno' => $faltaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($faltas));

    $this->_setFaltaAbstractDataMapperMock($mock);
  }

  protected function _getExpectedSituacaoFaltas()
  {
    $faltaAluno = $this->_getConfigOption('faltaAluno', 'instance');

    // Valores retornados pelas inst�ncias de classes legadas
    $cursoHoraFalta    = $this->_getConfigOption('curso', 'hora_falta');
    $serieCargaHoraria = $this->_getConfigOption('serie', 'carga_horaria');

    // Porcentagem configurada na regra
    $porcentagemPresenca = $this->_getRegraOption('porcentagemPresenca');

    $expected = new stdClass();
    $expected->situacao                 = 0;
    $expected->tipoFalta                = 0;
    $expected->cargaHoraria             = 0;
    $expected->cursoHoraFalta           = 0;
    $expected->totalFaltas              = 0;
    $expected->horasFaltas              = 0;
    $expected->porcentagemFalta         = 0;
    $expected->porcentagemPresenca      = 0;
    $expected->porcentagemPresencaRegra = 0;
    $expected->componentesCurriculares  = array();

    $expected->tipoFalta                = $faltaAluno->get('tipoFalta');
    $expected->cursoHoraFalta           = $cursoHoraFalta;
    $expected->porcentagemPresencaRegra = $porcentagemPresenca;
    $expected->cargaHoraria             = $serieCargaHoraria;

    return $expected;
  }
}