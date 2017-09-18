<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   07/2013
 * @version   $Id$
 */

require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesVeiculo.inc.php';

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class VeiculoController extends ApiCoreController
{
  protected $_processoAp        = 578; //verificar
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA; // verificar

  protected function loadNomeEmpresa($id) {
    $sql  = "select nome from cadastro.pessoa, modules.empresa_transporte_escolar emp where idpes = emp.ref_idpes and emp.cod_empresa_transporte_escolar = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }  

  protected function loadNomeMotorista($id) {
    $sql  = "select nome from cadastro.pessoa, modules.motorista where idpes = ref_idpes and cod_motorista = $1";
    $nome = $this->fetchPreparedQuery($sql, $id, false, 'first-field');

    return $this->toUtf8($nome, array('transform' => true));
  }    

  protected function createOrUpdateVeiculo($id = null){
    

    $veiculo                                     = new clsModulesVeiculo();
    $veiculo->cod_veiculo = $id;
    // após cadastro não muda mais id pessoa

    $veiculo->descricao                          = Portabilis_String_Utils::toLatin1($this->getRequest()->descricao);
    $veiculo->placa                              = Portabilis_String_Utils::toLatin1($this->getRequest()->placa);
    $veiculo->renavam                            = $this->getRequest()->renavam;
    $veiculo->chassi                             = $this->getRequest()->chassi;
    $veiculo->marca                              = Portabilis_String_Utils::toLatin1($this->getRequest()->marca);
    $veiculo->passageiros                        = $this->getRequest()->passageiros; 
    $veiculo->ano_fabricacao                     = $this->getRequest()->ano_fabricacao;
    $veiculo->ano_modelo                         = $this->getRequest()->ano_modelo; 
    $veiculo->malha                              = $this->getRequest()->malha; 
    $veiculo->ref_cod_tipo_veiculo               = $this->getRequest()->tipo; 
    $veiculo->exclusivo_transporte_escolar       = ($this->getRequest()->exclusivo_transporte_escolar == 'on' ? 'S' : 'N'); 
    $veiculo->adaptado_necessidades_especiais    = ($this->getRequest()->adaptado_necessidades_especiais == 'on' ? 'S' : 'N'); 
    $veiculo->ativo                              = ($this->getRequest()->ativo == 'on' ? 'S' : 'N');   
    $veiculo->descricao_inativo                  = Portabilis_String_Utils::toLatin1($this->getRequest()->descricao_inativo);
    $veiculo->ref_cod_empresa_transporte_escolar = $this->getRequest()->empresa_id; 
    $veiculo->ref_cod_motorista                  = $this->getRequest()->motorista_id; 
    $veiculo->observacao                         = Portabilis_String_Utils::toLatin1($this->getRequest()->observacao);

    return (is_null($id) ? $veiculo->cadastra() : $veiculo->edita());
  }

  protected function sqlsForNumericSearch() {

    $sqls[] = "select distinct cod_veiculo as id, (descricao || ', Placa: ' || placa) as name from
                 modules.veiculo where (cod_veiculo like $1||'%') OR (lower(to_ascii(placa)) like '%'||lower(to_ascii($1))||'%')";

    return $sqls;
  }


  protected function sqlsForStringSearch() {

    $sqls[] = "select distinct cod_veiculo as id, (descricao || ', Placa: ' || placa ) as name from
                 modules.veiculo where (lower(to_ascii(descricao)) like '%'||lower(to_ascii($1))||'%') OR (lower(to_ascii(placa)) like '%'||lower(to_ascii($1))||'%')";

    return $sqls;
  }

  protected function validateSizeOfObservacao(){
    if (strlen($this->getRequest()->observacao)<=255)
      return true;
    else{
      $this->messenger->append('O campo Observações não pode ter mais que 255 caracteres.');
      return false;
    }

  }

  protected function validateSizeOfDescricaoInativo(){
    if (strlen($this->getRequest()->descricao_inativo)<=255)
      return true;
    else{
      $this->messenger->append('O campo Descrição de inatividade não pode ter mais que 255 caracteres.');
      return false;
    }

  }      

  protected function validateIfVeiculoIsNotInUse(){

      $it = new clsModulesItinerarioTransporteEscolar();
      $lista = $it->lista(null,null,null,$this->getRequest()->id);
      if(is_array($lista) && count($lista)>0){
        $this->messenger->append('Não é possível excluir um veículo que está vinculada a um itinerário.',
                                 'error', false, 'error');
        return false;
      }else{
        return true;
      }
  }  

  protected function get() {
    
      $id                                      = $this->getRequest()->id;
      $veiculo                                 = new clsModulesVeiculo();
      $veiculo->cod_veiculo                    = $id;
      $veiculo                                 = $veiculo->detalhe();

      $attrs  = array(
        'cod_veiculo'     => 'id',
        'descricao'         => 'descricao',
        'placa'          => 'placa',
        'renavam' => 'renavam',
        'chassi'               => 'chassi',
        'marca'        => 'marca',
        'ano_fabricacao'    => 'ano_fabricacao',
        'ano_modelo'    => 'ano_modelo',
        'passageiros'   =>  'passageiros',
        'malha'   =>  'malha',
        'ref_cod_tipo_veiculo'   =>  'tipo',
        'exclusivo_transporte_escolar'   =>  'exclusivo_transporte_escolar',
        'adaptado_necessidades_especiais'   =>  'adaptado_necessidades_especiais',
        'ativo'   =>  'ativo',
        'descricao_inativo'   =>  'descricao_inativo',
        'ref_cod_empresa_transporte_escolar'   =>  'empresa',
        'ref_cod_motorista'   =>  'motorista',
        'observacao'  =>  'observacao'
      );

      $veiculo = Portabilis_Array_Utils::filter($veiculo, $attrs);

      $veiculo['empresaNome']                   = Portabilis_String_Utils::toUtf8($this->loadNomeEmpresa($veiculo['empresa']));

      $veiculo['motoristaNome']                 = Portabilis_String_Utils::toUtf8($this->loadNomeMotorista($veiculo['motorista']));
      $veiculo['descricao']                     = Portabilis_String_Utils::toUtf8($veiculo['descricao']);
      $veiculo['marca']                         = Portabilis_String_Utils::toUtf8($veiculo['marca']);
      $veiculo['placa']                         = Portabilis_String_Utils::toUtf8($veiculo['placa']);
      $veiculo['chassi']                        = Portabilis_String_Utils::toUtf8($veiculo['chassi']);
      $veiculo['descricao_inativo']             = Portabilis_String_Utils::toUtf8($veiculo['descricao_inativo']);
      $veiculo['observacao']                    = Portabilis_String_Utils::toUtf8($veiculo['observacao']);
    
      return $veiculo;

  }

  protected function post() {

    if ($this->validateSizeOfDescricaoInativo() && $this->validateSizeOfObservacao()){
      $id = $this->createOrUpdateVeiculo();

      if (is_numeric($id)) {

        $this->messenger->append('Cadastro realizado com sucesso', 'success', false, 'error');
      }
      else
        $this->messenger->append('Aparentemente o veículo não pode ser cadastrado, por favor, verifique.');
    }   

    return array('id' => $id);
  }

  protected function put() {

    if ($this->validateSizeOfDescricaoInativo() && $this->validateSizeOfObservacao()){
     $id = $this->getRequest()->id;
     $editou = $this->createOrUpdateVeiculo($id);

     if ($editou) {
       $this->messenger->append('Alteração realizada com sucesso', 'success', false, 'error');
     }
     else
      $this->messenger->append('Aparentemente o cadastro não pode ser alterado, por favor, verifique.');
  }
   

    return array('id' => $id);
  }

  protected function delete() {
    $id = $this->getRequest()->id;


    $veiculo                      = new clsModulesVeiculo();
    $veiculo->cod_veiculo       = $id;
      
    if($veiculo->excluir()){
      $this->messenger->append('Cadastro removido com sucesso', 'success', false, 'error');
    }else
      $this->messenger->append('Aparentemente o cadastro não pode ser removido, por favor, verifique.','error', false, 'error');

    return array('id' => $id);
  }


  public function Gerar() {
    
    if ($this->isRequestFor('get', 'veiculo'))
      $this->appendResponse($this->get());

    elseif ($this->isRequestFor('get', 'veiculo-search'))
      $this->appendResponse($this->search());  

    // create
    elseif ($this->isRequestFor('post', 'veiculo'))
      $this->appendResponse($this->post());

    elseif ($this->isRequestFor('delete', 'veiculo')){
       if($this->validateIfVeiculoIsNotInUse()){
        $this->appendResponse($this->delete());
        echo "<script language= \"JavaScript\">
                location.href=\"intranet/transporte_veiculo_lst.php\";
              </script>";

        die();
      }
    // update
    }elseif ($this->isRequestFor('put', 'veiculo'))
      $this->appendResponse($this->put());

    else
      $this->notImplementedOperationError();
  }
}