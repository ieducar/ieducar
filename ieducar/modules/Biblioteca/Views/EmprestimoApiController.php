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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Biblioteca
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'include/pmieducar/clsPmieducarExemplar.inc.php';
require_once 'include/pmieducar/clsPmieducarBibliotecaDia.inc.php';
require_once 'include/pmieducar/clsPmieducarBibliotecaFeriados.inc.php';
require_once 'lib/Portabilis/Array/Utils.php';

class EmprestimoApiController extends ApiCoreController
{
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_BIBLIOTECA;

  // validadores regras negócio

  protected function validatesExistenceOfExemplar() {
    $valid = true;

    $exemplares = $this->loadExemplar($reload = true);

    if (! is_array($exemplares) || count($exemplares) < 1) {
      $id    = $this->getRequest()->exemplar_id;
      $tombo = $this->getRequest()->tombo_exemplar;

      $this->messenger->append("Aparentemente não existe um exemplar com id $id e/ou tombo $tombo, para a biblioteca informada.");
      $valid = false;
    }

    return $valid;
  }


  protected function validatesExistenceOfCliente() {
    $valid = true;

    if (! $this->loadCliente()) {
      $this->messenger->append("Não existe um cliente com id '{$this->getRequest()->cliente_id}'.");
      $valid = false;
    }

    return $valid;
  }


  protected function validatesClienteIsNotSuspenso() {
    $cliente = $this->loadCliente();

    if($cliente['suspenso']) {
      $this->messenger->append("Operação não pode ser realizada, pois o cliente esta suspenso.", 'error');
      return false;
    }

    return true;
  }


  protected function validatesSituacaoExemplarIsIn($expectedSituacoes) {
    if (! is_array($expectedSituacoes))
      $expectedSituacoes = array($expectedSituacoes);

    $situacao = $this->getSituacaoExemplar();
    $situacao = $situacao['flag'];
    $msg = "Operação não realizada, pois a situação atual do exemplar é $situacao quando deveria ser " . implode(' ou ', $expectedSituacoes) . ".";

    return $this->validator->validatesValueInSetOf($situacao, $expectedSituacoes, 'situação', false, $msg);
  }


  protected function canAcceptRequest() {
    return parent::canAcceptRequest()

           && $this->validatesPresenceOf(array('instituicao_id',
                                                'escola_id',
                                                'biblioteca_id',
                                                'cliente_id',
                                                'tombo_exemplar'))

           && $this->validatesIsNumeric('tombo_exemplar')
           && $this->validatesExistenceOfExemplar()
           && $this->validatesExistenceOfCliente();
  }


  protected function canPostEmprestimo() {
    return $this->validatesPresenceOf(array('exemplar_id'))
           && $this->validatesExistenceOfExemplar()
           && $this->validatesClienteIsNotSuspenso()
           && $this->validatesSituacaoExemplarIsIn('disponivel');

           /*
            #TODO validar:
              qtd emprestimos em aberto do cliente <= limite biblioteca
              valor R$ multas em aberto do cliente <= limite biblioteca

              não existe outro exemplar mesma obra emprestado para cliente
              validates situacao exemplar is disponivel or is reservado cliente
           */
  }


  protected function canPostDevolucao() {
    return $this->validatesPresenceOf(array('exemplar_id'))
           && $this->validatesExistenceOfExemplar()
           && $this->validatesSituacaoExemplarIsIn(array('emprestado', 'emprestado_e_reservado'));
  }


  /* metódos auxiliares resposta operação / recurso
    metódos iniciados com load consultam informação no banco de dados
    metódos iniciados com get consultam informação em objetos
  */

  protected function loadCliente($id = null) {
    if (! $id)
      $id = $this->getRequest()->cliente_id;

    // load cliente
		$cliente = new clsPmieducarCliente($id);
		$cliente = $cliente->detalhe();

    if ($cliente) {
      $cliente = Portabilis_Array_Utils::filter($cliente, array('cod_cliente' => 'id',
                                                                'ref_idpes'   => 'pessoa_id'));

      // load pessoa
		  $pessoa          = new clsPessoa_($cliente['pessoa_id']);
		  $pessoa          = $pessoa->detalhe();
      $cliente['nome'] = $this->toUtf8($pessoa["nome"]);

      // load suspensao
      $sql = "select 1 from pmieducar.cliente_suspensao where ref_cod_cliente = $1 and data_liberacao is null and data_suspensao + (dias||' day')::interval >= now()";
      $suspenso = $this->fetchPreparedQuery($sql, $params = array($id), true, 'first-field');

      $cliente['suspenso'] = $suspenso == '1';
    }

    return $cliente;
  }


