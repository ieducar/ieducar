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
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarFaltaAtrasoCompensado class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarFaltaAtrasoCompensado
{
  var $cod_compensado;
  var $ref_cod_escola;
  var $ref_ref_cod_instituicao;
  var $ref_cod_servidor;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_inicio;
  var $data_fim;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  /**
   * Armazena o total de resultados obtidos na �ltima chamada ao m�todo lista().
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por v�rgula, com os campos que devem ser selecionados na
   * pr�xima chamado ao m�todo lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por v�rgula, padr�o para
   * sele��o no m�todo lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo m�todo lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no m�todo lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padr�o de ordena��o no m�todo lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   */
  function clsPmieducarFaltaAtrasoCompensado($cod_compensado = NULL,
    $ref_cod_escola = NULL, $ref_ref_cod_instituicao = NULL,
    $ref_cod_servidor = NULL, $ref_usuario_exc = NULL, $ref_usuario_cad = NULL,
    $data_inicio = NULL, $data_fim = NULL, $data_cadastro = NULL,
    $data_exclusao = NULL, $ativo = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'falta_atraso_compensado';

    $this->_campos_lista = $this->_todos_campos = 'cod_compensado, ref_cod_escola, ref_ref_cod_instituicao, ref_cod_servidor, ref_usuario_exc, ref_usuario_cad, data_inicio, data_fim, data_cadastro, data_exclusao, ativo';

    if (is_numeric($ref_cod_escola)) {
      if (class_exists('clsPmieducarEscola')) {
        $tmp_obj = new clsPmieducarEscola($ref_cod_escola);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_escola = $ref_cod_escola;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_escola = $ref_cod_escola;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'")) {
          $this->ref_cod_escola = $ref_cod_escola;
        }
      }
    }

    if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
      if (class_exists('clsPmieducarServidor')) {
        $tmp_obj = new clsPmieducarServidor($ref_cod_servidor, NULL, NULL, NULL,
          NULL, NULL, NULL, 1, $ref_ref_cod_instituicao);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_servidor = $ref_cod_servidor;
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_servidor = $ref_cod_servidor;
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_cod_servidor}' AND ref_cod_instituicao = '{$ref_ref_cod_instituicao}'")) {
          $this->ref_cod_servidor = $ref_cod_servidor;
          $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
        }
      }
    }

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
          $this->ref_usuario_exc = $ref_usuario_exc;
        }
      }
    }

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
          $this->ref_usuario_cad = $ref_usuario_cad;
        }
      }
    }

    if (is_numeric($cod_compensado)) {
      $this->cod_compensado = $cod_compensado;
    }

    if (is_string($data_inicio)) {
      $this->data_inicio = $data_inicio;
    }

    if (is_string($data_fim)) {
      $this->data_fim = $data_fim;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_escola) && is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_usuario_cad) &&
      is_string($this->data_inicio) && is_string($this->data_fim)
    ) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_cod_escola)) {
        $campos  .= "{$gruda}ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_cod_escola}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_ref_cod_instituicao)) {
        $campos  .= "{$gruda}ref_ref_cod_instituicao";
        $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_servidor)) {
        $campos  .= "{$gruda}ref_cod_servidor";
        $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos  .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda    = ', ';
      }

      if (is_string($this->data_inicio)) {
        $campos  .= "{$gruda}data_inicio";
        $valores .= "{$gruda}'{$this->data_inicio}'";
        $gruda    = ', ';
      }

      if (is_string($this->data_fim)) {
        $campos  .= "{$gruda}data_fim";
        $valores .= "{$gruda}'{$this->data_fim}'";
        $gruda    = ', ';
      }

      $campos  .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda    = ', ';

      $campos  .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda    = ', ';

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return $db->InsertId("{$this->_tabela}_cod_compensado_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro
   *
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_compensado) && is_numeric($this->ref_usuario_exc)) {
      $db  = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_cod_escola)) {
        $set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_ref_cod_instituicao)) {
        $set .= "{$gruda}ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_cod_servidor)) {
        $set .= "{$gruda}ref_cod_servidor = '{$this->ref_cod_servidor}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ', ';
      }

      if (is_string($this->data_inicio)) {
        $set .= "{$gruda}data_inicio = '{$this->data_inicio}'";
        $gruda = ', ';
      }

      if (is_string($this->data_fim)) {
        $set .= "{$gruda}data_fim = '{$this->data_fim}'";
        $gruda = ', ';
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ', ';
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ', ';

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ', ';
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_compensado = '{$this->cod_compensado}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os par�metros
   * @return array
   */
  function lista($int_cod_compensado = NULL, $int_ref_cod_escola = NULL,
    $int_ref_ref_cod_instituicao = NULL, $int_ref_cod_servidor = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
    $date_data_inicio_ini = NULL, $date_data_inicio_fim = NULL,
    $date_data_fim_ini = NULL, $date_data_fim_fim = NULL,
    $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
    $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL,
    $int_ativo = NULL
  ) {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($int_cod_compensado)) {
      $filtros .= "{$whereAnd} cod_compensado = '{$int_cod_compensado}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_servidor)) {
      $filtros .= "{$whereAnd} ref_cod_servidor = '{$int_ref_cod_servidor}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_inicio_ini)) {
      $filtros .= "{$whereAnd} data_inicio >= '{$date_data_inicio_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_inicio_fim)) {
      $filtros .= "{$whereAnd} data_inicio <= '{$date_data_inicio_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_fim_ini)) {
      $filtros .= "{$whereAnd} data_fim >= '{$date_data_fim_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_fim_fim)) {
      $filtros .= "{$whereAnd} data_fim <= '{$date_data_fim_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_NULL($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} ativo = '1'";
      $whereAnd = ' AND ';
    }
    else {
      $filtros .= "{$whereAnd} ativo = '0'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_compensado)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_compensado = '{$this->cod_compensado}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->cod_compensado)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_compensado = '{$this->cod_compensado}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_compensado) && is_numeric($this->ref_usuario_exc)) {
      $this->ativo = 0;
      return $this->edita();
    }

    return FALSE;
  }

  /**
   * Retorna a quantidade de horas compensadas.
   * @return array
   */
  function ServidorHorasCompensadas($int_ref_cod_servidor = NULL,
    $int_ref_cod_escola = NULL, $int_ref_cod_instituicao)
  {
    if (is_numeric($int_ref_cod_servidor)) {
      /*strtotime( '2006-06-06' );
      date( "Y-m-d", time );*/
      $db = new clsBanco();
      $db->Consulta("
        SELECT
          fac.data_inicio,
          fac.data_fim
        FROM
          pmieducar.falta_atraso_compensado fac
        WHERE
          fac.ref_cod_servidor            = '{$int_ref_cod_servidor}'
          AND fac.ref_cod_escola          = '{$int_ref_cod_escola}'
          AND fac.ref_ref_cod_instituicao = '{$int_ref_cod_instituicao}'");

      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla;
      }

      $horas_total   = 0;
      $minutos_total = 0;

      if ($resultado) {
        foreach ( $resultado as $registro ) {
          $data_atual = strtotime($registro['data_inicio']);
          $data_fim   = strtotime($registro['data_fim']);

          do {
            $db2 = new clsBanco();

            $dia_semana   = $db2->CampoUnico("SELECT EXTRACT(DOW FROM (date '".date('Y-m-d', $data_atual)."') + 1)");
            $obj_servidor = new clsPmieducarServidor();
            $horas        = $obj_servidor->qtdhoras($int_ref_cod_servidor,
              $int_ref_cod_escola, $int_ref_cod_instituicao, $dia_semana);

            if ($horas) {
              $horas_total   += $horas['hora'];
              $minutos_total += $horas['min'];
            }

            $data_atual += 86400;
          } while ($data_atual <= $data_fim);
        }
      }

      $res['hora'] = $horas_total;
      $res['min']  = $minutos_total;

      return $res;
    }

    return FALSE;
  }

/**
   * Define quais campos da tabela ser�o selecionados no m�todo Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o m�todo Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o m�todo Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query respons�vel pelo limite de
   * registros retornados/afetados.
   *
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado como ordena��o no m�todo Lista().
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query respons�vel pela Ordena��o dos
   * registros.
   *
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }
}