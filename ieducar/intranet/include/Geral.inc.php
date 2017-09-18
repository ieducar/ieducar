<?php

/*
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
 */

/**
 * @author   Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo disponível desde a versão 1.0.0
 * @version  $Id$
 */

// Inclui operações de bootstrap.
require_once '../includes/bootstrap.php';


require_once ("include/pessoa/clsPessoa_.inc.php");
require_once ("include/pessoa/clsPessoaFj.inc.php");
require_once ("include/pessoa/clsPessoaJuridica.inc.php");
require_once ("include/pessoa/clsPessoaFisica.inc.php");
require_once ("include/pessoa/clsPessoaTelefone.inc.php");
require_once ("include/pessoa/clsEnderecoPessoa.inc.php");
require_once ("include/pessoa/clsEnderecoExterno.inc.php");
require_once ("include/pessoa/clsEndereco.inc.php");
require_once ("include/pessoa/clsFisicaCpf.inc.php");
require_once ("include/pessoa/clsFisica.inc.php");
require_once ("include/pessoa/clsJuridica.inc.php");
require_once ("include/pessoa/clsCepLogradouroBairro.inc.php");
require_once ("include/pessoa/clsCepLogradouro.inc.php");
require_once ("include/pessoa/clsLogradouro.inc.php");
require_once ("include/pessoa/clsBairro.inc.php");
require_once ("include/pessoa/clsMunicipio.inc.php");
require_once ("include/pessoa/clsUf.inc.php");
require_once ("include/pessoa/clsPais.inc.php");
require_once ("include/pessoa/clsVila.inc.php");
require_once ("include/pessoa/clsTipoLogradouro.inc.php");
require_once ("include/pessoa/clsFuncionario.inc.php");
require_once ("include/pessoa/clsEscolaridade.inc.php");
require_once ("include/pessoa/clsEstadoCivil.inc.php");
require_once ("include/pessoa/clsOcupacao.inc.php");
require_once ("include/pessoa/clsFisica.inc.php");
require_once ("include/pessoa/clsOrgaoEmissorRg.inc.php");
require_once ("include/pessoa/clsDocumento.inc.php");
require_once ("include/pessoa/clsRegiao.inc.php");
require_once ("include/pessoa/clsEscolaridade.inc.php");
require_once ("include/pessoa/clsCadastroEscolaridade.inc.php");
require_once ("include/pessoa/clsCadastroDeficiencia.inc.php");
require_once ("include/pessoa/clsCadastroFisicaDeficiencia.inc.php");
require_once ("include/pmidrh/clsSetor.inc.php");


require_once ("include/pmidrh/geral.inc.php");
require_once ("include/pessoa/clsBairroRegiao.inc.php");
require_once ("include/funcoes.inc.php");
require_once ("include/clsParametrosPesquisas.inc.php");
require_once ("include/portal/geral.inc.php");
require_once ("include/public/geral.inc.php");
require_once ("include/urbano/geral.inc.php");