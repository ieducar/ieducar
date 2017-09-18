<?php

require_once 'include/clsPDF.inc.php';


class relatorios
{
	var $titulo_relatorio;
	var $pdf;
	var $altura=0;
	var $texto;
	var $num_linhas=0;
	var $espacoEntreLinhas;
	var $numeroPagina=0;
	var $capa;
	var $rodape;
	var $lastMod;
	var $margem_esquerda = 50;
	var $margem_direita = 50;
	var $margem_topo = 50;
	var $margem_fundo = 50;
	var $txt_padding_left = 5;
	var $largura;
	var $alturaUltimaLinha = 13;
	var $cabecalho;
	var $altura_titulo;
	var $tamanho_titulo;
	var $cor_fundo_titulo;
	var $cor_texto_titulo;
	var $cor_fundo_cabecalho;
	var $cor_fundo_rodape;
	var $tfont = 10;
	var $fonte_titulo;
	var $caixa;
	var $valor_titulo;
	var $linha_transparente = false;
	var $exibe_borda = true;
	var $exibe_titulo_relatorio = true;
	var $exibe_produzido_por = true;

	function relatorios( $nome, $espacoEntreLinhas=80, $capa=false, $rodape=false, $tipoFolha="A4", $cabecalho="Prefeitura de Itajaí\nCentro Tecnologico de Informação e Modernização Administrativa.\nRua Alberto Werner, 100 - Vila Operária\nCEP. 88304-053 - Itajaí - SC", $cod_fundo_titulo = "#000000", $cor_texto_titulo = "#FFFFFF", $cor_fundo_cabecalho = "#D3D3D3", $cor_fundo_rodape = "#D3D3D3", $depurar = false)
	{
		$this->fonte_titulo = "arial";
		$this->cor_fundo_titulo = $cod_fundo_titulo;
		$this->cor_texto_titulo = $cor_texto_titulo;
		$this->cor_fundo_cabecalho = $cor_fundo_cabecalho;
		$this->cor_fundo_rodape = $cor_fundo_rodape;
		$this->pdf = new clsPDF( $nome, "Cartas Folhas de Rosto", $tipoFolha, "", $depurar );
		$this->titulo_relatorio = $nome;
		$this->rodape = $rodape;
		$this->espacoEntreLinhas = $espacoEntreLinhas;
		$this->cabecalho = $cabecalho ? $cabecalho : "Prefeitura de Itajaí\nCentro Tecnologico de Informação e Modernização Administrativa.\nRua Alberto Werner, 100 - Vila Operária\nCEP. 88304-053 - Itajaí - SC";
		$this->largura = $this->pdf->largura;
		if( $capa && ! ( $capa[0] == "" && $capa[1] = "" ) )
		{
			$this->pdf->OpenPage();
			$linha = 0.0001;
			$cores = array( "#000000", "#111111", "#222222", "#333333", "#444444", "#555555", "#666666", "#777777", "#888888", "#999999", "#AAAAAA", "#BBBBBB", "#CCCCCC", "#DDDDDD", "#FFFFFF" );
			$x = 100;
			$y = 150;
			$xMod = 7;
			$yMod = 9;

			$total = count( $cores );
			for( $i = 0; $i < 7; $i++ )
			{
				$this->pdf->Shape( 'ret', $x + ( $i * $xMod ), $y + ( $i * $yMod ), 400, 500, $linha, $cores[($total - $i - 1)] );
			}
			$this->pdf->Write( $capa[0], 190, 290, 300, 100, $this->fonte_titulo, 20, $cores[0], "center" );
			$this->pdf->Write( $capa[1], 190, 400, 300, 100, $this->fonte_titulo, 15, $cores[2], "center" );

			$this->pdf->Shape( 'ret', 50, 0, 25, 850, $linha, $cores[13], $cores[13] );
			$this->pdf->Shape( 'ret', 0, 750, 600, 25, $linha, $cores[13], $cores[13] );

			$this->pdf->Shape( 'ret', 52, 0, 25, 850, $linha, $cores[14], $cores[14] );
			$this->pdf->Shape( 'ret', 0, 752, 600, 25, $linha, $cores[14], $cores[14] );
			$this->pdf->ClosePage();
		}
	}

