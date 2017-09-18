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
 * Avaliacao_Service_NotaRecuperacaoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_NotaRecuperacaoTest extends Avaliacao_Service_TestCommon
{
  public function testSalvarNotasDeUmComponenteCurricularNoBoletimEmRecuperacao()
  {
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = array(
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 5,
        'etapa'                => 1
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 5,
        'etapa'                => 2
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 6,
        'etapa'                => 3
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 6,
        'etapa'                => 4
      )),
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 6,
        'etapa'                => 'Rc'
      )),
    );

    $media = new Avaliacao_Model_NotaComponenteMedia(array(
      'notaAluno'            => $notaAluno->id,
      'componenteCurricular' => 1,
      'media'                => 5.7,
      'mediaArredondada'     => 5,
      'etapa'                => 'Rc'
    ));

    $media->markOld();

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($notas[0])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($notas[1])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(3))
         ->method('save')
         ->with($notas[2])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(4))
         ->method('save')
         ->with($notas[3])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(5))
         ->method('save')
         ->with($notas[4])
         ->will($this->returnValue(TRUE));

    $mock->expects($this->at(6))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($notas));

    $this->_setNotaComponenteDataMapperMock($mock);

    // Configura mock para Avaliacao_Model_NotaComponenteMediaDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteMediaDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(1))
         ->method('find')
         ->with(array($notaAluno->id, $this->_getConfigOption('matricula', 'cod_matricula')))
         ->will($this->returnValue(array()));

    $mock->expects($this->at(2))
         ->method('save')
         ->with($media)
         ->will($this->returnValue(TRUE));

    $this->_setNotaComponenteMediaDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addNotas($notas);
    $service->saveNotas();

    $notasSalvas = $service->getNotas();

    $etapas = array_merge(
      range(1, count($this->_getConfigOptions('anoLetivoModulo'))),
      array('Rc')
    );

    foreach ($notasSalvas as $notaSalva) {
      $key = array_search($notaSalva->etapa, $etapas, FALSE);
      $this->assertTrue($key !== FALSE);
      unset($etapas[$key]);
    }
  }

  public function testSalvarNotasDeUmComponenteCurricularNoBoletimEmRecuperacaoComNotasLancadas()
  {
    $notaAluno = $this->_getConfigOption('notaAluno', 'instance');

    $notas = array(
      new Avaliacao_Model_NotaComponente(array(
        'componenteCurricular' => 1,
        'nota'                 => 5,
      ))
    );

    $notasPersistidas = array(
      new Avaliacao_Model_NotaComponente(array(
        'id'                   => 1,
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'nota'                 => 6,
        'notaArredondada'      => 6,
        'etapa'                => 1
      )),
      new Avaliacao_Model_NotaComponente(array(
        'id'                   => 2,
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'nota'                 => 6,
        'notaArredondada'      => 6,
        'etapa'                => 2
      )),
      new Avaliacao_Model_NotaComponente(array(
        'id'                   => 3,
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'nota'                 => 6,
        'notaArredondada'      => 6,
        'etapa'                => 3
      )),
      new Avaliacao_Model_NotaComponente(array(
        'id'                   => 4,
        'notaAluno'            => $notaAluno->id,
        'componenteCurricular' => 1,
        'nota'                 => 6,
        'notaArredondada'      => 6,
        'etapa'                => 4
      ))
    );

    // Configura mock para Avaliacao_Model_NotaComponenteDataMapper
    $mock = $this->getCleanMock('Avaliacao_Model_NotaComponenteDataMapper');

    $mock->expects($this->at(0))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($notasPersistidas));

    $mock->expects($this->at(1))
         ->method('save')
         ->with($notas[0])
         ->will($this->returnValue(TRUE));

    $notasSalvas = array_merge($notasPersistidas, $notas);

    $mock->expects($this->at(2))
         ->method('findAll')
         ->with(array(), array('notaAluno' => $notaAluno->id), array('etapa' => 'ASC'))
         ->will($this->returnValue($notasSalvas));

    $this->_setNotaComponenteDataMapperMock($mock);

    $service = $this->_getServiceInstance();
    $service->addNotas($notas);
    $service->saveNotas();

    $etapas = array_merge(
      range(1, count($this->_getConfigOptions('anoLetivoModulo'))),
      array('Rc')
    );

    foreach ($notasSalvas as $notaSalva) {
      $key = array_search($notaSalva->etapa, $etapas, FALSE);
      $this->assertTrue($key !== FALSE);
      unset($etapas[$key]);
    }
  }
}