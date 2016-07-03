<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once ("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Ocorr&ecirc;ncia Disciplinar" );
		$this->processoAp = "580";
                $this->addEstilo( "localizacaoSistema" );
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	var $cod_tipo_ocorrencia_disciplinar;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $descricao;
	var $max_ocorrencias;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Tipo Ocorr&ecirc;ncia Disciplinar - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Tipo Ocorr&ecirc;ncia Disciplinar",
			"M&aacute;ximo Ocorr&ecirc;ncias"
		);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
			$lista_busca[] = "Institui&ccedil;&atilde;o";

		$this->addCabecalhos($lista_busca);

		// Filtros de Foreign Keys
//		$get_escola = true;
		include("include/pmieducar/educar_campo_lista.php");

		// outros Filtros
		$this->campoTexto( "nm_tipo", "Tipo Ocorr&ecirc;ncia Disciplinar", $this->nm_tipo, 30, 255, false );
		//$this->campoNumero( "max_ocorrencias", "Max Ocorrencias", $this->max_ocorrencias, 15, 255, false );


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_tipo_ocorrencia_disciplinar = new clsPmieducarTipoOcorrenciaDisciplinar();
		$obj_tipo_ocorrencia_disciplinar->setOrderby( "nm_tipo ASC" );
		$obj_tipo_ocorrencia_disciplinar->setLimite( $this->limite, $this->offset );

		$lista = $obj_tipo_ocorrencia_disciplinar->lista(
			null,
			null,
			null,
			$this->nm_tipo,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao
		);

		$total = $obj_tipo_ocorrencia_disciplinar->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}
				$lista_busca = array(
					"<a href=\"educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar={$registro["cod_tipo_ocorrencia_disciplinar"]}\">{$registro["nm_tipo"]}</a>",
					"<a href=\"educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar={$registro["cod_tipo_ocorrencia_disciplinar"]}\">{$registro["max_ocorrencias"]}</a>"
				);

				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_tipo_ocorrencia_disciplinar_det.php?cod_tipo_ocorrencia_disciplinar={$registro["cod_tipo_ocorrencia_disciplinar"]}\">{$registro["ref_cod_instituicao"]}</a>";
				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_tipo_ocorrencia_disciplinar_lst.php", $total, $_GET, $this->nome, $this->limite );

		if( $obj_permissoes->permissao_cadastra( 580, $this->pessoa_logada,3 ) )
		{
			$this->acao = "go(\"educar_tipo_ocorrencia_disciplinar_cad.php\")";
			$this->nome_acao = "Novo";
		}
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Tipos de Ocorr�ncias Disciplinares"
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