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
 * Avaliacao_Service_SituacaoTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class Avaliacao_Service_SituacaoTest extends Avaliacao_Service_TestCommon
{
  public function testSituacaoAluno()
  {
    $notaSituacoes = array(
      1 => App_Model_MatriculaSituacao::APROVADO,
      2 => App_Model_MatriculaSituacao::APROVADO_APOS_EXAME,
      3 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
      4 => App_Model_MatriculaSituacao::EM_EXAME,
      5 => App_Model_MatriculaSituacao::REPROVADO
    );

    $faltaSituacoes = array(
      1 => App_Model_MatriculaSituacao::EM_ANDAMENTO,
      2 => App_Model_MatriculaSituacao::APROVADO,
      3 => App_Model_MatriculaSituacao::REPROVADO
    );

    // Possibilidades
    $expected = array(
      1 => array(
        1 => array(FALSE, TRUE, FALSE, FALSE),
        2 => array(TRUE, FALSE, FALSE, FALSE),
        3 => array(FALSE, FALSE, TRUE, FALSE)
      ),
      2 => array(
        1 => array(FALSE, TRUE, FALSE, TRUE),
        2 => array(TRUE, FALSE, FALSE, TRUE),
        3 => array(FALSE, FALSE, TRUE, TRUE)
      ),
      3 => array(
        1 => array(FALSE, TRUE, FALSE, FALSE),
        2 => array(FALSE, TRUE, FALSE, FALSE),
        3 => array(FALSE, TRUE, TRUE, FALSE)
      ),
      4 => array(
        1 => array(FALSE, TRUE, FALSE, TRUE),
        2 => array(FALSE, TRUE, FALSE, TRUE),
        3 => array(FALSE, TRUE, TRUE, TRUE)
      ),
      5 => array(
        1 => array(FALSE, TRUE, FALSE, FALSE),
        2 => array(FALSE, FALSE, FALSE, FALSE),
        3 => array(FALSE, FALSE, TRUE, FALSE)
      )
    );

    foreach ($notaSituacoes as $i => $notaSituacao) {
      $nota = new stdClass();
      $nota->situacao = $notaSituacao;

      foreach ($faltaSituacoes as $ii => $faltaSituacao) {
        $service = $this->setExcludedMethods(array('getSituacaoAluno'))
                        ->getCleanMock('Avaliacao_Service_Boletim');

        $falta = new stdClass();
        $falta->situacao = $faltaSituacao;

        $service->expects($this->once())
                ->method('getSituacaoComponentesCurriculares')
                ->will($this->returnValue($nota));

        $service->expects($this->once())
                ->method('getSituacaoFaltas')
                ->will($this->returnValue($falta));

        // Testa
        $situacao = $service->getSituacaoAluno();

        $this->assertEquals($expected[$i][$ii][0], $situacao->aprovado, "Aprovado, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][1], $situacao->andamento, "Andamento, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][2], $situacao->retidoFalta, "Retido por falta, caso $i - $ii");
        $this->assertEquals($expected[$i][$ii][3], $situacao->recuperacao, "Recupera��o, caso $i - $ii");
      }
    }
  }
}