	function setMargem( $esquerda=50, $direita=50, $topo=50, $fundo=50 )
	{
		$this->margem_direita = $direita;
		$this->margem_esquerda = $esquerda;
		$this->margem_topo = $topo;
		$this->margem_fundo = $fundo;
	}

  public function novaPagina($altura_titulo = 12, $tamanho_titulo = 14)
  {
    // Recupera objeto no escopo global
    global $coreExt;
    $config = $coreExt['Config']->app->template;

    $this->altura_titulo = $altura_titulo;
    $this->tamanho_titulo = $tamanho_titulo;
    $this->numeroPagina++;
    $this->pdf->OpenPage();

    // Desenha as barras cinza do topo
    $this->pdf->Shape('ret', $this->margem_esquerda - 10, 765, 5, 50, 1,
      $this->cor_fundo_cabecalho, $this->cor_fundo_cabecalho);
    $this->pdf->Shape('ret', $this->margem_esquerda + 56, 765,
      $this->largura - $this->margem_esquerda - $this->margem_direita - 106,
      50, 1, $this->cor_fundo_cabecalho, $this->cor_fundo_cabecalho);
    $this->pdf->Shape('ret', $this->largura - $this->margem_direita - 40, 765,
      ($this->largura - $this->margem_direita) - ($this->largura - $this->margem_direita - 40) + 10,
      50, 1, $this->cor_fundo_cabecalho, $this->cor_fundo_cabecalho);

    // Desenha as duas caixas do fim (repete o de cima com altura diferente)
    $this->pdf->Shape('ret', $this->margem_esquerda - 10, 40, 5, 50, 1,
      $this->cor_fundo_rodape, $this->cor_fundo_rodape);
    $this->pdf->Shape('ret', $this->margem_esquerda + 56, 40,
      $this->largura - $this->margem_esquerda - $this->margem_direita - 106,
      50, 1, $this->cor_fundo_rodape, $this->cor_fundo_rodape);
    $this->pdf->Shape('ret', $this->largura - $this->margem_direita - 40, 40,
      ($this->largura - $this->margem_direita) - ($this->largura - $this->margem_direita - 40) + 10,
      50, 1, $this->cor_fundo_rodape, $this->cor_fundo_rodape);

    // Escreve a numeracao da pagina
    $this->pdf->Write( $this->numeroPagina, $this->pdf->largura - $this->margem_direita - 25, 125, 15, 80, $this->fonte_titulo, 10, "#000000", "center" );

    // Insere o brasao da prefeitura
    $image = $config->get($config->pdf->logo, 'imagens/brasao.gif');
    $this->pdf->insertImageScaled("gif", $image, $this->margem_esquerda + 4,
      74, 45);

    $this->pdf->Write($this->cabecalho, 120, 110, 500, 80, $this->fonte_titulo,
      10, "#000000", "left");

    if ($this->exibe_titulo_relatorio) {
      // desenha a caixa para o titulo do relatorio
      $this->pdf->Shape('ret', $this->margem_esquerda - 1, $this->pdf->altura-129, $this->largura - $this->margem_esquerda - $this->margem_direita + 2, $this->altura_titulo, 1, $this->cor_fundo_titulo, $this->cor_fundo_titulo);
      // escreve o titulo do relatorio
      $this->pdf->Write( $this->titulo_relatorio, $this->margem_esquerda + $this->txt_padding_left, 130, 500, $this->tamanho_titulo, $fonte ,'10',$this->cor_texto_titulo,'left');
    }

    // escreve o texto de rodape
    $this->pdf->Write( $this->rodape, $this->margem_esquerda + 70, 848, 500, 80, $this->fonte_titulo, 15, "#000000", "left" );
    if($this->exibe_produzido_por) {
      $this->pdf->Write( "produzido por CTIMA", $this->margem_esquerda + 350, 870, 500, 80, $this->fonte_titulo, 7, "#000000", "left" );
    }

    if($this->exibe_titulo_relatorio)
      $this->altura = 140;
    else
      $this->altura = 100;

    if($this->valor_titulo) {
      $this->novalinha($this->valor_titulo[0],$this->valor_titulo[1],$this->valor_titulo[2],$this->valor_titulo[3],$this->valor_titulo[4],$this->valor_titulo[5],$this->valor_titulo[6],$this->valor_titulo[7],$this->valor_titulo[8],$this->valor_titulo[9]);
    }
  }


