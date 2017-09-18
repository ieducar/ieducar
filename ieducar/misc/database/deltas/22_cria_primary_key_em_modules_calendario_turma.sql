-- //

--
-- Cria a chave prim�ria na tabela modules.calendario_turma. O sql da tabela foi
-- gerado a partir de uma modelagem incompleta, que n�o continha as defini��es de
-- chave prim�ria.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE "modules"."calendario_turma"
  ADD CONSTRAINT "calendario_turma_pk"
  PRIMARY KEY ("calendario_ano_letivo_id", "ano", "mes", "dia", "turma_id");

-- //@UNDO

-- SQL omitido intencionalmente. A tabela n�o � para ser criada sem a primary key.

-- //