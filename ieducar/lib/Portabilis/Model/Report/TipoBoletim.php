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
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Report
 * @subpackage  Model
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Enum.php';

/**
 * RegraAvaliacao_Model_TipoParecerDescritivo class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Report
 * @subpackage  Model
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class Portabilis_Model_Report_TipoBoletim extends CoreExt_Enum
{
  const BIMESTRAL                     = 1;
  const TRIMESTRAL                    = 2;
  const TRIMESTRAL_CONCEITUAL         = 3;
  const SEMESTRAL                     = 4;
  const SEMESTRAL_CONCEITUAL          = 5;
  const SEMESTRAL_EDUCACAO_INFANTIL   = 6;
  const PARECER_DESCRITIVO_COMPONENTE = 7;
  const PARECER_DESCRITIVO_GERAL      = 8;

  // Em conversa com a Portabilis, eles n�o v�o disponibilizar os outros relat�rios de boletim.
  // Portanto, at� fazermos os nossos, s� tem bimestral.
  protected $_data = array(
    self::BIMESTRAL                     => 'Bimestral' //,
    //self::TRIMESTRAL                    => 'Trimestral',
    //self::TRIMESTRAL_CONCEITUAL         => 'Trimestral conceitual',
    //self::SEMESTRAL                     => 'Semestral',
    //self::SEMESTRAL_CONCEITUAL          => 'Semestral conceitual',
    //self::SEMESTRAL_EDUCACAO_INFANTIL   => 'Semestral educa&ccedil;&atilde;o infantil',
    //self::PARECER_DESCRITIVO_COMPONENTE => 'Parecer descritivo por componente curricular',
    //self::PARECER_DESCRITIVO_GERAL      => 'Parecer descritivo geral'
  );

  public static function getInstance() 
  {
    return self::_getInstance(__CLASS__);
  }
}