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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar Devolu&ccedil;&atilde;o" );
		$this->processoAp = "628";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $cod_emprestimo;
	var $ref_usuario_devolucao;
	var $ref_usuario_cad;
	var $ref_cod_cliente;
	var $ref_cod_exemplar;
	var $data_retirada;
	var $data_devolucao;
	var $valor_multa;

	var $ref_cod_biblioteca;

	var $dias_da_semana = array( 'Sun' => 1, 'Mon' => 2, 'Tue' => 3, 'Wed' => 4, 'Thu' => 5, 'Fri' => 6, 'Sat' => 7 );

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_emprestimo = $_GET["cod_emprestimo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 628, $this->pessoa_logada, 11,  "educar_exemplar_devolucao_lst.php" );

		if( is_numeric( $this->cod_emprestimo ) )
		{

			$obj = new clsPmieducarExemplarEmprestimo( $this->cod_emprestimo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
			}
		}
		$this->url_cancelar = "educar_exemplar_devolucao_lst.php";
		$this->nome_url_cancelar = "Cancelar";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_biblioteca_index.php"                  => "i-Educar - Biblioteca",
         ""        => "Realizar devolu&ccedil;&atilde;o"             
    ));
    $this->enviaLocalizacao($localizacao->montar());

		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_emprestimo", $this->cod_emprestimo );

		$this->data_retirada = dataFromPgToBr($this->data_retirada, "Y-m-d");

		$obj_exemplar = new clsPmieducarExemplar($this->ref_cod_exemplar);
		$det_exemplar = $obj_exemplar->detalhe();
		$cod_acervo = $det_exemplar["ref_cod_acervo"];

		$obj_acervo = new clsPmieducarAcervo( $cod_acervo );
		$det_acervo = $obj_acervo->detalhe();
		// tipo de exemplar
		$cod_exemplar_tipo = $det_acervo["ref_cod_exemplar_tipo"];
		$titulo_obra = $det_acervo["titulo"];
		$this->ref_cod_biblioteca = $det_acervo["ref_cod_biblioteca"];

		$this->campoOculto( "ref_cod_biblioteca", $this->ref_cod_biblioteca );

		$obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
		$det_biblioteca = $obj_biblioteca->detalhe();
		// valor da multa da biblioteca por dia
		$valor_multa_biblioteca = $det_biblioteca["valor_multa"];

		$obj_cliente_tipo_cliente = new clsPmieducarClienteTipoCliente();
		$lst_cliente_tipo_cliente = $obj_cliente_tipo_cliente->lista(null,$this->ref_cod_cliente,null,null,null,null,null,null,$this->ref_cod_biblioteca );
		if( is_array( $lst_cliente_tipo_cliente ) && count( $lst_cliente_tipo_cliente ) )
		{
			$det_cliente_tipo_cliente = array_shift($lst_cliente_tipo_cliente);
			// tipo do cliente
			$cod_cliente_tipo = $det_cliente_tipo_cliente["ref_cod_cliente_tipo"];

			$obj_cliente_tipo_exemplar_tipo = new clsPmieducarClienteTipoExemplarTipo( $cod_cliente_tipo, $cod_exemplar_tipo );
			$det_cliente_tipo_exemplar_tipo = $obj_cliente_tipo_exemplar_tipo->detalhe();
			// qtde de dias disponiveis para emprestimo
			$dias_emprestimo = $det_cliente_tipo_exemplar_tipo["dias_emprestimo"];
		}

		$data_entrega = date("Y-m-d", strtotime("$this->data_retirada +".$dias_emprestimo." days"));

		//---------------------DIAS FUNCIONAMENTO----------------------//
		$obj_biblioteca_dia = new clsPmieducarBibliotecaDia();
		$lst_biblioteca_dia = $obj_biblioteca_dia->lista($this->ref_cod_biblioteca);
		if( is_array( $lst_biblioteca_dia ) && count( $lst_biblioteca_dia ) )
		{
			foreach ($lst_biblioteca_dia AS $dia_semana)
			{
				// dias de funcionamento da biblioteca
				$biblioteca_dias_semana[] = $dia_semana["dia"];
			}
		}
		// salva somente os dias que n se repetem ( dias de n funcionamento)
		$biblioteca_dias_folga = array_diff($this->dias_da_semana, $biblioteca_dias_semana);
		// inverte as relacoes entre chaves e valores ( de $variavel["Sun"] => 1, para $variavel[1] => "Sun")
		$biblioteca_dias_folga = array_flip($biblioteca_dias_folga);

		//---------------------DIAS FERIADO----------------------//
		$obj_biblioteca_feriado = new clsPmieducarBibliotecaFeriados();
		$lst_biblioteca_feriado = $obj_biblioteca_feriado->lista( null, $this->ref_cod_biblioteca );
		if( is_array( $lst_biblioteca_feriado ) && count( $lst_biblioteca_feriado ) )
		{
			foreach ($lst_biblioteca_feriado AS $dia_feriado)
			{
				// dias de feriado da biblioteca
				$biblioteca_dias_feriado[] = dataFromPgToBr($dia_feriado["data_feriado"], "D Y-m-d");
			}
		}

		// devido a comparacao das datas, é necessario mudar o formato da data
		$data_entrega = dataFromPgToBr($data_entrega, "D Y-m-d");

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

		$data_entrega = dataFromPgToBr($data_entrega, "Y-m-d");

		// verifica se houve atraso na devolucao do exemplar
		if ($data_entrega < date('Y-m-d'))
		{
			$dias_atraso = (int)((time() - strtotime($data_entrega)) / 86400);
			$dias_atraso = $dias_atraso > 0 ? $dias_atraso : 0;
			
			$valor_divida = $dias_atraso * $valor_multa_biblioteca;
			$valor_divida = number_format($valor_divida, 2,",",".");
			$data_entrega = dataFromPgToBr($data_entrega, "d/m/Y");
		}

		// foreign keys
		$obj_cliente = new clsPmieducarCliente($this->ref_cod_cliente);
		$det_cliente = $obj_cliente->detalhe();
		$ref_idpes = $det_cliente["ref_idpes"];
		$obj_pessoa = new clsPessoa_($ref_idpes);
		$det_pessoa = $obj_pessoa->detalhe();
		$nm_pessoa = $det_pessoa["nome"];

		$this->campoTextoInv("nm_pessoa", "Cliente", $nm_pessoa, 30, 255);
		$ref_cod_exemplar_ = $this->ref_cod_exemplar;
		$this->campoTextoInv("ref_cod_exemplar_", "Tombo", $ref_cod_exemplar_, 15, 50);
		$this->campoOculto( "ref_cod_exemplar", $this->ref_cod_exemplar );
		$this->campoTextoInv("titulo_obra", "Obra", $titulo_obra, 30, 255);

		@session_start(); 
			$reload = $_SESSION['reload'];
		@session_write_close();

		if ($valor_divida && !$reload )
		{
			$this->valor_multa = $valor_divida;
			$this->campoMonetario("valor_divida", "Valor Multa", $valor_divida, 8, 8,false,'','','',true);
			$this->campoOculto( "valor_multa", $this->valor_multa );

			$reload = 1;
			@session_start();
				$_SESSION['reload'] = $reload;
			@session_write_close();

			echo "<script>
				if(!confirm('Atraso na devolução do exemplar ($dias_atraso dias)! \\n Data prevista para a entrega: $data_entrega \\n Valor total da multa: R$$valor_divida \\n Deseja adicionar a multa?'))
					window.location = 'educar_exemplar_devolucao_cad.php?cod_emprestimo={$this->cod_emprestimo}';
			</script>";
		}
		elseif ($valor_divida && $reload )
		{
			echo "<script> alert('Valor da multa ignorado!'); </script>";
			$valor_divida = '0,00';
			$this->campoMonetario("valor_divida", "Valor Multa", $valor_divida, 8, 8,false,'','','',true);
			$this->campoOculto( "valor_multa", $this->valor_multa );
		}
	}

	function Novo()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 628, $this->pessoa_logada, 11,  "educar_exemplar_devolucao_lst.php" );

		$this->valor_multa = urldecode($this->valor_multa);
		$this->valor_multa = str_replace(".","",$this->valor_multa);
		$this->valor_multa = str_replace(",",".",$this->valor_multa);

