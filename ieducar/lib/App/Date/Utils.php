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
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Date
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'App/Date/Exception.php';

/**
 * App_Date_Utils class.
 *
 * Possui m�todos
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Date
 * @since     Classe dispon�vel desde a vers�o 1.2.0
 * @version   @@package_version@@
 */
class App_Date_Utils
{
  /**
   * Retorna o ano de uma string nos formatos dd/mm/yyyy e dd/mm/yyyy hh:ii:ss.
   * @param string $date
   * @param int
   */
  public static function getYear($date)
  {
    $parts = explode('/', $date);
    $year  = explode(' ', $parts[2]);

    if (is_array($year)) {
      $year = $year[0];
    }

    return (int) $year;
  }

  /**
   * Verifica se ao menos uma das datas de um array � do ano especificado.
   * @param   array  $dates Datas nos formatos dd/mm/yyyy [hh:ii:ss].
   * @param   int    $year  Ano esperado.
   * @param   int    $at    Quantidade m�nima de datas esperadas no ano $year.
   * @return  bool   TRUE se ao menos uma das datas estiver no ano esperado.
   * @throws  App_Date_Exception
   */
  public static function datesYearAtLeast(array $dates, $year, $at = 1)
  {
    $matches = 0;

    foreach ($dates as $date) {
      $dateYear = self::getYear($date);
      if ($year == $dateYear) {
        $matches++;
      }
    }

    if ($matches >= $at) {
      return TRUE;
    }

    throw new App_Date_Exception(sprintf(
      'Ao menos "%d" das datas informadas deve ser do ano "%d". Datas: "%s".',
      $at, $year, implode('", "', $dates)
    ));
  }
}