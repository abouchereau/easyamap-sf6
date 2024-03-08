ALTER TABLE user RENAME COLUMN id_user TO id;
ALTER TABLE user ADD COLUMN reset_token VARCHAR(100);

ALTER TABLE farm RENAME COLUMN id_farm TO id;

ALTER TABLE referent RENAME COLUMN id_referent TO id;
ALTER TABLE referent RENAME COLUMN fk_user TO id_user;
ALTER TABLE referent RENAME COLUMN fk_farm TO id_farm;



UPDATE user u
LEFT JOIN referent r on r.id_user = u.id
LEFT JOIN farm f on f.id_user = u.id
SET u.roles = CONCAT(
    '[',
    CASE WHEN u.is_active=1 THEN '"ROLE_USER"' ELSE '' END,
    CASE WHEN u.is_active=1 and u.is_adherent=1 THEN ',"ROLE_ADHERENT"' ELSE '' END,
    CASE WHEN u.is_active=1 and u.is_admin=1 THEN ',"ROLE_ADMIN"' ELSE '' END,
    CASE WHEN u.is_active=1 and f.id IS NOT NULL THEN ',"ROLE_FARMER"' ELSE '' END,
    CASE WHEN u.is_active=1 and r.id IS NOT NULL THEN ',"ROLE_REFERENT"' ELSE '' END,
    ']'
    );


ALTER TABLE user DROP COLUMN is_active;
ALTER TABLE user DROP COLUMN is_adherent;
ALTER TABLE user DROP COLUMN is_admin;
ALTER TABLE user DROP COLUMN last_connection;