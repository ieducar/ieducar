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

require_once 'Core/Controller/Page/ViewController.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

/**
 * ViewController class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     RegraAvaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ViewController extends Core_Controller_Page_ViewController
{
  protected $_dataMapper = 'RegraAvaliacao_Model_RegraDataMapper';
  protected $_titulo     = 'Detalhes da regra de avaliação';
  protected $_processoAp = 947;
  protected $_tableMap   = array(
    'Nome' => 'nome',
    'Sistema de nota' => 'tipoNota',
    'Tabela de arredondamento' => 'tabelaArredondamento',
    'Progressão' => 'tipoProgressao',
    'Média para promoção' => 'media',
    'Média exame para promoção' => 'mediaRecuperacao',
    'Fórmula de cálculo de média final' => 'formulaMedia',
    'Fórmula de cálculo de recuperação' => 'formulaRecuperacao',
    'Porcentagem presença' => 'porcentagemPresenca',
    'Parecer descritivo' => 'parecerDescritivo',
    'Tipo de presença' => 'tipoPresenca'
  );
  protected function _preRender(){

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""                                  => "Detalhe da regra de avalia&ccedil;&otilde;o"
    ));
    $this->enviaLocalizacao($localizacao->montar());
  }
}