  protected function loadQtdDiasEmprestimoForExemplar($exemplar = null) {
    $acervo             = $this->loadAcervo($exemplar['acervo']['id']);
    $exemplarTipoId     = $acervo['exemplar_tipo_id'];

    // obtem id tipo de cliente
		$clienteTipoCliente = new clsPmieducarClienteTipoCliente();
    $clienteTipoCliente = $clienteTipoCliente->lista(null,
                                                     $this->getRequest()->cliente_id,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     null,
                                                     $this->getRequest()->biblioteca_id);

    $clienteTipoId           = $clienteTipoCliente[0]['ref_cod_cliente_tipo'];

    // obtem quantidade dias emprestimo
		$clienteTipoExemplarTipo = new clsPmieducarClienteTipoExemplarTipo($clienteTipoId, $exemplarTipoId);
		$clienteTipoExemplarTipo = $clienteTipoExemplarTipo->detalhe();

    if (! $clienteTipoExemplarTipo || ! is_numeric($clienteTipoExemplarTipo["dias_emprestimo"]))
      throw new CoreExt_Exception("Aparentemente não foi definido a quantidade de dias de emprestimo para o tipo de cliente '$clienteTipoId' e tipo de exemplar '$exemplarTipoId'.");

		return $clienteTipoExemplarTipo["dias_emprestimo"];
  }


  protected function getDataPrevistaDisponivelForExemplar($exemplar, $dataInicio, $format = 'd/m/Y') {
    $qtdDiasEmprestimo = $this->loadQtdDiasEmprestimoForExemplar($exemplar);

    // reformada data dd/mm/aaaa => mm/dd/aaaa
    $_format = explode('/', $format);

    if (count($_format) > 0 && $_format[0] == 'd') {
      list($diaInicio, $mesInicio, $anoInicio) = explode("/", $dataInicio);
      $dataInicio = "$mesInicio/$diaInicio/$anoInicio";
    }
   
    // soma dias emprestimo
    $date = date('Y-m-d', strtotime("+$qtdDiasEmprestimo days", strtotime($dataInicio)));

    // #TODO Caso seja a devolução seja refatorada, separar esse trecho num método para reutilizar código
    $dias_da_semana = array( 'Sun' => 1, 'Mon' => 2, 'Tue' => 3, 'Wed' => 4, 'Thu' => 5, 'Fri' => 6, 'Sat' => 7 );

    $obj_biblioteca_dia = new clsPmieducarBibliotecaDia();
    $lst_biblioteca_dia = $obj_biblioteca_dia->lista($this->getRequest()->biblioteca_id);
    if( is_array( $lst_biblioteca_dia ) && count( $lst_biblioteca_dia ) )
    {
      foreach ($lst_biblioteca_dia AS $dia_semana)
      {
        // dias de funcionamento da biblioteca
        $biblioteca_dias_semana[] = $dia_semana["dia"];
      }
    }
    // Array de dias de não funcionamento
    $biblioteca_dias_folga = array_diff($dias_da_semana, $biblioteca_dias_semana);
    // inverte as relacoes entre chaves e valores ( de $variavel["Sun"] => 1, para $variavel[1] => "Sun")
    $biblioteca_dias_folga = array_flip($biblioteca_dias_folga);    

    $obj_biblioteca_feriado = new clsPmieducarBibliotecaFeriados();
    $lst_biblioteca_feriado = $obj_biblioteca_feriado->lista( null, $this->getRequest()->biblioteca_id );
    if( is_array( $lst_biblioteca_feriado ) && count( $lst_biblioteca_feriado ) )
    {
      foreach ($lst_biblioteca_feriado AS $dia_feriado)
      {
        // dias de feriado da biblioteca
        $biblioteca_dias_feriado[] = dataFromPgToBr($dia_feriado["data_feriado"], "D Y-m-d");
      }
    }    

    $data_entrega = dataFromPgToBr($date, "D Y-m-d");

    if(!is_array($biblioteca_dias_folga))
    {
      $biblioteca_dias_folga = array(null);
    }
    if(!is_array($biblioteca_dias_feriado))
    {
      $biblioteca_dias_feriado = array(null);
    }

    // verifica se a data cai em algum dia que a biblioteca n funciona
    while( in_array(substr($data_entrega,0,3), $biblioteca_dias_folga) || in_array($data_entrega, $biblioteca_dias_feriado) )
    {
      $data_entrega = date("D Y-m-d ",strtotime("$data_entrega +1 day"));
      $data_entrega = dataFromPgToBr($data_entrega, "D Y-m-d");
    }

    $data_entrega = dataFromPgToBr($data_entrega, $format);  

    return $data_entrega;
  }


