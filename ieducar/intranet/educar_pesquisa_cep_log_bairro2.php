<?php
/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponÃ­vel desde a versÃ£o 1.0.0
 * @version   $Id$
 */
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Listagem de Ruas');
    $this->processoAp         = 0;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
  }
}
/**
 * miolo1 class.
 *
 * @author    Prefeitura Municipal de ItajaÃ­ <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponÃ­vel desde a versÃ£o 1.0.0
 * @version   @@package_version@@
 */
class miolo1 extends clsListagem
{
  var $funcao_js = 'cv_libera_campos(\'cep_\', \'ref_sigla_uf_\', \'cidade\', \'nm_bairro\', \'ref_idtlog\', \'nm_logradouro\', \'isEnderecoExterno\', \'zona_localizacao\')';
  function Gerar()
  {
    global $coreExt;
    @session_start();
    $_SESSION['campo1']  = $_GET['campo1']  ? $_GET['campo1']  : $_SESSION['campo1'];
    $_SESSION['campo2']  = $_GET['campo2']  ? $_GET['campo2']  : $_SESSION['campo2'];
    $_SESSION['campo3']  = $_GET['campo3']  ? $_GET['campo3']  : $_SESSION['campo3'];
    $_SESSION['campo4']  = $_GET['campo4']  ? $_GET['campo4']  : $_SESSION['campo4'];
    $_SESSION['campo5']  = $_GET['campo5']  ? $_GET['campo5']  : $_SESSION['campo5'];
    $_SESSION['campo6']  = $_GET['campo6']  ? $_GET['campo6']  : $_SESSION['campo6'];
    $_SESSION['campo7']  = $_GET['campo7']  ? $_GET['campo7']  : $_SESSION['campo7'];
    $_SESSION['campo8']  = $_GET['campo8']  ? $_GET['campo8']  : $_SESSION['campo8'];
    $_SESSION['campo9']  = $_GET['campo9']  ? $_GET['campo9']  : $_SESSION['campo9'];
    $_SESSION['campo10'] = $_GET['campo10'] ? $_GET['campo10'] : $_SESSION['campo10'];
    $_SESSION['campo11'] = $_GET['campo11'] ? $_GET['campo11'] : $_SESSION['campo11'];
    $_SESSION['campo12'] = $_GET['campo12'] ? $_GET['campo12'] : $_SESSION['campo12'];
    $_SESSION['campo13'] = $_GET['campo13'] ? $_GET['campo13'] : $_SESSION['campo13'];
    $_SESSION['campo14'] = $_GET['campo14'] ? $_GET['campo14'] : $_SESSION['campo14'];
    $this->nome = 'form1';
    $this->funcao_js = sprintf(
      'cv_libera_campos(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
      $_SESSION['campo10'], $_SESSION['campo11'], $_SESSION['campo7'],
      $_SESSION['campo1'], $_SESSION['campo12'], $_SESSION['campo4'],
      $_SESSION['campo9'], $_SESSION['campo14']
    );
    $this->titulo = 'EndereÃ§o';
    // Paginador
    $limite = 7;
    $iniciolimit = $_GET['pagina_' . $this->nome] ?
      ($_GET['pagina_' . $this->nome] * $limite - $limite) : 0;
    // Filtros
    $this->campoTexto('nm_bairro', 'Bairro', $_GET['nm_bairro'], 40, 255);
    $this->campoCep('nr_cep', 'CEP', $_GET['nr_cep']);
    $this->campoTexto('nm_logradouro', 'Logradouro', $_GET['nm_logradouro'], 50, 255);
    $this->campoTexto('cidade', 'Cidade', $_GET['cidade'], 60, 60);
    // uf
    $defaultProvince = isset($_GET['ref_sigla_uf']) ? $_GET['ref_sigla_uf'] : $coreExt['Config']->app->locale->province;
    $options = array(
      'required' => false,
      'label'    => 'Estado',
      'value'    => $defaultProvince
    );
    $helperOptions = array(
      'attrName' => 'ref_sigla_uf'
    );
    $this->inputsHelper()->uf($options, $helperOptions);
    $this->addCabecalhos(array('Bairro', 'CEP', 'Logradouro', 'UF', 'Cidade'));
    // consulta dados
    $pre_select = '
      SELECT
        c.idlog, c.cep, c.idbai, u.sigla_uf, m.nome, t.idtlog, m.idmun, b.zona_localizacao, t.descricao ';

    $select = '
      FROM
        urbano.cep_logradouro_bairro c, public.bairro b, public.logradouro l,
        public.municipio m, public.uf u, urbano.tipo_logradouro t
      WHERE
        c.idlog = l.idlog AND
        c.idbai = b.idbai AND
        l.idmun = b.idmun AND
        l.idmun = m.idmun AND
        l.idtlog = t.idtlog AND
        m.sigla_uf = u.sigla_uf';
    $params = array();
    if (isset($_GET['nr_cep']))
      $params['c.cep'] = idFederal2int($_GET['nr_cep']);
    if (isset($_GET['nm_bairro']))
      $params['b.nome'] = $_GET['nm_bairro'];
    if (isset($_GET['nm_logradouro']))
      $params['l.nome'] = $_GET['nm_logradouro'];
    if (isset($_GET['ref_sigla_uf']))
      $params['u.sigla_uf'] = $_GET['ref_sigla_uf'];
    if (isset($_GET['cidade']))
      $params['m.nome'] = $_GET['cidade'];
    $paramCount = 1;
    foreach ($params as $name => $value) {
      $select .= " AND $name ILIKE '%'||\$$paramCount||'%'";
      $paramCount++;
    }
    $total  = Portabilis_Utils_Database::selectField(' SELECT COUNT(0) '.$select, array('params' => array_values($params)));
    $select .= sprintf(' LIMIT %s OFFSET %s', $limite, $iniciolimit);
    $result = Portabilis_Utils_Database::fetchPreparedQuery($pre_select.$select, array('params' => array_values($params)));

    foreach ($result as $record) {
      list($idlog, $cep, $idbai, $uf, $cidade, $tipoLogradouroId, $id_mun, $zona, $descricao) = $record;
      $cidade     = addslashes($cidade);
      $logradouro = new clsLogradouro($idlog);
      $logradouro = $logradouro->detalhe();
      $logradouro = addslashes($logradouro['nome']);
      $bairro     = new clsBairro($idbai);
      $bairro     = $bairro->detalhe();
      $bairro     = addslashes($bairro['nome']);
      $cep2  = int2CEP($cep);
      $s_end = '0';
      $url = sprintf(
        '<a href="javascript:void(0);" onclick="cv_set_campo(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\');liberaCampos();parent.fixUpPlaceholderEndereco();">%%s</a>',
        $_SESSION['campo1'], $bairro.' / Zona'.$zona, $_SESSION['campo2'],
        $idbai, $_SESSION['campo3'], $cep,
        $_SESSION['campo4'], $descricao." ".$logradouro,
        $_SESSION['campo5'], $idlog,
        '', '', '', '',
        '', '', '', '',
        $_SESSION['campo10'], $cep2, $_SESSION['campo11'], $id_mun.' - '.$cidade.' ('.$uf.')',
        $_SESSION['campo12'], $_SESSION['campo13'], $id_mun,
        '', ''
      );
      $this->addLinhas(array(
        sprintf($url, $bairro),
        sprintf($url, $cep2),
        sprintf($url, $logradouro),
        sprintf($url, $uf),
        sprintf($url, $cidade)
      ));
    }
    $this->largura = '100%';
    $this->addPaginador2('educar_pesquisa_cep_log_bairro.php', $total, $_GET,
      $this->nome, $limite);

/*
    if ($_GET['param']) {
      $this->rodape = '
        <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
          <tr width="100%">
            <td>
              <div align="center">[ <a href="javascript:void(0);" onclick="liberaCamposOuvidoria()">Cadastrar Novo EndereÃ§o</a> ]</div>
            </td>
          </tr>
        </table>';
    }
    else {
      $this->rodape = sprintf('
        <table border="0" cellspacing="0" cellpadding="0" width="100%%" align="center">
          <tr width="100%%">
            <td>
              <div align="center">[ <a href="javascript:void(0);" onclick="%s">Cadastrar Novo EndereÃ§o</a> ]</div>
            </td>
          </tr>
        </table>',
        $this->funcao_js
      );
    }*/
    @session_write_close();
  }
}
// Instancia objeto de pÃ¡gina
$pagina = new clsIndex();
// Instancia objeto de conteÃºdo
$miolo = new miolo1();
// Atribui o conteÃºdo Ã   pÃ¡gina
$pagina->addForm($miolo);
// Gera o cÃ³digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
function setFiltro()
{
  alert('filtro');
}
// FunÃ§Ã£o especÃ­fica para ouvidoria
function setaCamposOuvidoria(valor1, valor2, valor3, valor4, valor5, valor6,
  valor7, valor8, valor9, valor10, valor11, valor12)
{
  // Campo oculto flag atualiza
  parent.document.getElementById('atualiza').value  = 'false';
  parent.document.getElementById('nendereco').value = 'false';
  // Campo oculto cep
  obj1 = parent.document.getElementById('cep');
  obj1.value = valor1;
  // Campo visÃ­vel cep
  obj2 = parent.document.getElementById('cep_');
  obj2.value    = valor2;
  obj2.disabled = true;
  // Campo oculto sigla_uf
  obj3 = parent.document.getElementById('sigla_uf');
  obj3.value = valor3;
   // Campo visÃ­vel sigla_uf
  obj4 = parent.document.getElementById('sigla_uf_');
  obj4.value    = valor4;
  obj4.disabled = true;
  // Campo oculto cidade
  obj5 = parent.document.getElementById('cidade');
  obj5.value = valor5;
   // Campo visÃ­vel cidade
  obj6 = parent.document.getElementById('cidade_');
  obj6.value    = valor6;
  obj6.disabled = true;
  // Campo oculto nmCidade
  obj14 = parent.document.getElementById('nmCidade');
  obj14.value = valor6;
  // Campo oculto Bairro
  obj7 = parent.document.getElementById('idbai');
  obj7.value = valor7;
   // Campo visÃ­vel Bairro
  obj8 = parent.document.getElementById('bairro_');
  obj8.value    = valor8;
  obj8.disabled = true;
  obj13 = parent.document.getElementById('bairro');
  obj13.value = valor8;
  // Campo oculto idtlog ("tipo logradouro")
  obj9 = parent.document.getElementById('idtlog');
  obj9.value = valor9;
  // Campo visÃ­vel idtlog_ ("tipo logradouro")
  obj10 = parent.document.getElementById('idtlog_');
  obj10.value    = valor10;
  obj10.disabled = true;
  // Campo oculto logradouro
  obj11 = parent.document.getElementById('idlog');
  obj11.value = valor11;
  // Campo visÃ­vel logradouro
  obj12 = parent.document.getElementById('logradouro_');
  obj12.value    = valor12;
  obj12.disabled = true;
  obj14 = parent.document.getElementById('logradouro');
  obj14.value = valor12;
  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
}
function liberaCamposOuvidoria()
{
  parent.document.getElementById('atualiza').value  = 'false';
  parent.document.getElementById('nendereco').value = 'true';
  // Campo oculto cep
  obj1 = parent.document.getElementById('cep');
  obj1.value = null;
   // Campo visÃ­vel cep
  obj2 = parent.document.getElementById('cep_');
  obj2.value    = null;
  obj2.disabled = false;
  // Campo oculto sigla_uf
  obj3 = parent.document.getElementById('sigla_uf');
  obj3.value = null;
   // Campo visÃ­vel sigla_uf
  obj4 = parent.document.getElementById('sigla_uf_');
  obj4.value    = null;
  obj4.disabled = false;
  // Campo oculto cidade
  obj5 = parent.document.getElementById('cidade');
  obj5.value = null;
  // Campo visÃ­vel cidade
  obj6 = parent.document.getElementById('cidade_');
  obj6.value    = null;
  obj6.disabled = false;
  // Campo oculto bairro
  obj7 = parent.document.getElementById('idbai');
  obj7.value = null;
   // Campo visÃ­vel bairro
  obj8 = parent.document.getElementById('bairro_');
  obj8.value    = null;
  obj8.disabled = false;
  obj13 = parent.document.getElementById('bairro');
  obj13.value = null;
  // Campo oculto idtlog ("tipo logradouro")
  obj9 = parent.document.getElementById('idtlog');
  obj9.value = null;
  // Campo visÃ­vel itlog_ ("tipo logradouro")
  obj10 = parent.document.getElementById('idtlog_');
  obj10.value    = null;
  obj10.disabled = false;
  // Campo oculto logradouro
  obj11 = parent.document.getElementById('idlog');
  obj11.value = null;
  // Campo visÃ­vel logradouro_
  obj12 = parent.document.getElementById('logradouro_');
  obj12.value    = null;
  obj12.disabled = false;
  obj14 = parent.document.getElementById('logradouro');
  obj14.value = null;
  window.parent.fechaExpansivel('div_dinamico_' + (parent.DOM_divs.length * 1 - 1));
}
function liberaCampos(){

  parent.document.getElementById('municipio_municipio').disabled = false;
  parent.document.getElementById('bairro_bairro').disabled = false;
  parent.document.getElementById('logradouro_logradouro').disabled = false;
  parent.document.getElementById('logradouro').disabled = false;
  parent.document.getElementById('bairro').disabled = false;
  parent.document.getElementById('idtlog').disabled = false;
  parent.document.getElementById('zona_localizacao').disabled = false;
  parent.document.getElementById('logradouro').value = '';
  parent.document.getElementById('bairro').value = '';
  parent.document.getElementById('idtlog').value = '';
  parent.document.getElementById('zona_localizacao').value = '';
}
</script>