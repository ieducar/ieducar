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
 * @license   @@license@@
 * @package   iEd_Include
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

/**
 * Adiciona zeros a esquerda de um numero
 *
 * @param int $num
 * @param int $digitos
 * @return string
 */
function addLeadingZero($num, $digitos = 2)
{
  if (is_numeric($num)) {
    if ($digitos > 1) {
      for ($i = 1; $i < $digitos; $i++) {
        if ($num < pow(10, $i)) {
          $num = str_repeat('0', $digitos - $i) . $num;
          break;
        }
      }
    }
    return $num;
  }
  return str_repeat('0', $digitos);
}

function add2LeadingZero($num)
{
  return addLeadingZero($num, 3);
}

function calculoIdade($diaNasc, $mesNasc, $anoNasc)
{
  list ($dia,$mes,$ano) = explode('/', date('d/m/Y'));
  $idade = $ano - $anoNasc;
  $idade = (($mes<$mesNasc) OR (($mes == $mesNasc) AND ($dia<$diaNasc))) ? --$idade : $idade;
  return $idade;
}

function idFederal2int($str)
{
  $id_federal = str_replace(".", "", str_replace("-", "", str_replace("/", "", $str)));
  return ereg_replace("^0+", "", $id_federal);
}

function int2CPF($int)
{
  $str = str_repeat('0', 11 - strlen($int)) . $int;
  return substr($str, 0, 3) . '.' . substr($str, 3, 3). '.' . substr($str, 6, 3) . '-' . substr($str, 9, 2);
}

function int2CNPJ($int)
{
  if (strlen($int) < 14) {
    $str = str_repeat('0', 14 - strlen($int)) . $int;
  }
  else {
    $str = $int;
  }
  return substr($str, 0, 2) . '.' . substr($str, 2, 3). '.' . substr($str, 5, 3)
    . '/' . substr( $str, 8, 4 ) . "-" . substr($str, 12, 2);
}

/**
 * Formata um valor num�rico em uma representa��o string de CEP.
 *
 * @param  string|int  $int
 * @return string
 */
function int2CEP($int)
{
  if ($int) {
    $int = (string) str_pad($int, 8, '0', STR_PAD_LEFT);
    return substr($int, 0, 5) . '-' . substr($int, 5, 3);
  }
  else {
    return '';
  }
}

function limpa_acentos( $str_nome )
{
  $procura1   = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');
  $substitui1 = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'c', 'a', 'o', 'o', 'e');

  $procura2   = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');
  $substitui2 = array('A', 'E', 'I', 'O', 'U', 'A', 'E', 'I', 'O', 'U', 'A', 'E', 'I', 'O', 'U', 'C', 'A', 'O', 'E', 'O');

  $str_nome = str_replace($procura1, $substitui1, $str_nome);
  $str_nome = str_replace($procura2, $substitui2, $str_nome);

  return $str_nome;
}

function transforma_minusculo($str_nome)
{
  $nome = strtolower($str_nome);
  $arrayNome = explode(" ", $nome);
  $nome = '';

  foreach ($arrayNome as $parte) {
    if ($parte != 'de' && $parte != 'da' && $parte != 'dos' && $parte != 'do' &&
      $parte != 'das' && $parte != 'e') {
      $nome .= strtoupper(substr($parte, 0, 1)) . substr($parte, 1) . ' ';
    }
    else {
      $nome .= $parte . ' ';
    }
  }

  $procura1   = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');
  $substitui1 = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');

  $nome = str_replace($procura1, $substitui1, $nome);

  return $nome;
}