	function fechaPagina()
	{
		$this->pdf->ClosePage();
		$this->altura = 0;
	}

	// funcao para ser chamada a cada nova linha
	function novalinha( $texto,$deslocamento=0,$altura=13, $titulo=false, $fonte="arial", $divisoes=false, $fundo_titulo = "#000000", $fundo_texo = "#d3d3d3", $cor_texto_titulo = "#FFFFFF", $bool_traco = false, $titulo_permanente = false, $bool_transparente = null, $tamanho_font = null, $alinhamento = 'left' )
	{
		$this->tfont = $tamanho_font;
		if( ! $divisoes )
		{
			$divisoes = $this->espacoEntreLinhas;
		}
		$cor = "#000000";
		$fundo = $fundo_texo;
		if($this->altura == 0)
		{
			$this->novaPagina();
		}
		if($titulo)
		{
			$fundo = $fundo_titulo;
			$cor = $cor_texto_titulo;
		}
		if($titulo && $titulo_permanente)
		{
			$this->valor_titulo = array($texto,$deslocamento,$altura, $titulo, $fonte, $divisoes, $fundo_titulo, $fundo_texo, $cor_texto_titulo, $bool_traco);
		}


		//Verifica se é o fim da página
		if($this->altura +$altura > ( $this->pdf->altura * 0.85 ) )
		{
			$this->fillText();
			if($this->altura == 0)
			{
				$this->novaPagina();
				// altera a altura atual (de acordo com a altura passa)
				$this->altura += $altura;
				$this->alturaUltimaLinha = $altura;
			}
		}else
		{
							// altera a altura atual (de acordo com a altura passa)
				$this->altura += $altura;
				$this->alturaUltimaLinha = $altura;
		}

		$transparente = ( ! is_null($bool_transparente)) ? $bool_transparente: $this->linha_transparente;

		$this->texto[] = array("texto"=>$texto, "altura"=>$this->altura, "fonte"=>$fonte, "desloc"=>$deslocamento, "alturaLinha"=>$altura, "fundo"=>$fundo, "cor"=>$cor, "titulo"=>$titulo, "divisoes"=>$divisoes, "alturaultimalinha"=>$this->alturaUltimaLinha, "traco"=>$bool_traco, "tfont"=>$this->tfont, "transparente"=>$transparente, "alinhamento"=>$alinhamento );

	}


	function novalinha2( $texto,$deslocamento=0,$altura=13, $titulo=false, $fonte="arial", $divisoes=false, $fundo_titulo = "#000000", $fundo_texo = "#d3d3d3", $cor_texto_titulo = "#FFFFFF", $bool_traco = false, $tfont = '10', $bool_transparente = null)
	{
		if( ! $divisoes )
		{
			$divisoes = $this->espacoEntreLinhas;
		}
		$cor = "#000000";
		$fundo = $fundo_texo;
		if($this->altura == 0)
		{
			$this->novaPagina();
		}
		if($titulo)
		{
			$fundo = $fundo_titulo;
			$cor = $cor_texto_titulo;
		}



		//Verifica se é o fim da página
		if($this->altura +$altura > ( $this->pdf->altura * 0.85 ) )
		{
			$this->fillText();
			if($this->altura == 0)
			{
				$this->novaPagina();
				// altera a altura atual (de acordo com a altura passa)
				$this->altura += $altura;
				$this->alturaUltimaLinha = $altura;
			}
		}else
		{
							// altera a altura atual (de acordo com a altura passa)
				$this->altura += $altura;
				$this->alturaUltimaLinha = $altura;
		}

		$transparente = ( ! is_null($bool_transparente)) ? $bool_transparente: $this->linha_transparente;

		$this->tfont = $tfont;
		$this->texto[] = array("texto"=>$texto, "altura"=>$this->altura, "fonte"=>$fonte, "desloc"=>$deslocamento, "alturaLinha"=>$altura, "fundo"=>$fundo, "cor"=>$cor, "titulo"=>$titulo, "divisoes"=>$divisoes, "alturaultimalinha"=>$this->alturaUltimaLinha, "traco"=>$bool_traco, "tfont"=>$this->tfont, "transparente"=>$transparente);

	}

