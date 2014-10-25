<?php

class EducacensoParser {
	
	private $instituicao_id;
	private $filename;
	
	public function __construct($instituicao_id, $filename) {
		$this->instituicao_id = $instituicao_id;
		$this->filename = $filename;
	}
	
	/**
	 * Registro 00
	 */
	protected function parse_escola_id ($line) {
		
	}

	/**
	 * Registro 10
	 */
	protected function parse_escola_data ($line) {
		
	}
	
	/**
	 * Registro 20
	 */
	protected function parse_turma ($line) {
		
	} 
	
	/**
	 * Registro 30
	 */
	protected function parse_profissional_id ($line) {

	}
	
	/**
	 * Registro 40
	 */
	protected function parse_profissional_docs ($line) {
		
	}

	/**
	 * Registro 50
	 */
	protected function parse_profissional_data ($line) {
	
	}
	
	/**
	 * Registro 51
	 */
	protected function parse_profissional_data_school ($line) {
	
	}
	
	/**
	 * Registro 60
	 */
	protected function parse_aluno_id ($line) {
		
	}
	
	/**
	 * Registro 70
	 */
	protected function parse_aluno_data ($line) {
		
	}
	
	/**
	 * Registro 80
	 */
	protected function parse_student_links ($line) {
		
	}
	
}

?>