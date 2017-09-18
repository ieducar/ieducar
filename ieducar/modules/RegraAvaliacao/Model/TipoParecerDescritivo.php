<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Enum.php';

/**
 * RegraAvaliacao_Model_TipoParecerDescritivo class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class RegraAvaliacao_Model_TipoParecerDescritivo extends CoreExt_Enum
{
  const NENHUM           = 0;
  const ETAPA_DESCRITOR  = 1;
  const ETAPA_COMPONENTE = 2;
  const ETAPA_GERAL      = 3;
  const ANUAL_DESCRITOR  = 4;
  const ANUAL_COMPONENTE = 5;
  const ANUAL_GERAL      = 6;

  protected $_data = array(
    self::NENHUM           => 'Não usar parecer descritivo',
    self::ETAPA_COMPONENTE => 'Um parecer por etapa e por componente curricular',
    self::ETAPA_GERAL      => 'Um parecer por etapa, geral',
    self::ANUAL_COMPONENTE => 'Uma parecer por ano letivo e por componente curricular',
    self::ANUAL_GERAL      => 'Um parecer por ano letivo, geral',
  );

  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }
}
