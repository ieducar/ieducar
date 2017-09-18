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
 * @package     App_Date
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.2.0
 * @version     $Id$
 */

require_once 'App/Date/Utils.php';

/**
 * App_Date_UtilsTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     App_Date
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.2.0
 * @version     @@package_version@@
 */
class App_Date_UtilsTest extends UnitBaseTest
{
  public function testDatesYearAtLeast()
  {
    $dates = array(
      '01/01/2000',
      '01/02/2000'
    );

    try {
      App_Date_Utils::datesYearAtLeast($dates, 2001, 1);
      $this->fail('::datesYearAtLeast() deveria lan�ar App_Date_Exception.');
    }
    catch (App_Date_Exception $e) {
      $this->assertEquals(
        'Ao menos "1" das datas informadas deve ser do ano "2001". Datas: "01/01/2000", "01/02/2000".',
        $e->getMessage(),
        ''
      );
    }

    $this->assertTrue(
      App_Date_Utils::datesYearAtLeast($dates, 2000, 2),
      '::datesYearAtLeast() retorna "TRUE" quando uma das datas � do ano esperado.'
    );
  }
}