//		echo $this->cod_emprestimo." / ".$this->pessoa_logada." / ".date('Y-m-d')." / ".$this->valor_multa;die;

		$obj_situacao = new clsPmieducarSituacao();
		$lst_situacao = $obj_situacao->lista(null,null,null,null,2,null,1,0,null,null,null,null,1,$this->ref_cod_biblioteca);
		if( is_array( $lst_situacao ) && count( $lst_situacao ) )
		{
			$det_situacao = array_shift($lst_situacao);
			$cod_situacao = $det_situacao["cod_situacao"];
		}
		else
		{
			echo "<script> alert('ERRO - Não foi possível encontrar a situação DISPONÍVEL da biblioteca utilizada!'); </script>";
			return false;

		}

		$obj = new clsPmieducarExemplarEmprestimo( $this->cod_emprestimo, $this->pessoa_logada, null, null, null, null, date('Y-m-d'), $this->valor_multa);
		$editou = $obj->edita();
		if( $editou )
		{
			$obj = new clsPmieducarExemplar( $this->ref_cod_exemplar, null, null, null, $cod_situacao, $this->pessoa_logada, null, null, null, null, null, 1 );
			$editou = $obj->edita();
			if (!$editou)
			{
				$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
				echo "<!--\nErro ao cadastrar clsPmieducarSituacao\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( {$this->ref_cod_exemplar} ) && is_numeric( {$cod_situacao} )\n-->";
				return false;
			}

			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_exemplar_devolucao_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarExemplarEmprestimo\nvalores obrigatorios\nis_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_cliente )\n-->";
		return false;
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