function quebra_linhas_pdf($str_texto, $qtd_letras_linha = 60)
{
  $comp_comp = str_replace("\n", ' ', $str_texto);
  $tamanho_linha = $qtd_letras_linha;
  $gruda = '';
  $compromisso2 = '';

  while (strlen($comp_comp) > $tamanho_linha) {
    $i = $tamanho_linha;

    while (substr($comp_comp,$i,1) != ' ' && $i > 0) {
      $i--;
    }

    if ($i == 0) $i=$tamanho_linha;

    $compromisso2 .= $gruda . substr($comp_comp,0,$i);
    $comp_comp = substr($comp_comp,$i);
    $gruda = "\n";
  }

  $compromisso2 .= "$gruda $comp_comp";

  $comp_comp = ($compromisso2) ? $compromisso2 : $comp_comp;

  /**
   * @todo Realmente precisa desse \n[espa�o][espa�o]?
   */
  $comp_comp = str_replace("\n  ", "\n",$comp_comp);
  $comp_comp = str_replace("\n ", "\n",$comp_comp);
  return  $comp_comp;
}

/*
 * Funcoes foneticas (segundo as mesmas regras das funcoes do banco PG)
 */
function fonetiza_palavra($palavra)
{
  $i = -1;
  $fonetizado = '';

  /**
   * @todo J� � a terceira vez que esse tipo de opera��o � realizada. Precisa
   *   de refactoring. Ver funcoes.inc.php#transforma_minusculo
   */
  // limpa todas as letras acentuadas e passa para minusculas
  $acentuadasMin = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '?', '�', '?', '�', '�', '�', '�');
  $acentuadasMai = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '?', '�', '?', '�', '?', '�', '�');
  $letras_ok     = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'i', 'o', 'u', 'y', 'y', 'n', 'c');

  $palavra = str_replace($acentuadasMin, $letras_ok, $palavra);
  $palavra = str_replace($acentuadasMai, $letras_ok, $palavra);
  $palavra = strtolower($palavra);

  // Loop nas letras
  while ($i++ < strlen($palavra)) {
    // define as letras
    $letra_atual = substr($palavra, $i, 1);
    $letra_prox  = substr($palavra, $i + 1, 1);
    $letra_prox2 = substr($palavra, $i + 2, 1);

    if ($i) {
      $letra_ante = substr($palavra, $i, -1);
    }
    else {
      $letra_ante = '';
    }

    // numeros - ok
    if (is_numeric($letra_atual)) {
      $fonetizado .= $letra_atual;
      continue;
    }

    // letras iguais - pula
    if ($letra_atual == $letra_prox) {
      continue;
    }

    // A I ou O - ok
    if ($letra_atual == 'a' || $letra_atual == 'i' || $letra_atual == 'o') {
      $fonetizado .= $letra_atual;
      continue;
    }

    // E
    if ($letra_atual == 'e') {
      $fonetizado .= 'i';
      continue;
    }

    // R
    if ($letra_atual == 'r') {
      $fonetizado .= 'h';
      continue;
    }

    // S
    if ($letra_atual == 's') {
      if ($letra_prox != 'a' && $letra_prox != 'e' && $letra_prox != 'i' &&
        $letra_prox != 'o' && $letra_prox != 'u' && $letra_prox != 'y' &&
        strlen($fonetizado) == 0) {
        $fonetizado .= 'is';
        continue;
      }

      if ($letra_prox == 'c' && $letra_prox2 == 'h') {
        continue;
      }

      if ($letra_prox == 'h') {
        $fonetizado .= 'ks';
        $i++;
        continue;
      }

      $fonetizado .= $letra_atual;
      continue;
    }

    // N
    if ($letra_atual == 'n') {
      if ($letra_prox == 'h') {
        $fonetizado .= 'ni';
        continue;
      }

      if ($letra_prox != 'a' && $letra_prox != 'e' && $letra_prox != 'i' &&
        $letra_prox != 'o' && $letra_prox != 'u' && $letra_prox != 'y') {
        $fonetizado .= 'm';
        continue;
      }
      $fonetizado .= $letra_atual;
      continue;
    }

    // L
    if ($letra_atual == 'l') {
      if ($letra_prox == 'h') {
        $fonetizado .= 'li';
        continue;
      }

      if ($letra_prox != 'a' && $letra_prox != 'e' && $letra_prox != 'i' &&
        $letra_prox != 'o' && $letra_prox != 'u' && $letra_prox != 'y') {
        $fonetizado .= 'o';
        continue;
      }
      $fonetizado .= $letra_atual;
    }

    // D
    if ($letra_atual == 'd') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' || $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y' || $letra_prox == 'h') {
        $fonetizado .= 'd';
        continue;
      }

      $fonetizado .= 'di';
      continue;
    }

    // C
    if ($letra_atual == 'c') {
      if ($letra_prox == 'h' && ( $letra_prox2 == 'a' || $letra_prox2 == 'e' ||
        $letra_prox2 == 'i' || $letra_prox2 == 'o' || $letra_prox2 == 'u' ||
        $letra_prox2 == 'y')) {
        $fonetizado .= 'ks';
        continue;
      }

      if ($letra_prox == 'e' || $letra_prox == 'i' || $letra_prox == 'y') {
        $fonetizado .= 's';
        continue;
      }

      if ($letra_prox == 'a' || $letra_prox == 'o' || $letra_prox == 'u') {
        $fonetizado .= 'k';
        continue;
      }
    }

    // M
    if ($letra_atual == 'm') {
      if ($letra_prox != 'n') {
        $fonetizado .= $letra_atual;
        continue;
      }
    }

    // T
    if ($letra_atual == 't') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y' ||
        $letra_prox == 'h') {
        $fonetizado .= $letra_atual;
        continue;
      }

      $fonetizado .= 'ti';
      continue;
    }

    // U
    if ($letra_atual == 'u') {
      $fonetizado .= 'o';
      continue;
    }

    // V
    if ($letra_atual == 'v') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y' ||
        $letra_prox == 'h') {
        $fonetizado .= $letra_atual;
        continue;
      }

      $fonetizado .= 'vi';
      continue;
    }

    // G
    if ($letra_atual == 'g') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y') {

        if ($letra_prox == 'u') {
          if ($letra_prox2 == 'e' || $letra_prox2 == 'i' || $letra_prox2 == 'y') {
            $fonetizado .= 'j';
            $i++;
            continue;
          }
        }

        $fonetizado .= 'j';
        continue;
      }

      $fonetizado .= 'ji';
      continue;
    }

    // B
    if ($letra_atual == 'b') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y' ||
        $letra_prox == 'h') {
        $fonetizado .= $letra_atual;
        continue;
      }
      $fonetizado .= 'bi';
      continue;
    }

    // P
    if ($letra_atual == 'p') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y') {
        $fonetizado .= $letra_atual;
        continue;
      }

      if ($letra_prox == 'h') {
        $fonetizado .= 'f';
        continue;
      }

      $fonetizado .= 'f';
      continue;
    }

    // Z
    if ($letra_atual == 'z') {
      $fonetizado .= 's';
      continue;
    }

    // F
    if ($letra_atual == 'f') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y' ||
        $letra_prox == 'h') {
        $fonetizado .= $letra_atual;
        continue;
      }

      $fonetizado .= 'fi';
      continue;
    }

    // J
    if ($letra_atual == 'j') {
      $fonetizado .= $letra_atual;
      continue;
    }

    // K
    if ($letra_atual == 'k') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y' ||
        $letra_prox == 'h') {
        $fonetizado .= $letra_atual;
        continue;
      }

      $fonetizado .= 'ki';
      continue;
    }

    // Y
    if ($letra_atual == 'y') {
      $fonetizado .= 'i';
      continue;
    }

    // W
    if ($letra_atual == 'w') {
      if ($i == 0) {
        if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
          $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y') {
          $fonetizado .= 'v';
          continue;
        }

        $fonetizado .= 'vi';
        continue;
      }

      if ($letra_ante == 'e' || $letra_ante == 'i') {
        if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
          $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y') {
          $fonetizado .= 'v';
          continue;
        }

        $fonetizado .= 'o';
        continue;
      }

      $fonetizado .= 'v';
      continue;
    }

    // Q
    if ($letra_atual == 'q') {
      if ($letra_prox == 'a' || $letra_prox == 'e' || $letra_prox == 'i' ||
        $letra_prox == 'o' || $letra_prox == 'u' || $letra_prox == 'y') {
        $fonetizado .= 'k';

        if ($letra_prox == 'u') {
          $i++;
        }

        continue;
      }

      $fonetizado .= 'qi';
      continue;
    }

    // X
    if ($letra_atual == 'x') {
      $fonetizado .= 'ks';
      continue;
    }
  }

  return $fonetizado;
}

