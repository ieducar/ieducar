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
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Cadastro
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pessoa/clsCadastroRaca.inc.php';
require_once 'include/pessoa/clsCadastroFisicaFoto.inc.php';
require_once 'include/pessoa/clsCadastroFisicaRaca.inc.php';

require_once 'App/Model/ZonaLocalizacao.php';

/**
 * clsIndex class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Pessoa');
    $this->processoAp = 43;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Cadastro
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsDetalhe
{
  function Gerar()
  {
  	@session_start();
  	$this->pessoa_logada = $_SESSION['id_pessoa'];
  	session_write_close();
  	
    $this->titulo = 'Detalhe da Pessoa';

    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $cod_pessoa = @$_GET['cod_pessoa'];

    $objPessoa = new clsPessoaFisica($cod_pessoa);
    $db        = new clsBanco();

    $detalhe = $objPessoa->queryRapida(
      $cod_pessoa, 'idpes', 'complemento','nome', 'cpf', 'data_nasc',
      'logradouro', 'idtlog', 'numero', 'apartamento','cidade','sigla_uf',
      'cep', 'ddd_1', 'fone_1', 'ddd_2', 'fone_2', 'ddd_mov', 'fone_mov',
      'ddd_fax', 'fone_fax', 'email', 'url', 'tipo', 'sexo', 'zona_localizacao'
    );


    $objFoto = new clsCadastroFisicaFoto($cod_pessoa);
    $caminhoFoto = $objFoto->detalhe();
    if ($caminhoFoto!=false)
      $this->addDetalhe(array('Nome', $detalhe['nome'].'
                                  <p><img height="117" src="'.$caminhoFoto['caminho'].'"/></p>'));
    else
      $this->addDetalhe(array('Nome', $detalhe['nome']));
     
    $this->addDetalhe(array('CPF', int2cpf($detalhe['cpf'])));

    if ($detalhe['data_nasc']) {
      $this->addDetalhe(array('Data de Nascimento', dataFromPgToBr($detalhe['data_nasc'])));
    }

    // Cor/Ra�a.
    $raca = new clsCadastroFisicaRaca($cod_pessoa);
    $raca = $raca->detalhe();
    if (is_array($raca)) {
      $raca = new clsCadastroRaca($raca['ref_cod_raca']);
      $raca = $raca->detalhe();

      if (is_array($raca)) {
        $this->addDetalhe(array('Ra�a', $raca['nm_raca']));
      }
    }

    if ($detalhe['logradouro']) {
      if ($detalhe['numero']) {
        $end = ' n� ' . $detalhe['numero'];
      }

      if ($detalhe['apartamento']) {
        $end .= ' apto ' . $detalhe['apartamento'];
      }

      $this->addDetalhe(array('Endere�o',
        strtolower($detalhe['idtlog']) . ': ' . $detalhe['logradouro'] . ' ' . $end)
      );
    }

    if ($detalhe['complemento']) {
      $this->addDetalhe(array('Complemento', $detalhe['complemento']));
    }

    if ($detalhe['cidade']) {
      $this->addDetalhe(array('Cidade', $detalhe['cidade']));
    }

    if ($detalhe['sigla_uf']) {
      $this->addDetalhe(array('Estado', $detalhe['sigla_uf']));
    }

    $zona = App_Model_ZonaLocalizacao::getInstance();
    if ($detalhe['zona_localizacao']) {
      $this->addDetalhe(array(
        'Zona Localiza��o', $zona->getValue($detalhe['zona_localizacao'])
      ));
    }

    if ($detalhe['cep']) {
      $this->addDetalhe(array('CEP', int2cep($detalhe['cep'])));
    }

    if ($detalhe['fone_1']) {
      $this->addDetalhe(
        array('Telefone 1', sprintf('(%s) %s', $detalhe['ddd_1'], $detalhe['fone_1']))
      );
    }

    if ($detalhe['fone_2']) {
      $this->addDetalhe(
        array('Telefone 2', sprintf('(%s) %s', $detalhe['ddd_2'], $detalhe['fone_2']))
      );
    }

    if ($detalhe['fone_mov']) {
      $this->addDetalhe(
        array('Celular', sprintf('(%s) %s', $detalhe['ddd_mov'], $detalhe['fone_mov']))
      );
    }

    if ($detalhe['fone_fax']) {
      $this->addDetalhe(
        array('Fax', sprintf('(%s) %s', $detalhe['ddd_fax'], $detalhe['fone_fax']))
      );
    }

    if ($detalhe['url']) {
      $this->addDetalhe(array('Site', $detalhe['url']));
    }

    if ($detalhe['email']) {
      $this->addDetalhe(array('E-mail', $detalhe['email']));
    }

    $sexo = $detalhe['sexo'] == 'M' ? 'Masculino' : 'Feminino';

    $this->addDetalhe(array('Sexo', $sexo));

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(43, $this->pessoa_logada, 3)) {
    	$this->url_novo     = 'atendidos_cad.php';
    	$this->url_editar   = 'atendidos_cad.php?cod_pessoa_fj=' . $detalhe['idpes'];
    }
    $this->url_cancelar = 'atendidos_lst.php';

    $this->largura = '100%';
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndex();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();