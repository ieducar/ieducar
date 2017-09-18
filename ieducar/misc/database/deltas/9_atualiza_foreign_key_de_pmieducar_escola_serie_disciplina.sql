-- //

--
-- Atualiza a foreign key constraint de pmieducar.escola_serie_disciplina
-- para referenciar modules.componente_curricular.
--
-- Essa medida faz parte da tarefa de substitui��o do sistema de notas/faltas
-- por um m�dulo mais robusto e parametriz�vel.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE "pmieducar"."escola_serie_disciplina"
  DROP CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey;

ALTER TABLE "pmieducar"."escola_serie_disciplina"
  ADD CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey
  FOREIGN KEY (ref_cod_disciplina)
  REFERENCES modules.componente_curricular(id)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;
  
-- //@UNDO

ALTER TABLE "pmieducar"."escola_serie_disciplina"
  DROP CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey;

ALTER TABLE "pmieducar"."escola_serie_disciplina"
  ADD CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey
  FOREIGN KEY (ref_cod_disciplina)
  REFERENCES pmieducar.disciplina(cod_disciplina)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;

-- //