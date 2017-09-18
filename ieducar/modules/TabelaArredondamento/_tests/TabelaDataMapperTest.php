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

require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';
require_once 'TabelaArredondamento/Model/TabelaValor.php';
require_once 'TabelaArredondamento/Model/TabelaValorDataMapper.php';

/**
 * TabelaDataMapperTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class TabelaDataMapperTest extends UnitBaseTest
{
  protected $_mapper = NULL;

  protected function setUp()
  {
    $this->_mapper = new TabelaArredondamento_Model_TabelaDataMapper();
  }

  public function testGetterDeValorDataMapperInstanciaObjetoPorPadraoSeNenhumForConfigurado()
  {
    $this->assertType('TabelaArredondamento_Model_TabelaValorDataMapper', $this->_mapper->getTabelaValorDataMapper());
  }

  public function testFinderTabelaValor()
  {
    // Inst�ncia de Tabela
    $instance = new TabelaArredondamento_Model_Tabela(array(
      'id' => 1, 'instituicao' => 1, 'tipoNota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA
    ));

    // Prepara dados para o mock
    $data = array(
      'tabelaArredondamento' => 1,
      'nome'                 => NULL,
      'descricao'            => NULL,
      'valorMinimo'          => 0,
      'valorMaximo'          => 1
    );

    // Dados de retorno, popula para mock
    $returnValue = array();
    for ($i = 1; $i < 4; $i++) {
      $data['nome']      = $i;
      $data['descricao'] = '';
      $returnValue[] = new TabelaArredondamento_Model_TabelaValor($data);
      $data['valorMinimo'] = $data['valorMinimo'] + 1;
      $data['valorMaximo'] = $data['valorMaximo'] + 1;
    }

    // Expectativa do mock
    $mock = $this->getCleanMock('TabelaArredondamento_Model_TabelaValorDataMapper');
    $mock->expects($this->once())
         ->method('findAll')
         ->with(array(), array('tabelaArredondamento' => 1))
         ->will($this->returnValue($returnValue));

     // Chama o m�todo finder
     $this->_mapper->setTabelaValorDataMapper($mock);
     $returned = $this->_mapper->findTabelaValor($instance);

     // Asser��es
     $this->assertEquals($returnValue[0], $returned[0]);
     $this->assertEquals($returnValue[1], $returned[1]);
     $this->assertEquals($returnValue[2], $returned[2]);
  }
}