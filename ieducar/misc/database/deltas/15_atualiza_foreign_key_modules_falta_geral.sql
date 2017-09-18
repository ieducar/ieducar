-- //

--
-- Atualiza campos no banco de dados, removendo restri��o incorreta da tabela.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

-- Remove chave prim�ria incorreta
ALTER TABLE modules.falta_geral DROP CONSTRAINT falta_geral_pkey;

-- Chave prim�ria simples
ALTER TABLE modules.falta_geral ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (id);

-- //@UNDO

-- Adiciona chave prim�ria composta
ALTER TABLE modules.falta_geral ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (falta_aluno_id);

-- //