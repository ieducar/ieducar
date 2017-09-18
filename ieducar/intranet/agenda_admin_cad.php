<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Agenda" );
		$this->processoAp = "343";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsCadastro
{
	var $cod_agenda,
		$ref_ref_cod_pessoa_exc,
		$ref_ref_cod_pessoa_cad,
		$nm_agenda,
		$publica,
		$envia_alerta,
		$data_cad,
		$data_edicao,
		$ref_ref_cod_pessoa_own,
		$dono,
		$editar;

	function Inicializar()
	{
		$retorno = "Novo";

		$this->editar = false;
		if ( isset( $_GET['cod_agenda'] ) )
		{
			$this->cod_agenda = $_GET['cod_agenda'];
			$db = new clsBanco();
			$db->Consulta( "SELECT cod_agenda, ref_ref_cod_pessoa_exc, ref_ref_cod_pessoa_cad,	nm_agenda, publica, envia_alerta, data_cad, data_edicao, ref_ref_cod_pessoa_own FROM portal.agenda WHERE cod_agenda='{$this->cod_agenda}'" );
			if ($db->ProximoRegistro())
			{
				list( $this->cod_agenda, $this->ref_ref_cod_pessoa_exc, $this->ref_ref_cod_pessoa_cad, $this->nm_agenda, $this->publica, $this->envia_alerta, $this->data_cad, $this->data_edicao, $this->ref_ref_cod_pessoa_own ) = $db->Tupla();
				$this->fexcluir = true;
				$retorno = "Editar";
				$this->editar = true;
			}
			if( isset( $_GET["edit_rem"] ) && is_numeric( $_GET["edit_rem"] ) )
			{
				$db->Consulta( "DELETE FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$_GET["edit_rem"]}' AND ref_cod_agenda = '{$this->cod_agenda}'" );
				$this->mensagem = "Editor removido";
			}
			if( isset( $_POST["novo_editor"] ) && is_numeric( $_POST["novo_editor"] ) )
			{
				$db->Consulta( "SELECT 1 FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$_POST["novo_editor"]}' AND ref_cod_agenda = '{$this->cod_agenda}'" );
				if( ! $db->ProximoRegistro() )
				{
					$db->Consulta( "SELECT 1 FROM agenda WHERE ref_ref_cod_pessoa_own = '{$_POST["novo_editor"]}' AND cod_agenda = '{$this->cod_agenda}'" );
					if( ! $db->ProximoRegistro() )
					{
						$db->Consulta( "INSERT INTO agenda_responsavel ( ref_ref_cod_pessoa_fj, ref_cod_agenda ) VALUES ( '{$_POST["novo_editor"]}', '{$this->cod_agenda}' )" );
					}
					else
					{
						$this->mensagem = "O dono da agenda j&aacute; &eacute; considerado um editor da mesma.";
					}
				}
				else
				{
					$this->mensagem = "Este editor j&aacute; est&aacute; cadastrado";
				}
			}
		}

		if( $retorno == "Editar" )
		{
			$this->url_cancelar = "agenda_admin_det.php?cod_agenda={$this->cod_agenda}";
		}
		else
		{
			$this->url_cancelar = "agenda_admin_lst.php";
		}
		$this->nome_url_cancelar = "Cancelar";

		$nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""                                  => "$nomeMenu agenda"
    ));
    $this->enviaLocalizacao($localizacao->montar());			

		return $retorno;
	}

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$db = new clsBanco();
		$objPessoa = new clsPessoaFisica();

		$this->campoOculto( "pessoaFj", $this->pessoaFj );
		$this->campoOculto( "cod_agenda", $this->cod_agenda );

		$this->campoTexto( "nm_agenda", "Nome da Agenda", $this->nm_agenda, 50, 50 );

		$this->campoLista( "publica", "Pública", array( "N&atilde;o", "Sim" ), $this->publica );

		$this->campoLista( "envia_alerta", "Envia Alerta", array( "N&atilde;o", "Sim" ), $this->envia_alerta );

		$i = 0;
		if( $this->ref_ref_cod_pessoa_own )
		{
			list( $nome ) = $objPessoa->queryRapida( $this->ref_ref_cod_pessoa_own, "nome" );
			$this->campoTextoInv( "editor{$i}", "Editores", $nome, 50, 255 );
		}

		$lista = array( "Pesquise a pessoa clicando no botao ao lado" );

		if ($this->cod_agenda)
		{
			$db->Consulta( "SELECT ref_ref_cod_pessoa_fj FROM agenda_responsavel WHERE ref_cod_agenda = '{$this->cod_agenda}'" );
			while ( $db->ProximoRegistro() )
			{
				$i++;
				list( $idpes ) = $db->Tupla();
				list( $nome ) = $objPessoa->queryRapida( $idpes, "nome" );
				$this->campoTextoInv( "editor{$i}", "Editores", $nome, 50, 255, false, false, false, false, "<a href=\"agenda_admin_cad.php?cod_agenda={$this->cod_agenda}&edit_rem=$idpes\">remover</a>" );
			}
			//$this->campoListaPesq( "novo_editor", "Novo Editor", $lista, 0, "pesquisa_funcionario.php", false, false, false, "&nbsp; &nbsp; &nbsp; <a href=\"javascript:var idpes = document.getElementById('novo_editor').value; if( idpes != 0 ) { document.location.href='agenda_admin_cad.php?cod_agenda={$this->cod_agenda}&edit_add=' + idpes; } else { alert( 'Selecione a pessoa clicando na imagem da Lupa' ); }\">Adicionar</a>" );
			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit( 1 );
			$parametros->adicionaCampoSelect( "novo_editor", "ref_cod_pessoa_fj", "nome" );
			$this->campoListaPesq( "novo_editor", "Novo Editor", $lista, 0, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
			//$this->campoLista( "edit_add", "Editores", $lista, "", "", false, "", "<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'pesquisa_funcionario_lst.php?campos=$serializedcampos\'></iframe>' );\">", false, true );
			unset( $campos );
		}
		else
		{
			//$this->campoListaPesq( "dono", "Dono da agenda", $lista, 0, "pesquisa_funcionario.php" );
			$parametros = new clsParametrosPesquisas();
			$parametros->setSubmit( 0 );
			$parametros->adicionaCampoSelect( "dono", "ref_cod_pessoa_fj", "nome" );
			$this->campoListaPesq( "dono", "Dono da agenda", $lista, 0, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
			//$this->campoLista( "dono", "Dono da agenda", $lista, "", "", false, "", "<img id='lupa' src=\"imagens/lupa.png\" border=\"0\" onclick=\"showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'pesquisa_funcionario_lst.php?campos=$serializedcampos\'></iframe>' );\">", false, true );
		}
	}

	function Novo()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$campos = "";
		$values = "";
		$db = new clsBanco();

		if($this->nm_agenda)
		{
			if( is_string( $this->nm_agencia ) )
			{
				$campos .= ", nm_agencia";
				$values .= ", '{$this->nm_agencia}'";
			}

			if( is_numeric( $this->dono ) )
			{
				if( $this->dono )
				{
					$campos .= ", ref_ref_cod_pessoa_own";
					$values .= ", '{$this->dono}'";
				}
				else
				{
					$campos .= ", ref_ref_cod_pessoa_own";
					$values .= ", NULL";
				}
			}

			if( is_numeric( $this->publica ) )
			{
				$campos .= ", publica";
				$values .= ", '{$this->publica}'";
			}

			if( is_numeric( $this->envia_alerta ) )
			{
				$campos .= ", envia_alerta";
				$values .= ", '{$this->envia_alerta}'";
			}

			$db->Consulta( "INSERT INTO portal.agenda( ref_ref_cod_pessoa_cad, data_cad, nm_agenda $campos) VALUES( '{$this->pessoa_logada}', NOW(), '{$this->nm_agenda}' $values)" );
			$id_agenda = $db->insertId( "portal.agenda_cod_agenda_seq" );

			$db->Consulta( "INSERT INTO portal.agenda_responsavel( ref_ref_cod_pessoa_fj, ref_cod_agenda ) VALUES( '{$this->pessoa_logada}', '{$id_agenda}' )" );
			header( "location: agenda_admin_lst.php" );
			die();
			return true;
		}
		else
		{
			return false;
		}
	}

	function Editar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$set = "";
		$db = new clsBanco();

		if( is_numeric( $this->cod_agenda ) )
		{
			if( is_string( $this->nm_agenda ) )
			{
				$set .= ", nm_agenda = '{$this->nm_agenda}'";
			}

			if( is_numeric( $this->publica ) )
			{
				$set .= ", publica = '{$this->publica}'";
			}

			if( is_numeric( $this->envia_alerta ) )
			{
				$set .= ", envia_alerta = '{$this->envia_alerta}'";
			}

			$db->Consulta( "UPDATE portal.agenda SET ref_ref_cod_pessoa_exc = '{$this->pessoa_logada}', data_edicao = NOW() $set WHERE cod_agenda = '{$this->cod_agenda}'" );
			header( "location: agenda_admin_lst.php" );
			die();
			return true;
		}
		else
		{
			$this->mensagem = "Codigo de Diaria invalido!";
			return false;
		}

		return true;
	}

	function Excluir()
	{
		if( is_numeric( $this->cod_agenda ) )
		{
			$db = new clsBanco();

			$db->Consulta( "DELETE FROM portal.agenda_compromisso WHERE ref_cod_agenda={$this->cod_agenda}" );
			$db->Consulta( "DELETE FROM portal.agenda_responsavel WHERE ref_cod_agenda={$this->cod_agenda}" );

			$db->Consulta( "DELETE FROM portal.agenda WHERE cod_agenda={$this->cod_agenda}" );
			header( "location: agenda_admin_lst.php" );
			die();
			return true;
		}
		$this->mensagem = "Codigo da Agenda inválido!";
		return false;
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();
?>