	function setLinhaTransparente( $boolTransparente )
	{
		$this->linha_transparente = $boolTransparente;
	}

	function fillText()
	{
		if($this->exibe_borda)
			$this->pdf->Shape('ret', $this->margem_esquerda - 1, $this->pdf->altura  - $this->altura -3, $this->largura - $this->margem_direita - $this->margem_esquerda + 2, $this->altura-135, 1);

		// passa todas as linhas
		foreach ( $this->texto as $linha )
		{
			if( !$linha['titulo'])
				$this->num_linhas++;
			$mod = ( $linha['alturaLinha'] - $linha['alturaultimalinha'] > 0 ) ? ( $linha['alturaLinha'] - $linha['alturaultimalinha'] ): 0;
			$mod += ($linha['alturaLinha'] > $this->lastMod) ? $this->lastMod : 0;
			// se for titulo ou linha impar desenha uma caixa no fundo

			if( ( $this->num_linhas % 2 || $linha['titulo'] ) && ! $linha['transparente'] )
			{
				if(!$linha['traco'])
				{
					$this->pdf->Shape( 'ret', $this->margem_esquerda+0.5, $this->pdf->altura - $linha['altura'] - $mod , $this->largura - $this->margem_direita - $this->margem_esquerda-2, $linha['alturaLinha'], 1, $linha['fundo'], $linha['fundo'] );
				}
			}
			$i = 0;
			$col = 0;
			if($linha['traco'])
			{
				$posx = $this->margem_esquerda + $this->txt_padding_left + $i ;
				$this->pdf->line($posx-5, $this->pdf->altura - $linha['altura']+$mod +3, $this->largura - $this->margem_direita - $posx+ 55, $this->pdf->altura - $linha['altura'] + $mod+3,1.5);
			}else
			{
				// passa as colunas escrevendo elas
				foreach( $linha['texto'] as $texto )
				{
					if($linha['tfont']=="")
						$linha['tfont']= 10;//$this->tfont;
					$posx = $this->margem_esquerda + $this->txt_padding_left + $i + $linha['desloc'];
					$this->pdf->Write( $texto, $posx, $linha['altura']+$mod, $this->largura - $this->margem_direita - $posx, $linha['alturaLinha'], $linha['fonte'], $linha['tfont'], $linha['cor'], $linha['alinhamento'] );
					$colSum = ( is_array( $linha['divisoes'] ) )? $linha['divisoes'][$col]: $linha['divisoes'];
					$i += $colSum;
					$col++;
				}
			}

			$this->lastMod = $mod ;
		}
		$this->texto ="";
		$this->altura = 0;
		$this->fechaPagina();
	}

	function exibeBorda($exibe_borda)
	{
		if(is_bool($exibe_borda))
			$this->exibe_borda = $exibe_borda;

	}

	function exibeTituloRelatorio($exibeTituloRelatorio)
	{
		if(is_bool($exibeTituloRelatorio))
			$this->exibe_titulo_relatorio = $exibeTituloRelatorio;

	}

	function fechaPdf()
	{
		if( $this->texto )
		{
			$this->fillText();
		}
		$this->pdf->ClosePage();
		$link = $this->pdf->GetLink();
		$this->pdf->CloseFile();
		return $link;
	}

	function quebraPagina()
	{
		if( $this->texto )
		{
			$this->fillText();
		}
		$this->pdf->ClosePage();
		$this->altura = 0;
	}
}
?>