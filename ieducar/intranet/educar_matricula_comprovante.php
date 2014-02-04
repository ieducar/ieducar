<?php

require_once '../includes/bootstrap.php';
require_once 'include/pmieducar/clsPmieducarAluno.inc.php';
require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
require_once 'include/pmieducar/clsPmieducarSerie.inc.php';
require_once 'include/pmieducar/clsPmieducarTurma.inc.php';

$matricula = new clsPmieducarMatricula($_GET['ref_cod_matricula']);
$matricula = $matricula->detalhe();

$escola = new clsPmieducarEscola($matricula['ref_ref_cod_escola']);
$escola = $escola->detalhe();

$serie = new clsPmieducarSerie($matricula['ref_ref_cod_serie']);
$serie = $serie->detalhe();

$matriculaTurma = new clsPmieducarMatriculaTurma();
$matriculaTurma = $matriculaTurma->lista($matricula['cod_matricula'], NULL, NULL,
  NULL, NULL, NULL, NULL, NULL, 1);

if ($matriculaTurma) {
  $matriculaTurma = array_shift($matriculaTurma);
  $turma = new clsPmieducarTurma($matriculaTurma['ref_cod_turma']);
  $turma = $turma->detalhe();
  $turma = $turma['nm_turma'];
}
?>
<html version="-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
  <title>Comprovante de matrícuka</title>
  <script type="text/javascript">
    window.onload = function() {
      window.print();
    }
  </script>
</head>
<body>
  <table>
    <tr>
      <td width="80"><img src="../web/images/brasao.gif" width="60" /></td>
      <td>
        Secretaria Municipal de Educação<br />
        Comprovante de matrícula <?php print $matricula['cod_matricula'] ?>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>Aluno</td>
      <td><?php print $matricula['nome'] ?></td>
    </tr>
    <?php if (NULL != $nomeRes): ?>
    <tr>
      <td>Nome do responsável</td>
      <td><?php print $nomeRes ?></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td>Escola</td>
      <td><?php print $escola['nome'] ?></td>
    </tr>
    <tr>
      <td>Ano/série</td>
      <td><?php print $serie['nm_serie'] ?></td>
    </tr>
    <tr>
      <td>Turma</td>
      <td><?php print $turma ?></td>
    </tr>
  </table>
</body>
</html>