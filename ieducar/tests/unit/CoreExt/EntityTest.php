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
 * @package     CoreExt_Entity
 * @subpackage  UnitTests
 * @since       Arquivo dispon�vel desde a vers�o 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'CoreExt/_stub/Entity.php';
require_once 'CoreExt/_stub/EntityDataMapper.php';
require_once 'CoreExt/_stub/ParentEntity.php';
require_once 'CoreExt/_stub/ChildEntity.php';
require_once 'CoreExt/_stub/ChildEntityDataMapper.php';
require_once 'CoreExt/_stub/EnumSex.php';

/**
 * CoreExt_EntityTest class.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Entity
 * @subpackage  UnitTests
 * @since       Classe dispon�vel desde a vers�o 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_EntityTest extends UnitBaseTest
{
  public function testNovaInstanciaEMarcadaComoNew()
  {
    $entity = new CoreExt_EntityStub();
    $this->assertTrue($entity->isNew());
  }

  public function testSetaAtributoDoComponente()
  {
    $esperado = 'fooBar';
    $entity = new CoreExt_EntityStub();
    $entity->nome = $esperado;
    $this->assertEquals($esperado, $entity->nome);
  }

  public function testPrintDaInstancia()
  {
    $this->outputBuffer(TRUE);
    print new CoreExt_EntityStub();
    $output = $this->outputBuffer(FALSE);
    $this->assertEquals('CoreExt_EntityStub', $output);
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testSetarAtributoQueNaoExisteLancaExcecaoNaoVerificada()
  {
    $entity = new CoreExt_EntityStub();
    $entity->foo = 'bar';
  }

  public function testAtributoEstaSetado()
  {
    $entity = new CoreExt_EntityStub();
    $this->assertFalse(isset($entity->nome));
    $entity->nome = 'fooBar';
    $this->assertTrue(isset($entity->nome));
  }

  public function testAtributoFoiDestruido()
  {
    $entity = new CoreExt_EntityStub();
    $entity->nome = 'fooBar';
    $this->assertTrue(isset($entity->nome));
    unset($entity->nome);
    $this->assertFalse(isset($entity->nome));
  }

  public function testCampoIdentidadeEConsideradoNuloParaNaoNumericos()
  {
    $entity = new CoreExt_EntityStub();
    $entity->id = '';
    $this->assertNull($entity->id);
    $entity->id = '0';
    $this->assertNull($entity->id);
    $entity->id = 0;
    $this->assertNull($entity->id);
    $entity->id = NULL;
    $this->assertNull($entity->id);
  }

  public function testAutoConversaoParaBooleano()
  {
    $entity = new CoreExt_EntityStub();

    $entity->doador = 'f';
    $this->assertSame(FALSE, $entity->doador);
    $entity->doador = 0;
    $this->assertSame(FALSE, $entity->doador);

    $entity->doador = 't';
    $this->assertSame(TRUE, $entity->doador);
    $entity->doador = 1;
    $this->assertSame(TRUE, $entity->doador);
  }

  public function testNovaInstanciaENula()
  {
    $entity = new CoreExt_EntityStub();
    $this->assertTrue($entity->isNull());
  }

  public function testInstanciaComAlgumAtributoNaoNuloTornaseNaoNula()
  {
    $entity = new CoreExt_EntityStub();
    $entity->nome = 'fooBar';
    $this->assertFalse($entity->isNull());
  }

  /**
   * @group CoreExt_Locale
   */
  public function testAutoConversaoTiposNumericos()
  {
    $entity = new CoreExt_ChildEntityStub();
    $entity->peso = '12,5';
    $this->assertType('float', $entity->peso);
  }

  public function testSetaAtributosNaInstanciacao()
  {
    $data = array(
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => TRUE
    );
    $entity = new CoreExt_EntityStub($data);
    $this->assertEquals($data, $entity->toArray());
  }

  public function testSetaDataMapper()
  {
    $expected = new CoreExt_EntityDataMapperStub();

    $entity = new CoreExt_EntityStub();
    $entity->setDataMapper($expected);

    $this->assertSame($expected, $entity->getDataMapper());
  }

  /**
   * @group CoreExt_Locale
   */
  public function testInstanciaRetornaObjetoCoreextLocalePorPadrao()
  {
    $entity = new CoreExt_EntityStub();
    $this->assertType('CoreExt_Locale', $entity->getLocale());
  }

  /**
   * @group CoreExt_Validate_Validatable
   */
  public function testConfiguraValidadorParaAtributo()
  {
    $entity = new CoreExt_EntityStub();
    $entity->setValidator('estadoCivil', new CoreExt_Validate_String());
    $this->assertType('CoreExt_Validate_String', $entity->getValidator('estadoCivil'));
  }

  /**
   * @expectedException Exception
   * @group CoreExt_Validate_Validatable
   */
  public function testConfigurarValidadorParaAtributoInexistenteLancaExcecao()
  {
    $entity = new CoreExt_EntityStub();
    $entity->setValidator('fooAttr', new CoreExt_Validate_String());
  }

  /**
   * @group CoreExt_Validate_Validatable
   */
  public function testAtributosDaInstanciaSaoValidos()
  {
    $data = array(
      'nome' => 'fooBar'
    );
    $entity = new CoreExt_EntityStub($data);
    $this->assertTrue($entity->isValid('nome'), 'Failed asserting isValid() for "nome" attribute.');
    $this->assertTrue($entity->isValid(), 'Failed asserting isValid() class\' attributes.');
    $this->assertFalse($entity->hasError('nome'));
    $this->assertFalse($entity->hasErrors());
  }

  /**
   * @group CoreExt_Validate_Validatable
   */
  public function testValidacaoGeralRetornaFalseSeUmAtributoForInvalido()
  {
    $data = array(
      'nome' => ''
    );
    $entity = new CoreExt_EntityStub($data);
    $this->assertFalse($entity->isValid());
    $this->assertEquals('Obrigat�rio.', $entity->getError('nome'));
    $this->assertTrue($entity->hasErrors());
  }

  /**
   * Testa com inst�ncia de CoreExt_Entity que contenha refer�ncias DataMapper.
   * @group Overload
   * @group CoreExt_Validate_Validatable
   * @see   testSetterDeReferenciaOverloadAtribuiValorParaAReferenciaENoAtributoQuandoUmaInstanciaDeCoreExtEntityComFieldIdentityEPassada
   */
  public function testValidacaoSanitizaValorDeAtributoComReferenciasDataMapper()
  {
    $data = array(
      'id'   => 1,
      'nome' => ' FooBar Jr ',
      'sexo' => 1,
      'tipoSanguineo' => 1,
      'peso' => '12,5'
    );
    $child = new CoreExt_ChildEntityStub($data);

    $data = array(
      'nome' => ' FooBar ',
      'filho' => $child
    );
    $entity = new CoreExt_ParentEntityStub($data);

    // Atribui validadores para os atributos
    $entity->setValidator('nome', new CoreExt_Validate_String());
    $entity->setValidator('filho', new CoreExt_Validate_Choice(array('choices' => array(1, 2))));

    // Valida e verifica pelos valores
    $entity->isValid();
    $this->assertEquals(1, $entity->get('filho'));
    $this->assertEquals('FooBar', $entity->nome);
  }

  /**
   * Testa com inst�ncia de CoreExt_Entity que contenha refer�ncias Enum.
   * @group CoreExt_Validate_Validatable
   */
  public function testValidacaoSanitizaValorDeAtributoComReferenciasEnum()
  {
    $data = array(
      'nome' => 'fooBar ',
      'sexo' => 1,
      'tipoSanguineo' => 1,
      'peso' => '12,5'
    );
    $entity = new CoreExt_ChildEntityStub($data);

    // Atribui validadores para os atributos
    $entity->setValidator('nome', new CoreExt_Validate_String());
    $entity->setValidator('sexo', new CoreExt_Validate_Choice(array('choices' => array(1, 2))));
    $entity->setValidator('tipoSanguineo', new CoreExt_Validate_Choice(array('choices' => array(1, 2))));
    $entity->setValidator('peso', new CoreExt_Validate_Numeric());

    // Valida e verifica pelos valores
    $this->assertTrue($entity->isValid());
    $this->assertEquals('fooBar', $entity->nome);
    $this->assertType('float', $entity->peso);
  }

  /**
   * @group CoreExt_Validate_Validatable
   */
  public function testCriaUmValidadorDependendoDoValorDeUmAtribudoDaInstancia()
  {
    $entity = new CoreExt_EntityStub(array('nome' => 'fooBar', 'estadoCivil' => ''));

    // Validador condicional
    $validator = $entity->validateIfEquals(
      'nome', 'fooBar', 'CoreExt_Validate_String', array('min' => 1, 'max' => 5), array('required' => FALSE)
    );

    // Retornou o validador do If
    $entity->setValidator('estadoCivil', $validator);
    $this->assertFalse($entity->isValid('estadoCivil'), 'Falhou na asser��o de validateIfEquals() para caso If.');

    // Validador condicional
    $entity->nome = 'barFoo';
    $validator = $entity->validateIfEquals(
      'nome', 'fooBar', 'CoreExt_Validate_String', array('min' => 1, 'max' => 5), array('required' => FALSE)
    );

    // Retornou o validador do Else
    $entity->setValidator('estadoCivil', $validator);
    $this->assertTrue($entity->isValid('estadoCivil'), 'Falhou na asser��o de validateIfEquals() para caso Else.');
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   * @group CoreExt_Validate_Validatable
   */
  public function testMetodoDeCriacaoDeValidadorSensivelAoCasoLancaExcecaoQuandoClasseNaoESubclasseDeCoreextValidateAbstract()
  {
    $entity = new CoreExt_EntityStub();
    $entity->validateIfEquals(
      'nome', '', 'CoreExt_Validate_Abstract', array(), array()
    );
  }

  public function testTransformaEntityEmArrayDeValores()
  {
    $data = array(
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => TRUE
    );
    $entity = new CoreExt_EntityStub($data);
    $array = $entity->filterAttr('id');
    $this->assertEquals(array(1), array_values($array));
  }

  public function testTransformaEntityEmArrayAssociativo()
  {
    $data = array(
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => TRUE
    );
    $entity = new CoreExt_EntityStub($data);

    // Interface de inst�ncia
    $array = $entity->filterAttr('id', 'nome');
    $this->assertEquals(array(1 => 'fooBar'), $array);

    // Interface est�tica
    $array = CoreExt_Entity::entityFilterAttr($entity, 'id', 'nome');
    $this->assertEquals(array(1 => 'fooBar'), $array);
  }

  public function testTransformaEntitiesEmArrayAssociativo()
  {
    $data1 = $data2 = array(
      'id' => 1,
      'nome' => 'fooBar',
      'estadoCivil' => 'solteiro',
      'doador' => TRUE
    );

    $data2['id'] = 2;
    $data2['nome'] = 'barFoo';

    $entity1 = new CoreExt_EntityStub($data1);
    $entity2 = new CoreExt_EntityStub($data2);

    $entities = array($entity1, $entity2);

    $array = CoreExt_EntityStub::entityFilterAttr($entities, 'id', 'nome');

    $this->assertEquals(array(1 => 'fooBar', 2 => 'barFoo'), $array);
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testSetterDeReferenciaParaAtributoInexistenteLancaExcecao()
  {
    $expected = new CoreExt_ChildEntityStub();
    $expected->setReference('foo', array());
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testSetterDeReferenciaLancaExcecaoCasoArrayDeDefinicaoContenhaConfiguracaoInexistente()
  {
    $expected = new CoreExt_ChildEntityStub();
    $expected->setReference('nome', array('autoload' => TRUE));
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testSetterDeReferenciaLancaExcecaoParaClasseCoreextDataMapperInvalida()
  {
    $expected = new CoreExt_ChildEntityStub();
    $expected->setReference('nome', array('class' => new stdClass()));
  }

  public function testSetterDeReferenciaOverloadAtribuiValorParaAReferenciaQuandoEInteiro()
  {
    $entity = new CoreExt_ParentEntityStub(array('id' => 1, 'nome' => 'fooBar', 'filho' => 1));
    $this->assertEquals(1, $entity->get('filho'));
  }

  public function testSetterDeReferenciaOverloadAtribuiValorNullParaAReferenciaQuandoElaENullable()
  {
    $entity = new CoreExt_ParentEntityStub(array('id' => 1, 'nome' => 'fooBar', 'filho' => NULL));
    $this->assertNull($entity->filho);
    $this->assertNull($entity->get('filho'));
  }

  /**
   * @group Overload
   */
  public function testSetterDeReferenciaOverloadAtribuiValorParaAReferenciaENoAtributoQuandoUmaInstanciaDeCoreExtEntityComFieldIdentityEPassada()
  {
    $child = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'FooBar Jr'));
    $entity = new CoreExt_ParentEntityStub(array('id' => 1, 'nome' => 'fooBar', 'filho' => $child));
    $this->assertEquals($child, $entity->filho);
    $this->assertEquals(1, $entity->get('filho'));
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testSetterDeReferenciaOverloadLancaExcecaoParaAtribuicaoDeValorNullEmReferenciaNaoNullable()
  {
    $entity = new CoreExt_ChildEntityStub(array('id' => 1, 'sexo' => NULL));
  }

  public function testEntityRecuperadaPeloDataMapperEMarcadaComoVelha()
  {
    $entity = new CoreExt_EntityStub(array('nome' => 'fooBar'));
    $entity->markOld();

    $mapper = $this->getCleanMock('CoreExt_EntityDataMapperStub');
    $mapper->expects($this->once())
           ->method('find')
           ->with(1)
           ->will($this->returnValue($entity));

    $this->assertFalse($mapper->find(1)->isNew());
  }

  public function testReferenciaInstanciaAClasseAtribuidaAutomaticamente()
  {
    $parent = new CoreExt_ParentEntityStub(array('filho' => 1));
    $child  = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'fooBar', 'sexo' => 1));

    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->once())
                ->method('find')
                ->with(1)
                ->will($this->returnValue($child));

    $parent->setReferenceClass('filho', $filhoMapper);

    $enum = CoreExt_EnumSexStub::getInstance();
    $expected = $enum[CoreExt_EnumSexStub::MALE];

    $this->assertEquals($child, $parent->filho);
    $this->assertEquals($expected, $child->sexo);
  }

  public function testInstanciaDeClassNaoEspecificaEArmazenadaNoArrayStaticDaClasse()
  {
    CoreExt_EntityStub::addClassToStorage('StdClassExtStub',
      NULL, 'CoreExt/_stub/StdClassExt.php');
    $this->assertType('StdClassExtStub', CoreExt_EntityStub::getClassFromStorage('StdClassExtStub'));
  }

  public function testInstanciaDeClassNaoEspecificaArmazenaInstanciaEspecifica()
  {
    $obj = new stdClass();
    $oid = spl_object_hash($obj);
    CoreExt_EntityStub::addClassToStorage('stdClass', $obj);
    $this->assertType('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
    $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
  }

  public function testInstanciaDeClassNaoEspecificaPodeSerRepostaPorNovaInstancia()
  {
    // S� para facilitar.
    for ($i = 0; $i <= 1; $i++) {
      $obj = new stdClass();
      $obj->i = $i;
      $oid = spl_object_hash($obj);
      CoreExt_EntityStub::addClassToStorage('stdClass', $obj);
      $this->assertType('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
      $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
    }
    CoreExt_EntityStub::addClassToStorage('stdClass');
    $this->assertType('stdClass', CoreExt_EntityStub::getClassFromStorage('stdClass'));
    $this->assertEquals($oid, spl_object_hash(CoreExt_EntityStub::getClassFromStorage('stdClass')));
  }

  public function testInstanciaDeClassESobresritaPorInstanciasSticky()
  {
    $oid1 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass(), NULL, TRUE);
    $oid1 = spl_object_hash($oid1);

    $oid2 = CoreExt_EntityStub::addClassToStorage('stdClass', new stdClass(), NULL, TRUE);
    $oid2 = spl_object_hash($oid2);

    $this->assertNotEquals($oid2, $oid1);
  }

  public function testInstanciaDeClassNaoEspecificaRetornaNullQuandoNaoEncontraUmaInstanciaDaClasse()
  {
    $this->assertNull(CoreExt_EntityStub::getClassFromStorage('fooBar'));
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testInstanciaDeClassNaoEspecificaLancaExcecaoQuandoInstanciaPassadaNaoTemAMesmaAssinaturaDoNomeDeClasseInformado()
  {
    CoreExt_EntityStub::addClassToStorage('fooBar', new stdClass());
  }

  public function testLazyLoadParaReferenciaNumericaAInstanciaEntity()
  {
    $data = array(
      'nome' => 'fooBar',
      'filho' => 1
    );

    $expected = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'barFoo'));

    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->once())
                ->method('find')
                ->with(1)
                ->will($this->returnValue($expected));

    $parent = new CoreExt_ParentEntityStub($data);
    $parent->setReference('filho', array('value' => 1, 'class' => $filhoMapper));

    $this->assertType('CoreExt_ChildEntityStub', $parent->filho);
    $this->assertEquals(1, $parent->filho->id);
  }

  /**
   * @group LazyLoad
   * @group CoreExt_Validate_Validatable
   */
  public function testLazyLoadNaoCarregaInstanciaEntityParaValidacao()
  {
    $data = array(
      'nome' => 'fooBar',
    );

    $expected = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'barFoo'));

    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->never())
                ->method('find');

    $parent = new CoreExt_ParentEntityStub($data);
    $parent->setReference('filho', array('value' => 1, 'class' => $filhoMapper));

    $this->assertTrue($parent->isValid());
  }

  public function testLazyLoadParaReferenciaObjetoAInstanciaEntityNaoCausaLazyLoad()
  {
    $expected = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'barFoo'));

    $data = array(
      'id'    => 1,
      'nome'  => 'fooBar',
      'filho' => $expected
    );

    // O m�todo "find" n�o deve ser chamado pois "child" j� existe como atributo
    // de "parent"
    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->never())
                ->method('find');

    // Configurando com mock, para ter certeza que n�o ir� tentar carregar
    // "child"
    $parent = new CoreExt_ParentEntityStub($data);
    $parent->setReference('filho', array('value' => 1, 'class' => $filhoMapper));

    $this->assertType('CoreExt_ChildEntityStub', $parent->filho);
    $this->assertEquals(1, $parent->filho->id);
    $this->assertEquals('barFoo', $parent->filho->nome);

    // toArray() causa chamadas lazy load. Garantindo que n�o ocorra.
    $this->assertEquals($data, $parent->toArray());

    // toDataArray() retorna sempre a refer�ncia num�rica.
    $data['filho'] = 1;
    $this->assertEquals($data, $parent->toDataArray());
  }

  public function testLazyLoadNaoERealizadoQuandoReferenciaIgualA0OuNulo()
  {
    $expected = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'barFoo'));

    $data = array(
      'id'    => 1,
      'nome'  => 'fooBar'
    );

    // O m�todo "find" n�o deve ser chamado pois "child" j� existe como atributo
    // de "parent"
    $filhoMapper = $this->getCleanMock('CoreExt_ChildEntityDataMapperStub');
    $filhoMapper->expects($this->never())
                ->method('find');

    // Configurando com mock, para ter certeza que n�o ir� tentar carregar
    // "child"
    $parent = new CoreExt_ParentEntityStub($data);

    $this->assertNull($parent->filho);
  }

  /**
   * @expectedException CoreExt_Exception_InvalidArgumentException
   */
  public function testLazyLoadParametroQueEReferenciaLancaExcecaoQuandoNaoEDoTipoIntegerOuCoreextEntity()
  {
    $expected = new stdClass();

    $data = array(
      'id'    => 1,
      'nome'  => 'fooBar',
      'filho' => $expected
    );

    $parent = new CoreExt_ParentEntityStub($data);
  }

  public function testLazyLoadDeReferenciaAUmTipoCoreextEnum()
  {
    $enum = CoreExt_EnumSexStub::getInstance();

    $child1 = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'barFoo'));
    $child2 = new CoreExt_ChildEntityStub(array('id' => 1, 'nome' => 'barFoo'));

    $child1->setReference('sexo', array('value' => 1, 'class' => $enum));
    $this->assertEquals('masculino', $child1->sexo);

    $child2->setReference('sexo', array('value' => 2, 'class' => $enum));
    $this->assertEquals('feminino', $child2->sexo);
  }
}