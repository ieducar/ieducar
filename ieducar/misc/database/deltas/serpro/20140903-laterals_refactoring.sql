-- Em Postgresql 9.3+, 'lateral' tornou-se keyword
ALTER TABLE portal_banner
  RENAME COLUMN lateral to is_lateral;
