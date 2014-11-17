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
    var $ano_destino = null;
    var $carga_horaria_professor = null;
    var $carga_horaria_aluno = null;

    function Inicializar() {
        session_start ();
        $this->pessoa_logada = $_SESSION ['id_pessoa'];
        session_write_close ();
        
        if (array_key_exists('cod_instituicao', $_POST ))
            $this->id_instituicao = $_POST ['cod_instituicao'];
        
        if (array_key_exists('ano_destino', $_POST)) {
            $ano = intval($_POST['ano_destino']);
            $ano_atual = intval(date('Y'));
            $ano_max = $ano_atual + 4;
            if (($ano > $ano_max) || ($ano < $ano_atual)) {
                $this->erros[] = "Ano de destino deve ser de $ano_atual a $ano_max, n&atilde;o $ano.";
            } else {
                $this->ano_destino = $ano;
            }
        }

        if (array_key_exists('carga_horaria_docente', $_POST)) {
            list($hours, $minutes) = sscanf($_POST['carga_horaria_docente'], '%02d:%02d');
            if (($minutes > 59) || ($hours > 12)) {
                $this->erros[] = sprintf("Hora inv&aacute;lida (docente): %02d:%02d.", $hours, $minutes);
            } else {
                $this->carga_horaria_professor = $hours + ($minutes / 60);
            }
        }
        
        if (array_key_exists('carga_horaria_aluno', $_POST)) {
            list($hours, $minutes) = sscanf($_POST['carga_horaria_aluno'], '%02d:%02d');
            if (($minutes > 59) || ($hours > 12)) {
                $this->erros[] = sprintf("Hora inv&aacute;lida (aluno): %02d:%02d.", $hours, $minutes);
            } else {
                $this->carga_horaria_aluno = $hours + ($minutes / 60);
            }
        }       
        
    }

    function Gerar() {
        $obj_permissoes = new clsPermissoes ();
        $nivel_usuario = $obj_permissoes->nivel_acesso ( $this->pessoa_logada );
        
        if ($nivel_usuario == 1) {
            if (array_key_exists('arquivo_educacenso', $_FILES)
                    && file_exists($_FILES['arquivo_educacenso']['tmp_name'])
                    && $this->id_instituicao
                    && $this->carga_horaria_aluno
                    && $this->carga_horaria_professor ) {
                        
                $parser = new EducacensoParser($this->id_instituicao, $_FILES['arquivo_educacenso']['tmp_name'], $this->pessoa_logada, $this->ano_destino, $this->carga_horaria_professor, $this->carga_horaria_aluno);
                $results = $parser->run();
                $this->campoMemo("resultados", "Resultados", implode("\n", $results), 120, 100);
            } else {
                if ($this->erros) {
                    foreach($this->erros as $e) {
                        $this->prependOutput("<P class='error'>" . $e . "</P>");
                    }
                }
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
                
                $this->campoLista("cod_instituicao", "Institui&ccedil;&atilde;o", $opcoes, '');
                $this->campoNumero("ano_destino", "Ano de destino", date('Y'), 4, 4, True);
                $this->campoHora("carga_horaria_aluno", "Carga horária padrão aluno/dia", "04:00", True);
                $this->campoHora("carga_horaria_docente", "Carga horária padrão docente/dia", "08:00", True);
                $this->campoArquivo('arquivo_educacenso', 'Arquivo', '', '60', 'Arquivo exportado pelo sistema Educacenso.', FALSE );
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
