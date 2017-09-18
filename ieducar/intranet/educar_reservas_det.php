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
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Reservas" );
		$this->processoAp = "609";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_reserva;
	var $ref_usuario_libera;
	var $ref_usuario_cad;
	var $ref_cod_cliente;
	var $data_reserva;
	var $data_prevista_disponivel;
	var $data_retirada;
	var $ref_cod_exemplar;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Reservas - Detalhe";
		

		$this->cod_reserva=$_GET["cod_reserva"];

		$tmp_obj = new clsPmieducarReservas( $this->cod_reserva );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: educar_reservas_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarExemplar" ) )
		{
			$obj_ref_cod_exemplar = new clsPmieducarExemplar( $registro["ref_cod_exemplar"] );
			$det_ref_cod_exemplar = $obj_ref_cod_exemplar->detalhe();

			if ( class_exists( "clsPmieducarAcervo" ) )
			{
				$acervo = $det_ref_cod_exemplar["ref_cod_acervo"];
				$obj_acervo = new clsPmieducarAcervo($acervo);
				$det_acervo = $obj_acervo->detalhe();
				$titulo_exemplar = $det_acervo["titulo"];
			}
		}
		else
		{
			$registro["ref_cod_exemplar"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarExemplar\n-->";
		}

		if( class_exists( "clsPmieducarCliente" ) )
		{
			$obj_cliente = new clsPmieducarCliente( $registro["ref_cod_cliente"] );
			$det_cliente = $obj_cliente->detalhe();
			$ref_idpes = $det_cliente["ref_idpes"];
			$obj_pessoa = new clsPessoa_($ref_idpes);
			$det_pessoa = $obj_pessoa->detalhe();
			$registro["ref_cod_cliente"] = $det_pessoa["nome"];
		}
		else
		{
			$registro["ref_cod_cliente"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCliente\n-->";
		}

		if( $registro["ref_cod_cliente"] )
		{
			$this->addDetalhe( array( "Cliente", "{$registro["ref_cod_cliente"]}") );
		}
		if( $titulo_exemplar )
		{
			$this->addDetalhe( array( "Obra", "{$titulo_exemplar}") );
		}
		if( $registro["ref_cod_exemplar"] )
		{
			$this->addDetalhe( array( "Tombo", "{$registro["ref_cod_exemplar"]}") );
		}
		if( $registro["data_reserva"] )
		{
			$this->addDetalhe( array( "Data Reserva", dataFromPgToBr( $registro["data_reserva"], "d/m/Y" ) ) );
		}
		if( $registro["data_prevista_disponivel"] )
		{
			$this->addDetalhe( array( "Data Prevista Dispon&iacute;vel", dataFromPgToBr( $registro["data_prevista_disponivel"], "d/m/Y" ) ) );
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 609, $this->pessoa_logada, 11 ) )
		{
			$this->url_novo = "educar_reservas_login_cad.php";
		}

		$this->url_cancelar = "educar_reservas_lst.php";
		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_biblioteca_index.php"                  => "i-Educar - Biblioteca",
         ""                                  => "Detalhe da reserva"
    ));
    $this->enviaLocalizacao($localizacao->montar());		
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>