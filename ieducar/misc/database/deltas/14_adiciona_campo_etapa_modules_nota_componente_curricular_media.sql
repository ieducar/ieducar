-- //

--
-- Atualiza campos no banco de dados, removendo restri��es que n�o seguiam
-- a inten��o do c�digo.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

-- Etapa da m�dia calculada
ALTER TABLE modules.nota_componente_curricular_media ADD COLUMN etapa character varying(2) NOT NULL;

-- Remove chave prim�ria composta (apenas por DataMapper)
ALTER TABLE modules.falta_componente_curricular DROP CONSTRAINT falta_componente_curricular_pkey;

-- Chave prim�ria simples
ALTER TABLE modules.falta_componente_curricular ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (id);

-- Remove �ndice �nico
DROP INDEX modules.nota_componente_curricular_media_nota_aluno_key;

-- //@UNDO

-- Remove campo etapa
ALTER TABLE modules.nota_componente_curricular_media DROP COLUMN etapa;

-- Remove �ndice �nico
CREATE UNIQUE INDEX nota_componente_curricular_media_nota_aluno_key
  ON modules.nota_componente_curricular_media(nota_aluno_id);
  
-- Remove chave prim�ria simples
ALTER TABLE modules.falta_componente_curricular DROP CONSTRAINT falta_componente_curricular_pkey;

-- Adiciona chave prim�ria composta
ALTER TABLE modules.falta_componente_curricular ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (falta_aluno_id, componente_curricular_id);

-- //