/**
 * retorna 1 se data1 for maior que a data2,
 * retorna 0 se a data1 for menor que a data2,
 * retorna 2 se forem iguais.
 */
function data_maior($data1, $data2)
{
  $data1 = explode('/', $data1);
  $data2 = explode('/', $data2);

  if($data1[2] > $data2[2]) {
    return 1;
  }
  elseif($data1[2] < $data2[2]) {
    return 0;
  }
  else {
    if($data1[1] > $data2[1]) {
      return 1;
    }
    elseif($data1[1] < $data2[1]) {
      return 0;
    }
    else {
      if($data1[0] > $data2[0]) {
        return 1;
      }
      elseif($data1[0] < $data2[0]) {
        return 0;
      }
      else {
        return 2;
      }
    }
  }
}

function minimiza_capitaliza($str)
{
  $nome = strtolower($str);
  $arrayNome = explode(' ', $nome);
  $nome ='';
  $gruda = '';

  foreach ($arrayNome as $parte) {
    if ($parte != 'de' && $parte != 'da' && $parte != 'dos' && $parte != 'do' &&
      $parte != 'das' && $parte != 'e') {
      $nome .= $gruda . strtoupper(substr($parte,0,1)).substr($parte,1);
    }
    else {
      $nome .= $gruda . $parte;
    }

    $gruda = ' ';
  }

  /**
   * @todo Mais um tratamento de acentos, ver funcoes.inc.php#transforma_minusculo().
   */
  $nome = str_replace(array('�','�','�','�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�'),
                      array('�','�','�','�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�'), $nome );
  return $nome;
}

