-- //

--
-- Atualiza a foreign key constraint de pmieducar.serie_disciplina
-- para referenciar modules.componente_curricular.
--
-- Adiciona refer�ncias a pmieducar.curso na tabela 
-- pmieducar.servidor_disciplina.
--
-- Essa medida faz parte da tarefa de substitui��o do sistema de notas/faltas
-- por um m�dulo mais robusto e parametriz�vel.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey
  FOREIGN KEY (ref_cod_disciplina)
  REFERENCES modules.componente_curricular(id)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT servidor_disciplina_pkey;

ALTER TABLE "pmieducar"."servidor_disciplina" 
  ADD COLUMN ref_cod_curso integer;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_pkey
  PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao,
    ref_cod_servidor, ref_cod_curso);

-- //@UNDO

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey
  FOREIGN KEY (ref_cod_disciplina)
  REFERENCES pmieducar.disciplina(cod_disciplina)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT servidor_disciplina_pkey;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_pkey
  PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor);

ALTER TABLE "pmieducar"."servidor_disciplina" DROP COLUMN ref_cod_curso;
  
-- //