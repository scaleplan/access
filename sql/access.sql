
-- ----------------------------
-- Type structure for roles
-- ----------------------------
DROP TYPE IF EXISTS "access"."roles";
CREATE TYPE "access"."roles" AS ENUM (
  'Администратор',
  'Преподаватель',
  'Слушатель',
  'Гость'
);

-- ----------------------------
-- Type structure for url_table
-- ----------------------------
DROP TYPE IF EXISTS "access"."url_table";
CREATE TYPE "access"."url_table" AS (
  "url_ids" int4,
  "user_id" int4
);

-- ----------------------------
-- Type structure for url_type
-- ----------------------------
DROP TYPE IF EXISTS "access"."url_type";
CREATE TYPE "access"."url_type" AS ENUM (
  'Создание',
  'Изменение',
  'Удаление',
  'Чтение'
);

-- ----------------------------
-- Sequence structure for access_right_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "access"."access_right_seq";
CREATE SEQUENCE "access"."access_right_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for tables_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "access"."tables_id_seq";
CREATE SEQUENCE "access"."tables_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 32767
START 1
CACHE 1;

-- ----------------------------
-- Sequence structure for url_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "access"."url_id_seq";
CREATE SEQUENCE "access"."url_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;

-- ----------------------------
-- Table structure for model_type
-- ----------------------------
DROP TABLE IF EXISTS "access"."model_type";
CREATE TABLE "access"."model_type" (
  "id" int2 NOT NULL DEFAULT nextval('"access".tables_id_seq'::regclass),
  "schema_name" varchar COLLATE "pg_catalog"."default" NOT NULL,
  "table_name" varchar COLLATE "pg_catalog"."default" NOT NULL,
  "dependent_tables_ids" int2[] NOT NULL DEFAULT '{}'::smallint[],
  "title" varchar(255) COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for right_parent
-- ----------------------------
DROP TABLE IF EXISTS "access"."right_parent";
CREATE TABLE "access"."right_parent" (
  "url_id" int2 NOT NULL,
  "ids" int4[],
  "is_allow" bool NOT NULL,
  "forbidden_selectors" varchar[] COLLATE "pg_catalog"."default"
)
;

-- ----------------------------
-- Table structure for role_right
-- ----------------------------
DROP TABLE IF EXISTS "access"."role_right";
CREATE TABLE "access"."role_right" (
  "url_id" int2 NOT NULL,
  "role" "access"."roles" NOT NULL,
  "ids" int4[],
  "is_allow" bool NOT NULL,
  "forbidden_selectors" varchar[] COLLATE "pg_catalog"."default"
)
INHERITS ("access"."right_parent")
;

-- ----------------------------
-- Table structure for url
-- ----------------------------
DROP TABLE IF EXISTS "access"."url";
CREATE TABLE "access"."url" (
  "id" int4 NOT NULL DEFAULT nextval('"access".url_id_seq'::regclass),
  "text" varchar COLLATE "pg_catalog"."default" NOT NULL,
  "name" varchar COLLATE "pg_catalog"."default",
  "model_type_id" int2,
  "type" "access"."url_type"
)
;

-- ----------------------------
-- Table structure for user_right
-- ----------------------------
DROP TABLE IF EXISTS "access"."user_right";
CREATE TABLE "access"."user_right" (
  "url_id" int2 NOT NULL,
  "user_id" int4 NOT NULL,
  "ids" int4[],
  "is_allow" bool NOT NULL,
  "forbidden_selectors" varchar[] COLLATE "pg_catalog"."default"
)
INHERITS ("access"."right_parent")
;

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS "access"."user_role";
CREATE TABLE "access"."user_role" (
  "role" "access"."roles" NOT NULL,
  "user_id" int4 NOT NULL
)
;

-- ----------------------------
-- Function structure for get_model_ids
-- ----------------------------
DROP FUNCTION IF EXISTS "access"."get_model_ids"("p_model_type_id" int2, "p_group_id" int4=NULL::integer);
CREATE OR REPLACE FUNCTION "access"."get_model_ids"("p_model_type_id" int2, "p_group_id" int4=NULL::integer)
  RETURNS TABLE("id" int4, "name" varchar) AS $BODY$
DECLARE
  l_model_name varchar;
BEGIN
	l_model_name = (SELECT schema_name || '.' || table_name FROM "access".model_type WHERE id = p_model_type_id);
	IF (p_group_id IS NULL) THEN
		RETURN QUERY EXECUTE 
			format(
				'SELECT id, name
				 FROM %I',						
				l_model_name
			);
	ELSE
		RETURN QUERY EXECUTE 
			format(
				'SELECT id, name
				 FROM %I
				 WHERE group_id = $1',						
				l_model_name
			) USING p_group_id;
	END IF;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;

-- ----------------------------
-- Function structure for get_models_list
-- ----------------------------
DROP FUNCTION IF EXISTS "access"."get_models_list"("p_model_type_id" int2, "p_group_id" int4=NULL::integer);
CREATE OR REPLACE FUNCTION "access"."get_models_list"("p_model_type_id" int2, "p_group_id" int4=NULL::integer)
  RETURNS TABLE("id" int8, "name" varchar) AS $BODY$
DECLARE
  l_schema_name varchar;
	l_table_name varchar;
	l_name_column_name varchar = 'name';
BEGIN
	SELECT schema_name, table_name FROM "access".model_type mt WHERE mt.id = p_model_type_id
	  INTO l_schema_name, l_table_name;
		
	IF (SELECT NOT EXISTS(SELECT column_name FROM information_schema.columns WHERE table_name = l_table_name AND table_schema = l_schema_name AND column_name = l_name_column_name)) THEN 
		l_name_column_name = 'id';
	END IF;
	
	IF (p_group_id IS NULL) THEN
		RETURN QUERY EXECUTE 
			format(
				'SELECT id::int8, %I::varchar AS name
				 FROM %I.%I',
				l_name_column_name,
				l_schema_name,
				l_table_name
			);
	ELSE
		RETURN QUERY EXECUTE 
			format(
				'SELECT id::int8, %I::varchar AS name
				 FROM %I.%I
				 WHERE group_id = $1',
				l_name_column_name,		 
				l_schema_name,
				l_table_name
			) USING p_group_id;
	END IF;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;

-- ----------------------------
-- View structure for urls_tmp
-- ----------------------------
DROP VIEW IF EXISTS "access"."urls_tmp";
CREATE VIEW "access"."urls_tmp" AS  SELECT array_agg(u.id) AS url_ids,
    pu.pu AS user_id
   FROM ((access.model_type t1
     LEFT JOIN access.url u ON ((u.model_type_id = ANY (array_append(t1.dependent_tables_ids, t1.id)))))
     CROSS JOIN unnest(ARRAY[1]) pu(pu))
  WHERE (t1.id = 39)
  GROUP BY pu.pu;

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
SELECT setval('"access"."access_right_seq"', 2, true);
ALTER SEQUENCE "access"."tables_id_seq"
OWNED BY "access"."model_type"."id";
SELECT setval('"access"."tables_id_seq"', 96, true);
ALTER SEQUENCE "access"."url_id_seq"
OWNED BY "access"."url"."id";
SELECT setval('"access"."url_id_seq"', 102, true);

-- ----------------------------
-- Indexes structure for table model_type
-- ----------------------------
CREATE UNIQUE INDEX "model_type_schema_name_table_name_idx" ON "access"."model_type" USING btree (
  "schema_name" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST,
  "table_name" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table model_type
-- ----------------------------
ALTER TABLE "access"."model_type" ADD CONSTRAINT "tables_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table right_parent
-- ----------------------------
ALTER TABLE "access"."right_parent" ADD CONSTRAINT "right_parent_pkey" PRIMARY KEY ("url_id");

-- ----------------------------
-- Primary Key structure for table role_right
-- ----------------------------
ALTER TABLE "access"."role_right" ADD CONSTRAINT "default_right_pkey" PRIMARY KEY ("url_id", "role");

-- ----------------------------
-- Indexes structure for table url
-- ----------------------------
CREATE UNIQUE INDEX "url_text_idx" ON "access"."url" USING btree (
  "text" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table url
-- ----------------------------
ALTER TABLE "access"."url" ADD CONSTRAINT "url_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table user_right
-- ----------------------------
ALTER TABLE "access"."user_right" ADD CONSTRAINT "access_right_pkey" PRIMARY KEY ("url_id", "user_id");

-- ----------------------------
-- Primary Key structure for table user_role
-- ----------------------------
ALTER TABLE "access"."user_role" ADD CONSTRAINT "user_role_pkey" PRIMARY KEY ("user_id");

-- ----------------------------
-- Foreign Keys structure for table role_right
-- ----------------------------
ALTER TABLE "access"."role_right" ADD CONSTRAINT "default_right_url_id_fkey" FOREIGN KEY ("url_id") REFERENCES "access"."url" ("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- ----------------------------
-- Foreign Keys structure for table url
-- ----------------------------
ALTER TABLE "access"."url" ADD CONSTRAINT "url_model_type_id_fkey" FOREIGN KEY ("model_type_id") REFERENCES "access"."model_type" ("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- ----------------------------
-- Foreign Keys structure for table user_right
-- ----------------------------
ALTER TABLE "access"."user_right" ADD CONSTRAINT "access_right_url_id_fkey" FOREIGN KEY ("url_id") REFERENCES "access"."url" ("id") ON DELETE CASCADE ON UPDATE CASCADE;