/**
 * Formata a data para o formato brasileiro
 *
 * @param string $data_original data que ser� transformada
 * @param bool $h_m determina se o a data retornada incluir� hora e minuto
 * @param bool $h_m_s determina se o a data retornada incluir� hora, minuto e segundo
 *
 * @return string
 */
function dataToBrasil($data_original, $h_m = FALSE, $h_m_s = FALSE)
{
  if ($data_original) {
    $arr_data = explode(' ', $data_original);

    $data = date('d/m/Y', strtotime($arr_data[0]));

    if ($h_m) {
      return "{$data} " . substr($arr_data[1], 0, 5);
    }
    elseif ($h_m_s) {
      return "{$data} " . substr($arr_data[1], 0, 8);
    }

    return $data;
  }

  return FALSE;
}

/**
 * Formata a data para o formato do banco
 *
 * @param string $data_original data que ser� transformada
 * @return string
 * @todo $data_original = NULL sempre ser� TRUE. Verificar que c�digo chama
 *   esta fun��o. L�gica falha.
 */
function dataToBanco($data_original, $inicial = NULL)
{
  if ($data_original) {
    $data = explode('/', $data_original);
    if(count($data)) {
      if (is_null($inicial)) {
        return "{$data[2]}-{$data[1]}-{$data[0]}";
      }

      if ($inicial == TRUE) {
        if ($data_original = NULL) {
          return FALSE;
        }
        else {
          return "{$data[2]}-{$data[1]}-{$data[0]} 00:00:00";
        }
      }
      elseif($inicial == FALSE) {
        if ($data_original = NULL) {
          return false;
        }
        else {
          return "{$data[2]}-{$data[1]}-{$data[0]} 23:59:59";
        }
      }
    }
    else {
      return FALSE;
    }
  }

  return FALSE;
}

/**
 * Formata uma data vinda do postgre
 *
 * @param string $data_original data que ser� transformada
 * @return string
 */
function dataFromPgToTime($data_original)
{
  if (strlen($data_original) > 16) {
    $data_original = substr($data_original, 0, 16);
  }

  return strtotime($data_original);
}

