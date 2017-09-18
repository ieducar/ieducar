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

class calendario
{
	var $meses = array( 1 => "Janeiro", 2 => "Fevereiro", 3 => "Mar&ccedil;o", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
	var $meses_dias;
	var $time;
	var $data;
	var $data_arr;
	var $data_db;
	var $dia;
	var $mes;
	var $mes_wolz;
	var $ano;
	var $dia_semana;
	var $deslocamento_entrada;
	var $deslocamento_saida;
	var $ultimo_dia;
	var $url_default;
	var $url_uniao;
	var $url_var;

	function calendario( $time, $url_default = '#', $url_var = "time" )
	{
		if( $time )
		{
			$this->time = $time;
		}
		else 
		{
			$this->time = time();
		}
		$this->data = date( "d/m/Y", $this->time );
		$this->data_db = date( "Y-m-d", $this->time );
		$this->dia = date( "d", $this->time );
		$this->mes = date( "m", $this->time );
		$this->mes_wolz = date( "n", $this->time );
		$this->ano = date( "Y", $this->time );
		
		$this->data_arr = array( $this->dia, $this->mes, $this->ano );
		
		$this->dia_semana = date( "w", $this->time );
		$this->deslocamento_entrada = date( "w", mktime( 0, 0, 0, $this->mes, 1, $this->ano ) ) - 1;
		$this->deslocamento_saida = date( "w", mktime( 0, 0, 0, $this->mes + 1, -1, $this->ano ) ) - 1;
		$this->ultimo_dia = date( "d", mktime( 0, 0, 0, $this->mes + 1, -1, $this->ano ) ) - 1;
		
		$this->url_default = $url_default;
		if( strpos( $this->url_default, '?' ) === false )
		{
			$this->url_uniao = '?';
		}
		else 
		{
			$this->url_uniao = '&';
		}
		$this->url_var = $url_var;
		
		// define a quantidade de dias nescessaria para chegar em cada mes ( a partir do mes atual )
		for( $i = $this->mes - 1, $totalDias = 0; $i > 0; $i-- )
		{
			// meses que estao atras recebem dias negativos
			$totalDias -= date( "t", mktime( 0, 0, 0, $i, 1, $this->ano ) );
			$this->meses_dias[$i] = $totalDias;
		}
		for( $i = $this->mes + 1, $totalDias = 0; $i < 13; $i++ )
		{
			// meses para frente recebem dias positivos
			$totalDias += date( "t", mktime( 0, 0, 0, $i - 1, 1, $this->ano ) );
			$this->meses_dias[$i] = $totalDias;
		}
		// mes atual nao tem nenhu mdia de diferenca
		$this->meses_dias[$this->mes] = 0;
	}
	
	function gera_calendario()
	{
		$retorno = "
			<div id=\"calendario\">
			<table width=\"100%\" height=\"100%\" border=\"0\" class=\"calendario\">
			<tr>
				<td colspan=\"4\" style=\"text-align: left;\">
					<select name=\"mes\" id=\"mes\" class=\"calendario\" onchange=\"document.location.href='{$this->url_default}{$this->url_uniao}{$this->url_var}=' + this.value\">";
		for( $i = 1; $i < 13; $i++ )
		{
			$time = $this->time + ( $this->meses_dias[$i] * 86400 );
			$mesMaxDias = date( "t", mktime( 0, 0, 0, $i, 1, $this->ano ) );
			if( $this->dia > $mesMaxDias )
			{
				$time -= ( $this->dia - $mesMaxDias ) * 86400;
			}
			$selecionado = "";
			if( $i == $this->mes_wolz )
			{
				$selecionado = " selected";
			}
			$retorno .= "<option value=\"{$time}\"{$selecionado}>{$this->meses[$i]}</option>\n";
		}
		$retorno .= "
			</select>
		</td>
		<td colspan=\"3\" style=\"text-align: right;\">
			<select name=\"ano\" id=\"ano\" class=\"calendario\" onchange=\"document.location.href='{$this->url_default}{$this->url_uniao}{$this->url_var}=' + this.value\">
		";
		
		$sel_ano_atual = date( "Y", $this->time );
		$sel_ano_atual_real = date( "Y", time() );
		// percorre do ano passado ateh 5 anos pra frente
		for( $i = $sel_ano_atual_real - 1; $i < $sel_ano_atual_real + 5; $i++ )
		{
			$dif_dias = date( "L", mktime( 0, 0, 0, 1, 1, $i ) ) ? 31622400: 31536000;
			$time = $this->time + ( $i - $sel_ano_atual ) * $dif_dias;
			//echo ( ( $i - $sel_ano_atual ) * $dif_dias ) . " -<br>";
			
			$selecionado = "";
			if( $i == $sel_ano_atual )
			{
				$selecionado = " selected";
			}
			$retorno .= "<option value=\"{$time}\"{$selecionado}>{$i}</option>\n";
		}
		$retorno .= '
				</select>
			</td>
		</tr>
		<tr>
			<td width="15%" class="calendario_dias_t" title="Domingo">D</td>
			<td width="14%" class="calendario_dias_t" title="Segunda Feira">S</td>
			<td width="14%" class="calendario_dias_t" title="Ter&ccedil;a Feira">T</td>
			<td width="14%" class="calendario_dias_t" title="Quarta Feira">Q</td>
			<td width="14%" class="calendario_dias_t" title="Quinta Feira">Q</td>
			<td width="14%" class="calendario_dias_t" title="Sexta Feira">S</td>
			<td width="15%" class="calendario_dias_t" title="S&aacute;bado">S</td>
		</tr>';
		$comeco = true;
		
		$aux_desloc = 0;
		$aux_finalizador = $this->ultimo_dia;
		$aux_desloc = $aux_desloc - $this->deslocamento_entrada;
		$aux_finalizador = $aux_finalizador + 10 - $this->deslocamento_saida;
		
		$t_aux = 0;
		for ($aux=$aux_desloc; $aux<=$aux_finalizador; $aux++)
		{
			$data = mktime( 0, 0, 0, $this->mes, $aux, $this->ano );
			if ( $comeco )
			{
				$retorno .= "<tr>\n";
				$comeco = false;
			}
			$d = date('d', $data);
			$m = date('m', $data);
			$Y = date('Y', $data);
			$classe = ( $aux <= 0 || $aux > $this->ultimo_dia + 2 ) ? "calendario_outromes" : "calendario_dias"; 
			if( $data <= $this->time && $data + 86400 > $this->time )
			{
				$classe = "calendario_dia_sel";
			}
			$retorno .= "<td class='{$classe}'><a href='{$this->url_default}{$this->url_uniao}{$this->url_var}={$data}'>{$d}</a></td>\n";
			
			if( $t_aux++ > 5 )
			{
				$t_aux = 0;
				$comeco = true;
				$retorno .= "</tr>\n";
			}
		}
		$retorno .= '</table></div><!-- dias_mes';
		foreach ( $this->meses_dias AS $mes => $dias )
		{
			$retorno .= "\n$mes => $dias";
		}
		$retorno .= "\n-->";
		
		return $retorno;
	}
	
	function print_calendario()
	{
		echo $this->gera_calendario();
	}
}