-- Do not run on the 2024 version of the HFW database.
-- Needed for older versions of the database though.

CREATE TABLE IF NOT EXISTS db_meta_data (
  db_name varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Full Database Name',
  db_lbl varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Database Nickname',
  db_dsr varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Database description',
  PRIMARY KEY (db_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Keeps track of database name';

--
-- Dumping data for table db_meta_data
--

INSERT IGNORE INTO db_meta_data (db_name, db_lbl, db_dsr) VALUES
('Hooplafw', 'Hoopla Database 1', 'Hoopla Project Database');

ALTER TABLE `pg_pg_obj_brg`  ADD `use_def_ctx_bit` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'True if the def_ctx value is the fallback.'  AFTER `use_def_bit`;