/**
 * Formata uma data ISO-8601 no formato do locale pt_BR.
 *
 * O formato ISO-8601 geralmente � utilizado pelos DBMS atuais nos tipos de campos datetime/timestamp.
 * O PostgreSQL utiliza este padr�o.
 *
 * @param string $data_original Data que ser� formatada
 * @param string $formatacao    String de formata��o no padr�o aceito pela fun��o date() do PHP
 * @link  http://www.php.net/date Documenta��o da fun��o PHP date()
 *
 * @return string
 */
function dataFromPgToBr($data_original, $formatacao = 'd/m/Y')
{
  return date($formatacao, dataFromPgToTime($data_original));
}


/**
 * Funcao que troca caracteres acentuados por caracteres extendidos de HTML (para compatibilidade de encode).
 * Ex: � = &aacute;
 * pode substituir na ordem reversa
 *
 * @param string $text
 * @param bool $reverse
 * @return string
 * @todo Onde essa fun��o � chamada? Transforma��o totalmente desnecess�ria.
 */
function extendChars($text, $reverse = FALSE)
{
  $chars = array("�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�",
                 "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�", "�" );
  $extends = array("&Atilde;", "&Acirc;", "&Aacute;", "&Agrave;", "&Auml;", "&Eacute;", "&Ecirc;", "&Egrave;", "&Euml;", "&Iacute;", "&Igrave;", "&Iuml;", "&Icirc;",   "&Ocirc;", "&Otilde;", "&Oacute;", "&Ograve;", "&Ouml;", "&Uacute;", "&Ugrave;", "&Ucirc;", "&Uuml;", "&Yacute;", "&Ntilde;", "&Ccedil;",
                   "&atilde;", "&acirc;", "&aacute;", "&agrave;", "&auml;", "&eacute;", "&ecirc;", "&egrave;", "&euml;", "&iacute;", "&igrave;", "&iuml;", "&icirc;",   "&ocirc;", "&otilde;", "&oacute;", "&ograve;", "&ouml;", "&uacute;", "&ugrave;", "&ucirc;", "&uuml;", "&yacute;", "&ntilde;", "&ccedil;" );

  if ($reverse) {
    return str_replace($extends, $chars, $text);
  }
  else {
    return str_replace($chars, $extends, $text);
  }
}

/**
 * Esta fun��o recebe como par�metros a string que deseja-se quebrar em linhas e o tamanho
 * de caracteres que a linha vai ter, e ela retorna um array com as linhas.
 */
function quebra_linhas($string, $tamanho)
{
  $string_atual = $string;
  $pos = 0;
  $linhas = array();

  while (strlen($string_atual ) > 0) {
    if ($tam < strlen($string_atual)) {
      $linhas[$pos] = retorna_linha($string_atual, $tamanho);
      $string_atual = trim(substr($string_atual, strlen($linhas[$pos])));
    }
    else {
      $linhas[$pos] = retorna_linha($string_atual, strlen($string_atual));
      $string_atual = trim(substr($string_atual, strlen($linhas[$pos])));
    }

    $pos++;
  }

  return $linhas;
}

function retorna_linha($string, $tam)
{
  truncate($string,$tam);
}

/**
 * @todo Casting para string ao inv�s de concatena��o ($str = "" . $int . "";)
 */
