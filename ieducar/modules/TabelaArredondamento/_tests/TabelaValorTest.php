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
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';
require_once 'TabelaArredondamento/Model/Tabela.php';
require_once 'RegraAvaliacao/Model/Nota/TipoValor.php';

/**
 * TabelaValorTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class TabelaValorTest extends UnitBaseTest
{
  protected $_entity = NULL;

  protected function setUp()
  {
    $this->_entity = new TabelaArredondamento_Model_TabelaValor();
  }

  public function testGetterDeDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('TabelaArredondamento_Model_TabelaValorDataMapper', $this->_entity->getDataMapper());
  }

  public function testEntityValidators()
  {
    $tabelaNumerica = new TabelaArredondamento_Model_Tabela(array(
      'nome' => 'foo',
      'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
    ));

    $tabelaConceitual = new TabelaArredondamento_Model_Tabela(array(
      'nome' => 'bar',
      'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL
    ));

    // Usa a inst�ncia rec�m criaca
    $this->_entity->tabelaArredondamento = $tabelaNumerica;

    // Asser��o para nota num�rica
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_Numeric', $validators['nome']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMinimo']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMaximo']);
    $this->assertTrue(!isset($validators['descricao']));

    // Asser��o para nota conceitual
    $this->_entity->tabelaArredondamento = $tabelaConceitual;
    $validators = $this->_entity->getDefaultValidatorCollection();
    $this->assertType('CoreExt_Validate_String',  $validators['nome']);
    $this->assertType('CoreExt_Validate_String',  $validators['descricao']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMinimo']);
    $this->assertType('CoreExt_Validate_Numeric', $validators['valorMaximo']);
  }
}