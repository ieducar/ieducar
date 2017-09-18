-- //

--
-- Cria �ndices adicionais para melhorar performance de queries SQL SELECT
-- que apresentaram lentid�o em bancos de dados com mais de 2 mil docentes.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE INDEX quadro_horario_horarios_busca_horarios_idx
  ON pmieducar.quadro_horario_horarios (ref_servidor, ref_cod_instituicao_servidor, dia_semana, hora_inicial, hora_final, ativo);
  COMMENT ON INDEX pmieducar.quadro_horario_horarios_busca_horarios_idx IS
    '�ndice para otimizar a busca por professores na cria��o de quadro de hor�rios.';

CREATE INDEX servidor_idx
  ON pmieducar.servidor (cod_servidor, ref_cod_instituicao, ativo);
  COMMENT ON INDEX pmieducar.servidor_idx IS
    '�ndice para otimiza��o de acesso aos campos mais usados para queries na tabela.';

CREATE INDEX servidor_alocacao_busca_horarios_idx
  ON pmieducar.servidor_alocacao (ref_ref_cod_instituicao, ref_cod_escola, ativo, periodo, carga_horaria);
  COMMENT ON INDEX pmieducar.servidor_alocacao_busca_horarios_idx IS
    '�ndice para otimizar a busca por professores na cria��o de quadro de hor�rios.';

-- //@UNDO

DROP INDEX pmieducar.quadro_horario_horarios_busca_horarios_idx;
DROP INDEX pmieducar.servidor_idx;
DROP INDEX pmieducar.servidor_alocacao_busca_horarios_idx;

-- //