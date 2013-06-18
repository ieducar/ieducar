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
require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");


class clsEndereco
{
	var $idpes;
	var $tipo;
	var $idtlog;
	var $logradouro;
	var $idlog;
	var $numero;
	var $letra;
	var $complemento;
	var $bairro;
	var $idbai;
	var $cep;
	var $cidade;
	var $idmun;
	var $sigla_uf;
	var $reside_desde;
	var $bloco;
	var $apartamento;
	var $andar;

	function clsEndereco($idpes=false)
	{
		$this->idpes = $idpes;
	}
	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{
		if($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT cep, idlog, numero, letra, complemento, idbai, bloco, andar, apartamento, logradouro, bairro, cidade, sigla_uf, idtlog FROM cadastro.v_endereco WHERE idpes = '{$this->idpes}'");
			if($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				$this->bairro = $tupla['bairro'];
				$this->idbai = $tupla['idbai'];
				$this->cidade = $tupla['cidade'];
				$this->sigla_uf = $tupla['sigla_uf'];
				$this->complemento = $tupla['complemento'];
				$this->bloco = $tupla['bloco'];
				$this->apartamento = $tupla['apartamento'];
				$this->andar = $tupla['andar'];
				$this->letra = $tupla['letra'];
				$this->numero = $tupla['numero'];
				$this->logradouro = $tupla['logradouro'];
				$this->idlog =  $tupla['idlog'];
				$this->idtlog = $tupla['idtlog'];
				$this->cep = $tupla['cep'];
				return $tupla;

			}
		
		}
		return false;
	}

	function edita()
	{

	}
}
?>