function int2IdFederal($int) {
  $str = "" . $int . "";

  if (strlen($str) > 11) {
    if (strlen($int) < 14) {
      $str = str_repeat('0', 14 - strlen($int)) . $int;
    }

    $str = str_replace('.', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('/', '', $str);

    $temp = substr( $str, 0, 2 );

    if (strlen($temp) == 2) {
      $temp .= '.';
    }

    $temp .= substr($str, 2 ,3);

    if (strlen($temp) == 6) {
      $temp .= '.';
    }

    $temp .= substr($str, 5, 3);

    if (strlen($temp) == 10) {
      $temp .= '/';
    }

    $temp .= substr( $str, 8, 4 );

    if (strlen($temp) == 15) {
      $temp .= '-';
    }

    $temp .= substr($str, 12, 2);
    return $temp;
  }
  else {
    if (strlen($int) < 11) {
      $str = str_repeat('0', 11 - strlen($int)) . $int;
    }

    $str = str_replace('.', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('-', '', $str);

    $temp = substr($str, 0, 3);

    if (strlen($temp) == 3) {
      $temp .= '.';
    }

    $temp .= substr($str, 3, 3);

    if (strlen($temp) == 7) {
      $temp .= '.';
    }

    $temp .= substr($str, 6, 3);

    if (strlen($temp) == 11) {
      $temp .= '-';
    }

    $temp .= substr($str, 9, 2);

    return $temp;
  }
}

/**
 * Verifica se o valor � booleano
 * aceita como true:
 * 'true', 't', true, 1, '1', 'yes', 'y', 'sim', 's'
 *
 * @param mixed $val
 * @return bool
 */
function dbBool($val)
{
  return ($val === 'true' || $val === 't' || $val === TRUE || $val == 1 ||
    $val === 'yes' || $val === 'y' || $val === 'sim' || $val === 's');
}

/**
 * Corta uma string caso ela seja maior do que $size caracteres
 * Caso $breakWords seja setado como false, quebrar� a string no �ltimo espaco " "
 * encontrado antes do caracter $size (desde que o retorno at� esse ponto n�o ande mais caracteres do que 25% de $size)
 *
 * @param string $text
 * @param int $size
 * @param bool $breakWords
 * @return string
 */
function truncate($text, $size = 100, $breakWords = FALSE)
{
  if (strlen($text) > $size) {
    $text = substr(trim($text), 0, $size);
    $espaco = strrpos($text, ' ');
    if ($espaco !== FALSE && !$breakWords && $espaco / $size > 0.75) {
      $text = substr( $text, 0, $espaco );
    }
    $text .= "...";
  }
  return $text;
}

/**
 * capitaliza todos os caracteres de uma string inclu�ndo os acentuados
 * ex: s�rie => S�RIE
 * @param string $text
 * @return string
 */
function str2upper($text) {
  $ASCII_SPC_MIN = '�����������������������������??';
  $ASCII_SPC_MAX = '����������������������������???';
  return strtr(strtoupper($text), $ASCII_SPC_MIN, $ASCII_SPC_MAX);
}

/**
 * @todo
 */
function girarTextoImagem($texto, $tamanho = 8, $altura = 130)
{
  $largura = $tamanho + 5;
  $vertical = $altura;
  $palavras = explode(' ', $texto);

  for ($i = 0; $i < sizeof($palavras); $i++) {
    // verifica se a proxima palavra cabe na linha
    if ($vertical-(strlen($palavras[$i]) * $tamanho) < 0) {
      $vertical = $altura;
      $largura += $tamanho;
    }

    $vertical -= strlen($palavras[$i]) * $tamanho;
  }

  $vertical = $altura;
  $horizontal = $tamanho;

  $imagem = imagecreatetruecolor($largura, $altura);
  $cor = imagecolorallocate($imagem, 0, 0, 0);

  imagefilledrectangle($imagem, 0, 0, ($largura), ($altura),
    imagecolorallocate($imagem, 255, 255, 255));

  $y_espaco = imagettftext($imagem, $tamanho, 90, $horizontal,$vertical, $cor,
    'arquivos/fontes/Vera.ttf',  ' ');
  $y_espaco = $y_espaco[2];

  for ($i = 0; $i < sizeof($palavras); $i++) {
    $y = imagettfbbox($tamanho, 0, 'arquivos/fontes/Vera.ttf', $palavras[$i]);
    $y = $y[2];

    if ($vertical - $y < 0) {
      $vertical = $altura;
      $horizontal += $tamanho + 4;
    }
    elseif ($i != 0) {
    }

    imagettftext($imagem, $tamanho, 90, $horizontal, $vertical, $cor,
      'arquivos/fontes/Vera.ttf', $palavras[$i]);

    $vertical -= ($y + $y_espaco);
  }

  $texto = str_replace(' ', '_', limpa_acentos($texto));
   imagepng($imagem, "tmp/{$texto}.png");

   return "tmp/{$texto}.png";
}