  protected function loadReservasForExemplar($exemplar, $clienteId = null, $reload = false) {
    if ($reload || ! isset($this->_reservas)) {
		  $reservas = new clsPmieducarReservas();
		  $reservas = $reservas->lista(null,
                                   null,
                                   null,
                                   $clienteId,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   null,
                                   $exemplar['id'],
                                   1,
                                   $this->getRequest()->biblioteca_id,
                                   $this->getRequest()->instituicao_id,
                                   $this->getRequest()->escola_id,
                                   $data_retirada_null = true);

      if($reservas) {
        $reservas = Portabilis_Array_Utils::filterSet($reservas, array('cod_reserva'     => 'id',
                                                                       'data_reserva'    => 'data',
                                                                       'ref_cod_cliente' => 'cliente_id',
                                                                       'data_prevista_disponivel'));

        // adicionada informaçoes adicionais a cada reserva
        foreach($reservas as $index => $reserva) {
          $cliente                 = $this->loadCliente($reserva["cliente_id"]);

          $reserva['cliente']      = $cliente;
          $reserva['nome_cliente'] = $cliente['id'] . ' - ' . $cliente['nome'];
          $reserva['data']         = date('d/m/Y', strtotime($reserva['data']));
          $reserva['situacao']     = $this->getSituacaoForFlag('reservado');

        /* para o cliente da reserva: considera a data prevista disponivel gravada na reserva.
           para outros considera a data prevista disponivel da reserva + a quantidade de dias de emprestimo do exemplar
        */
        if ($this->getRequest()->cliente_id == $cliente['id'])
          $reserva['data_prevista_disponivel'] = date('d/m/Y', strtotime($reserva['data_prevista_disponivel']));

        else
          $reserva['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($exemplar, $reserva['data_prevista_disponivel'], 'd/m/Y');
        } //fim for each
      }

      $this->_reservas = $reservas;
    }

    return $this->_reservas;
  }


  protected function loadEmprestimoForExemplar($exemplar = null) {
    if(is_null($exemplar))
      $exemplar = $this->loadExemplar();

    $emprestimo = new clsPmieducarExemplarEmprestimo();

    $emprestimo = $emprestimo->lista(null,
                                     null,
                                     null,
                                     null,
                                     $exemplar['id'],
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     $devolvido = false,
                                     $this->getRequest()->biblioteca_id,
                                     null,
                                     $this->getRequest()->instituicao_id,
                                     $this->getRequest()->escola_id);

    if($emprestimo) {
  	  $emprestimo = array_shift($emprestimo);
      $emprestimo = Portabilis_Array_Utils::filter($emprestimo, array('cod_emprestimo'  => 'id',
                                                                      'data_retirada'   => 'data',
                                                                      'ref_cod_cliente' => 'cliente_id'));
      // adiciona informações adicionais ao emprestimo
      $cliente                                = $this->loadCliente($emprestimo["cliente_id"]);

      $emprestimo['cliente']                  = $cliente;
      $emprestimo['nome_cliente']             = $cliente['id'] . ' - ' . $cliente['nome'];
      $emprestimo['situacao']                 = $this->getSituacaoForFlag('emprestado');

      $emprestimo['data']                     = date('d/m/Y', strtotime($emprestimo['data']));
      $emprestimo['data_prevista_disponivel'] = $this->getDataPrevistaDisponivelForExemplar($exemplar, $emprestimo['data'], 'd/m/Y');
    }

    return $emprestimo;
  }


  protected function existsReservaForExemplar($exemplar = null, $clienteId = null) {
    $reservas = $this->loadReservasForExemplar($exemplar, $clienteId, $reload = true);
    return is_array($reservas) && count($reservas) > 0;
  }


  protected function getSituacaoForFlag($flag) {
    $situacoes = array(
      'indisponivel'           => array('flag'  => 'indisponivel', 'label' => 'Indisponível'),
      'disponivel'             => array('flag'  => 'disponivel'  , 'label' => 'Disponível'  ),
      'emprestado'             => array('flag'  => 'emprestado'  , 'label' => 'Emprestado'  ),
      'reservado'              => array('flag'  => 'reservado'   , 'label' => 'Reservado'   ),
      'emprestado_e_reservado' => array('flag'  => 'emprestado_e_reservado',
                                        'label' => 'Emprestado e reservado'                ),
      'invalida'               => array('flag'  => 'invalida'    , 'label' => 'Inválida'    )
    );

    return $situacoes[$flag];
  }


  protected function loadSituacaoForExemplar($exemplar) {

    $situacao                  = new clsPmieducarSituacao($exemplar["situacao_id"]);
    $situacao                  = $situacao->detalhe();

    $reservado                 = $this->existsReservaForExemplar($exemplar);
    $emprestado                = $situacao["situacao_emprestada"] == 1;

    $situacaoPermiteEmprestimo = $situacao["permite_emprestimo"]  == 2;
    $exemplarPermiteEmprestimo = $exemplar["permite_emprestimo"]  == 2;

    if ($emprestado && $reservado)
      $flagSituacaoExemplar = 'emprestado_e_reservado';
    elseif ($emprestado)
      $flagSituacaoExemplar = 'emprestado';
    elseif ($reservado)
      $flagSituacaoExemplar =  'reservado';
    elseif ($situacaoPermiteEmprestimo && $exemplarPermiteEmprestimo)
      $flagSituacaoExemplar = 'disponivel';
    elseif (! $situacaoPermiteEmprestimo || ! $exemplarPermiteEmprestimo)
      $flagSituacaoExemplar = 'indisponivel';
    else
      $flagSituacaoExemplar = 'invalida';

    return $this->getSituacaoForFlag($flagSituacaoExemplar);
  }


  protected function getSituacaoExemplar($exemplar = null) {
    if (is_null($exemplar))
      $exemplar = $this->loadExemplar();

    if ($exemplar['situacao']['flag'] == 'reservado')
      $exemplar['situacao']['flag'] = $this->validateReservaOfExemplar($exemplar);    

    return $exemplar['situacao'];
  }

  // Verifica se há reservas para o exemplar
  // Efetua retirada da reserva caso seja para o cliente em questão
  protected function validateReservaOfExemplar($exemplar = null){
    if (is_null($exemplar))
      $exemplar = $this->loadExemplar();

    $reservas = $this->loadReservasForExemplar($exemplar);
    $cont = 0;
    $clientePossuiReserva = false;
    $codReserva = 0;
    if (is_array($reservas) && count($reservas)){
      foreach ($reservas as $registro) {
        $cont;
        if ($registro['cliente_id'] == $this->getRequest()->cliente_id){
          $clientePossuiReserva = true;
          $codReserva = $registro['id'];
          break;
        }
      }
    }
    if ($clientePossuiReserva){
      if ($cont==1){
        $reservas = new clsPmieducarReservas($codReserva);
        $reservas->data_retirada = date('Y-m-d H:i:s');
        $reservas->edita();
        $return = 'disponivel';
      }else{ 

        $this->messenger->append("Outros clientes já haviam reservado o exemplar anteriormente.".$cont, 'success');
        $return = 'reservado';
      }
    }else if($cont>0){
      $this->messenger->append("Outros clientes já haviam reservado o exemplar.", 'success');
      $return = 'reservado';
    }
    
    return $return;
  }


  protected function getPendenciasForExemplar($exemplar) {
    if (! isset($exemplar['situacao']))
      throw new CoreExt_Exception("Exemplar deve possuir uma chave 'situacao' para getPendenciasForExemplar.");

    $situacaoExemplar = $exemplar['situacao'];
    $pendencias       = array();

    // get emprestimo
    if (strpos($situacaoExemplar['flag'], 'emprestado') > -1) {
      $emprestimo = $this->loadEmprestimoForExemplar($exemplar);

      if($emprestimo != false)
        $pendencias[] = $emprestimo;
    }

    // get reservas
    if (strpos($situacaoExemplar['flag'], 'reservado') > -1) {
      $reservas = $this->loadReservasForExemplar($exemplar);

      if ($reservas != false)
        $pendencias = array_merge($pendencias, $reservas);
    }

    return $pendencias;
  }


  protected function loadAcervo($id = '', $reload = false) {
    if (empty($id))
      $id = $this->getRequest()->acervo_id;

    if ($reload || ! isset($this->_acervos))
      $this->_acervos = array();

    if (! isset($this->_acervos[$id])) {
      $acervo = new clsPmieducarAcervo($id);
      $acervo = $acervo->detalhe();

      if ($acervo) {
        $acervo = Portabilis_Array_Utils::filter($acervo, array('cod_acervo'             => 'id',
                                                                'ref_cod_exemplar_tipo'  => 'exemplar_tipo_id',
                                                                'ref_cod_acervo'         => 'acervo_referencia_id',
                                                                'ref_cod_acervo_colecao' => 'colecao_id',
                                                                'ref_cod_acervo_idioma'  => 'idioma_id',
                                                                'ref_cod_acervo_editora' => 'editora_id',
                                                                'ref_cod_biblioteca'     => 'biblioteca_id',
                                                                'titulo',
                                                                'sub_titulo',
                                                                'cdu',
                                                                'cutter',
                                                                'volume',
                                                                'num_edicao',
                                                                'ano',
                                                                'num_paginas',
                                                                'isbn',
                                                                'data_cadastro'));
      }


      $this->_acervos[$id] = $acervo;
    }

    return $this->_acervos[$id];
  }


  protected function loadExemplares($reload = false, $id = null) {
    if ($reload || ! isset($this->_exemplares)) {
		  $exemplares = new clsPmieducarExemplar();

      // filtra por acervo_id e/ou tombo_exemplar (caso tenha recebido tais parametros)
      $exemplares = $exemplares->lista($id,
                                       null,
                                       null,
                                       $this->getRequest()->acervo_id,
                                       null,
                                       null,
                                       null,
                                       null,
                                       null,
                                       null,
                                       null,
                                       null,
                                       null,
                                       1,
                                       null,
                                       null,
                                       null,
                                       null,
                                       $this->getRequest()->biblioteca_id,
                                       null,
                                       $this->getRequest()->instituicao_id,
                                       $this->getRequest()->escola_id,
                                       $this->getRequest()->tombo_exemplar);

      if ($exemplares) {
        $exemplares = Portabilis_Array_Utils::filterSet($exemplares, array('cod_exemplar'         => 'id',
                                                                           'ref_cod_fonte'        => 'fonte_id',
                                                                           'ref_cod_motivo_baixa' => 'motivo_baixa_id',
                                                                           'ref_cod_acervo'       => 'acervo_id',
                                                                           'ref_cod_biblioteca'   => 'biblioteca_id',
                                                                           'ref_cod_situacao'     => 'situacao_id',
                                                                           'permite_emprestimo',
                                                                           'tombo'));
        // adiciona situacao e pendencias de cada exemplar
        foreach($exemplares as $index => $exemplar) {
          $acervo                                           = $this->loadAcervo($exemplar['acervo_id']);
          $exemplares[$index]['acervo']                     = array();
          $exemplares[$index]['acervo']['id']               = $exemplar['acervo_id'];
          $exemplares[$index]['acervo']['titulo']           = $acervo['titulo'];
          $exemplares[$index]['acervo']['exemplar_tipo_id'] = $acervo['exemplar_tipo_id'];

          $exemplares[$index]['exemplar_tipo_id']           = $acervo['exemplar_tipo_id'];

          $exemplares[$index]['situacao']         = $this->loadSituacaoForExemplar($exemplares[$index]);
          $exemplares[$index]['pendencias']       = $this->getPendenciasForExemplar($exemplares[$index]);
        }
      }

      $this->_exemplares = $exemplares;
    }

    return $this->_exemplares;
  }


  protected function loadExemplar($reload = false, $id = null) {
    if (! $id)
      $id = $this->getRequest()->exemplar_id;

    return array_shift($this->loadExemplares($reload, $id));
  }


  /* metódos resposta operação / recurso
     metódos nomeados no padrão operaçãoRecurso */

  protected function getExemplares() {
    $this->appendResponse('exemplares', $this->loadExemplares($reload = true));
  }


  protected function loadSituacaoExemplar($permiteEmprestimo = true, $padrao = true, $emprestada = false){
    $permiteEmprestimo = $permiteEmprestimo == true ? 2 : 1;
    $emprestada        = $emprestada        == true ? 1 : 0;

    if (! is_null($padrao))
      $padrao = $padrao == true ? 1 : 0;

    $situacao = new clsPmieducarSituacao();
    $situacao = $situacao->lista(null,
                                 null,
                                 null,
                                 null,
                                 $permiteEmprestimo,
                                 null,
                                 $padrao,
                                 $emprestada,
                                 null,
                                 null,
                                 null,
                                 null,
                                 1,
                                 $this->getRequest()->biblioteca_id,
                                 $this->getRequest()->instituicao_id,
                                 $this->getRequest()->escola_id);

    if ($situacao) {
      $situacao = Portabilis_Array_Utils::filter($situacao[0], array('cod_situacao'     => 'id',
                                                                  'ref_cod_biblioteca'  => 'biblioteca_id',
                                                                  'nm_situacao'         => 'label',
                                                                  'situacao_padrao'     => 'padrao',
                                                                  'situacao_emprestada' => 'emprestada',
                                                                  'permite_emprestimo',
                                                                  'descricao'));
    }

    return $situacao;
  }


  protected function updateSituacaoExemplar($newSituacao){
    if (! $newSituacao)
      throw new CoreExt_Exception('$newSituacao não pode ser falso em updateSituacaoExemplar.');

    $exemplar                   = new clsPmieducarExemplar();
    $exemplar->cod_exemplar     = $this->getRequest()->exemplar_id;
    $exemplar->ref_cod_acervo   = $this->getRequest()->acervo_id;
    $exemplar->ref_cod_situacao = $newSituacao['id'];
    $exemplar->ref_usuario_exc  = $this->getSession()->id_pessoa;

		return $exemplar->edita();
  }


  protected function postEmprestimo() {
    if ($this->canPostEmprestimo()) {
      // altera situacao exemplar para emprestado
      $situacaoEmprestimo = $this->loadSituacaoExemplar($permiteEmprestimo = false, $padrao = null, $emprestada = true);

      if($situacaoEmprestimo && ! $this->updateSituacaoExemplar($situacaoEmprestimo))
        $this->messenger->append("Aparentemente a situação do exemplar não foi alterada para emprestado.", 'error');
      elseif(! $situacaoEmprestimo)
        $this->messenger->append("Não foi encontrado uma situação cadastrada para emprestimo.", 'error');

      // grava emprestimo
		  if(! $this->messenger->hasMsgWithType('error')) {
        $emprestimo                   = new clsPmieducarExemplarEmprestimo();
        $emprestimo->ref_usuario_cad  = $this->getSession()->id_pessoa;
        $emprestimo->ref_cod_cliente  = $this->getRequest()->cliente_id;
        $emprestimo->ref_cod_exemplar = $this->getRequest()->exemplar_id;

        if ($emprestimo->cadastra())
          $this->messenger->append("Emprestimo realizado com sucesso.", 'success');
        else
          $this->messenger->append("Aparentemente o realizado não foi cadastrado, por favor, tente novamente.", 'error');
      }
    }

    $this->appendResponse('exemplar', $this->loadExemplar($reload = true));
  }


  protected function postDevolucao() {

    if ($this->canPostDevolucao()) {
      // altera situacao exemplar para disponivel
      $situacaoDisponivel = $this->loadSituacaoExemplar($permiteEmprestimo = true, $padrao = true, $emprestada = false);

      if($situacaoDisponivel && ! $this->updateSituacaoExemplar($situacaoDisponivel))
        $this->messenger->append("Aparentemente a situação do exemplar não foi alterada para disponivel.", 'error');
      elseif(! $situacaoDisponivel)
        $this->messenger->append("Não foi encontrado uma situação padrão cadastrada para exemplar disponivel.", 'error');

      // grava emprestimo
		  if(! $this->messenger->hasMsgWithType('error')) {

        $_emprestimo                        = $this->loadEmprestimoForExemplar();
        $emprestimo                         = new clsPmieducarExemplarEmprestimo();
        $emprestimo->cod_emprestimo         = $_emprestimo['id'];
        $emprestimo->ref_usuario_devolucao  = $this->getSession()->id_pessoa;
        $emprestimo->data_devolucao         = date("Y-m-d");

        // TODO calcular / setar valor multa (se) devolução atrasada?

        if ($emprestimo->edita())
          $this->messenger->append("Devolução realizada com sucesso.", 'success');
        else
          $this->messenger->append("Aparentemente a devolução não foi cadastrada, por favor, tente novamente.", 'error');
      }
    }

    $this->appendResponse('exemplar', $this->loadExemplar($reload = true));
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'exemplares'))
      $this->getExemplares();

    elseif ($this->isRequestFor('post', 'emprestimo'))
      $this->postEmprestimo();

    elseif ($this->isRequestFor('post', 'devolucao'))
      $this->postDevolucao();

    else
      $this->notImplementedOperationError();
  }
}
