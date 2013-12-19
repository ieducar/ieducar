-- Altera o search path.

ALTER DATABASE ieducar SET search_path = "$user", public, portal, cadastro, acesso, alimentos, consistenciacao, historico, pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano, modules;

-- Trim de tipo Date, removido nas versões mais novas.
CREATE FUNCTION pg_catalog.btrim(date) RETURNS TEXT LANGUAGE SQL STABLE
as $f$ 
  SELECT trim($1::text); 
$f$;

-- Grants de permissões para o usuário
GRANT USAGE ON SCHEMA public TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO ieducar; 
GRANT USAGE ON SCHEMA portal TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA portal TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA portal TO ieducar; 
GRANT USAGE ON SCHEMA cadastro TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA cadastro TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA cadastro TO ieducar; 
GRANT USAGE ON SCHEMA acesso TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA acesso TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA acesso TO ieducar; 
GRANT USAGE ON SCHEMA alimentos TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA alimentos TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA alimentos TO ieducar; 
GRANT USAGE ON SCHEMA consistenciacao TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA consistenciacao TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA consistenciacao TO ieducar; 
GRANT USAGE ON SCHEMA historico TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA historico TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA historico TO ieducar; 
GRANT USAGE ON SCHEMA pmiacoes TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA pmiacoes TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA pmiacoes TO ieducar; 
GRANT USAGE ON SCHEMA pmicontrolesis TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA pmicontrolesis TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA pmicontrolesis TO ieducar; 
GRANT USAGE ON SCHEMA pmidrh TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA pmidrh TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA pmidrh TO ieducar; 
GRANT USAGE ON SCHEMA pmieducar TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA pmieducar TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA pmieducar TO ieducar; 
GRANT USAGE ON SCHEMA pmiotopic TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA pmiotopic TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA pmiotopic TO ieducar; 
GRANT USAGE ON SCHEMA urbano TO ieducar; 
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA urbano TO ieducar; 
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA urbano TO ieducar;
