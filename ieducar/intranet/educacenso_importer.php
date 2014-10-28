<?php
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/EducacensoParser.inc.php");
require_once ("include/pmieducar/clsPermissoes.inc.php");
require_once ("include/public/geral.inc.php");

require_once ('include/localizacaoSistema.php');

class clsIndex extends clsBase {

    function Formular() {
        $this->SetTitulo ( "Educacenso - Importa&ccedil;&atilde;o" );
        $this->processoAp = "21300";
        $this->addEstilo ( "localizacaoSistema" );
    }
}

class indice extends clsCadastro {
    var $pessoa_logada;
    var $id_instituicao = null;

    function Inicializar() {
        session_start ();
        $this->pessoa_logada = $_SESSION ['id_pessoa'];
        session_write_close ();
        
        if (array_key_exists ( 'cod_instituicao', $_POST ))
            $this->id_instituicao = $_POST ['cod_instituicao'];
    }

    function Gerar() {
        $obj_permissoes = new clsPermissoes ();
        $nivel_usuario = $obj_permissoes->nivel_acesso ( $this->pessoa_logada );
        
        if ($nivel_usuario == 1) {
            
            if (array_key_exists ( 'arquivo_educacenso', $_FILES ) && $this->id_instituicao) {
                $parser = new EducacensoParser($this->id_instituicao, $_FILES['arquivo_educacenso']['tmp_name'], $this->pessoa_logada);
                $results = $parser->run();
                $this->campoMemo("resultados", "Resultados", implode("\n", $results), 120, 100);
            } else {
                $opcoes = array (
                        "" => "Selecione uma institui&ccedil;&atilde;o" 
                );
                $instituicoes = new clsPmieducarInstituicao ();
                $instituicoes->setCamposLista ( "cod_instituicao, nm_instituicao" );
                $instituicoes->setOrderby ( "nm_instituicao ASC" );
                $lista = $instituicoes->lista ( null, null, null, null, null, null, null, null, null, null, null, null, null, 1 );
                if (is_array ( $lista ) && count ( $lista )) {
                    foreach ( $lista as $registro ) {
                        $opcoes ["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
                    }
                }
                
                $this->campoLista ( "cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, '');
                $this->campoArquivo ( 'arquivo_educacenso', 'Arquivo', '', '60', 'Arquivo exportado pelo sistema Educacenso.', FALSE );
            }
        }
    }
}

// Instancia objeto de página
$pagina = new clsIndex ();

// Instancia objeto de conteúdo
$miolo = new indice ();

// Atribui o conteúdo à página
$pagina->addForm ( $miolo );

// Gera o código HTML
$pagina->MakeAll ();
?>
