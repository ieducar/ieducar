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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Pagamento Multa" );
		$this->processoAp = "622";
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

	var $cod_pagamento_multa;
	var $ref_usuario_cad;
	var $ref_cod_cliente;
	var $ref_cod_cliente_tipo;
	var $valor_pago;
	var $data_cadastro;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Pagamento Multa - Detalhe";
		


		$this->ref_cod_cliente 		= $_GET["cod_cliente"];
		$this->ref_cod_cliente_tipo = $_GET["cod_cliente_tipo"];

		if(!$this->ref_cod_cliente || !$this->ref_cod_cliente_tipo)
			header("Location: educar_pagamento_multa_lst.php");

		if( class_exists( "clsPmieducarCliente" ) )
		{
			$obj_tipo = new clsPmieducarClienteTipo( $this->ref_cod_cliente_tipo );
			$det_tipo = $obj_tipo->detalhe();
			$obj_ref_cod_cliente = new clsPmieducarCliente();
			$lst_ref_cod_cliente = $obj_ref_cod_cliente->listaCompleta( $this->ref_cod_cliente, null, null, null, null, null, null, null, null, null, 1, null, null, $this->ref_cod_cliente_tipo );
			if ( $lst_ref_cod_cliente ) {
				foreach ( $lst_ref_cod_cliente as $registro ) {
					$this->addDetalhe( array( "Cliente", "{$registro["nome"]}") );
					$this->addDetalhe( array( "Login", "{$registro["login"]}") );

					$obj_divida = new clsPmieducarExemplarEmprestimo();
					$lst_divida = $obj_divida->lista( null, null, null, $registro["cod_cliente"], null, null, null, null, null, null, null, null, true );
					if( $lst_divida ) {
						$tabela = "<TABLE>
								       <TR align=center>
								           <TD bgcolor=#A1B3BD><B>Data de Devolu&ccedil;&atilde;o</B></TD>
								           <TD bgcolor=#A1B3BD><B>T&iacute;tulo</B></TD>
								           <TD bgcolor=#A1B3BD><B>Biblioteca</B></TD>
								           <TD bgcolor=#A1B3BD><B>Valor</B></TD>
								       </TR>";
						$cont  = 0;
						$total = 0;
						foreach ( $lst_divida as $divida ) {
							$total += $divida["valor_multa"];
							if ( ($cont % 2) == 0 )
								$color = " bgcolor=#E4E9ED ";
							else
								$color = " bgcolor=#FFFFFF ";
							$obj_exemplar = new clsPmieducarExemplar( $divida["ref_cod_exemplar"] );
							$det_exemplar = $obj_exemplar->detalhe();
							if( $det_exemplar ) {
								$obj_acervo = new clsPmieducarAcervo( $det_exemplar["ref_cod_acervo"] );
								$det_acervo = $obj_acervo->detalhe();
								$obj_bib	= new clsPmieducarBiblioteca( $det_acervo["ref_cod_biblioteca"] );
								$det_bib	= $obj_bib->detalhe();
							}
							$corpo .= "<TR>
										    <TD {$color} align=left>".dataFromPgToBr( $divida["data_devolucao"] )."</TD>
										    <TD {$color} align=left>{$det_acervo["titulo"]}</TD>
										    <TD {$color} align=left>{$det_bib["nm_biblioteca"]}</TD>
										    <TD {$color} align=right>"."R$".number_format( $divida["valor_multa"], 2, ",", "." )."</TD>
										</TR>";
							$cont++;
						}
						$tabela .= $corpo;
						if ( ($cont % 2) == 0 )
							$color = " bgcolor=#E4E9ED ";
						else
							$color = " bgcolor=#FFFFFF ";
						$tabela .= "<TR>
										<TD {$color} colspan=3 align=right > <B>Total</B> </TD>
										<TD {$color} align=right > <B>"."R$".number_format( $total, 2, ",", "." )."</B> </TD>
									</TR>";
						$obj_multa  = new clsPmieducarPagamentoMulta( null, null, $registro["cod_cliente"], null, null, $det_tipo["ref_cod_biblioteca"] );
						$total_pago =  $obj_multa->totalPago();
						$cont++;
						if ( ($cont % 2) == 0 )
							$color = " bgcolor=#E4E9ED ";
						else
							$color = " bgcolor=#FFFFFF ";
						$tabela .= "<TR>
										<TD {$color} colspan=3 align=right > <B>Total Pago</B> </TD>
										<TD {$color} align=right > <B>"."R$".number_format( $total_pago, 2, ",", "." )."</B> </TD>
									</TR>";
						$cont++;
						if ( ($cont % 2) == 0 )
							$color = " bgcolor=#E4E9ED ";
						else
							$color = " bgcolor=#FFFFFF ";
						$obj_tot = new clsPmieducarExemplarEmprestimo();
						$lst_tot = $obj_tot->listaDividaPagamentoCliente( $registro["cod_cliente"], null, null, null, $det_tipo["ref_cod_biblioteca"] );
						$total_bib = 0;
						if ( $lst_tot ) {
							foreach ( $lst_tot as $total_reg ) {
								$total_bib = $total_reg["valor_multa"];
							}
						}
						$tabela .= "<TR>
										<TD {$color} colspan=3 align=right > <B>Total (Biblioteca)</B> </TD>
										<TD {$color} align=right > <B>"."R$".number_format( $total_bib, 2, ",", "." )."</B> </TD>
									</TR>";
						$cont++;
						if ( ($cont % 2) == 0 )
							$color = " bgcolor=#E4E9ED ";
						else
							$color = " bgcolor=#FFFFFF ";
						$tabela .= "<TR>
										<TD {$color} colspan=3 align=right > <B>Total Devido</B> </TD>
										<TD {$color} align=right > <B>"."R$".number_format( ( $total - $total_pago ), 2, ",", "." )."</B> </TD>
									</TR>";
						$tabela .= "</TABLE>";
						if ( $tabela )
								$this->addDetalhe( array( "Multa", "{$tabela}") );
					}
					$this->ref_cod_cliente = $registro["cod_cliente"];
				}
			}
		}
		else
		{
			$registro["ref_cod_cliente"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCliente\n-->";
		}

		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 622, $this->pessoa_logada, 11 ) )
		{
			$this->caption_novo = "Pagar";
			$this->url_novo = "educar_pagamento_multa_cad.php?cod_cliente={$this->ref_cod_cliente}&cod_biblioteca={$det_tipo["ref_cod_biblioteca"]}";
			$this->url_editar = false;
		}

		$this->url_cancelar = "educar_pagamento_multa_lst.php";
		$this->largura = "100%";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_biblioteca_index.php"                  => "i-Educar - Biblioteca",
         ""                                  => "Detalhe da d&iacute;vida"
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