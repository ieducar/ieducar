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
 * @package   iEd_Cadastro
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';

/**
 * clsEndereco class.
 *
 * Possui API de busca por endere�o de pessoa atrav�s da view
 * "cadastro.v_endereco".
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsEndereco
{
  var $idpes;
  var $tipo;
  var $idtlog;
  var $logradouro;
  var $idlog;
  var $numero;
  var $letra;
  var $complemento;
  var $bairro;
  var $idbai;
  var $cep;
  var $cidade;
  var $idmun;
  var $sigla_uf;
  var $reside_desde;
  var $bloco;
  var $apartamento;
  var $andar;
  var $zona_localizacao;

  /**
   * Construtor.
   * @param int $idpes
   */
  function clsEndereco($idpes = FALSE)
  {
    $this->idpes = $idpes;
  }

  /**
   * Retorna o endere�o da pessoa cadastrada (tabela cadastro.endereco_pessoa
   * ou cadastro.endereco_externo) como array associativo.
   * @return array|FALSE caso n�o haja um endere�o cadastrado.
   */
  function detalhe()
  {
    if ($this->idpes) {
      $db = new clsBanco();

      $sql = sprintf('SELECT
                cep, idlog, numero, letra, complemento, idbai, bloco, andar,
                apartamento, logradouro, bairro, cidade, sigla_uf, idtlog,
                zona_localizacao
              FROM
                cadastro.v_endereco
              WHERE
                idpes = %d', $this->idpes);

      $db->Consulta($sql);

      if ($db->ProximoRegistro()) {
        $tupla                  = $db->Tupla();
        $this->bairro           = $tupla['bairro'];
        $this->idbai            = $tupla['idbai'];
        $this->cidade           = $tupla['cidade'];
        $this->sigla_uf         = $tupla['sigla_uf'];
        $this->complemento      = $tupla['complemento'];
        $this->bloco            = $tupla['bloco'];
        $this->apartamento      = $tupla['apartamento'];
        $this->andar            = $tupla['andar'];
        $this->letra            = $tupla['letra'];
        $this->numero           = $tupla['numero'];
        $this->logradouro       = $tupla['logradouro'];
        $this->idlog            = $tupla['idlog'];
        $this->idtlog           = $tupla['idtlog'];
        $this->cep              = $tupla['cep'];
        $this->zona_localizacao = $tupla['zona_localizacao'];

        return $tupla;
      }
    }

    return FALSE;
  }

  function edita()
  {
  }
}