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
 * @package   App_Model
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Enum.php';

/**
 * App_Model_MatriculaSituacao class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class App_Model_MatriculaSituacao extends CoreExt_Enum
{
  const APROVADO            = 1;
  const REPROVADO           = 2;
  const EM_ANDAMENTO        = 3;
  const TRANSFERIDO         = 4;
  const RECLASSIFICADO      = 5;
  const ABANDONO            = 6;
  const EM_EXAME            = 7;
  const APROVADO_APOS_EXAME = 8;
  const RETIDO_FALTA        = 9;

  protected $_data = array(
    self::APROVADO            => 'Aprovado',
    self::REPROVADO           => 'Retido',
    self::EM_ANDAMENTO        => 'Em andamento',
    self::TRANSFERIDO         => 'Transferido',
    self::RECLASSIFICADO      => 'Reclassificado',
    self::ABANDONO            => 'Abandono',
    self::EM_EXAME            => 'Em exame',
    self::APROVADO_APOS_EXAME => 'Aprovado ap�s exame',
    self::RETIDO_FALTA        => 'Retido por falta'
  );

  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }
}