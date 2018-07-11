--
-- PostgreSQL database dump
--

-- Dumped from database version 10.4
-- Dumped by pg_dump version 10.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: acless; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA acless;


--
-- Name: roles; Type: TYPE; Schema: acless; Owner: -
--

CREATE TYPE acless.roles AS ENUM (
    'Администратор',
    'Преподаватель',
    'Слушатель',
    'Гость'
);


--
-- Name: url_table; Type: TYPE; Schema: acless; Owner: -
--

CREATE TYPE acless.url_table AS (
	url_ids integer,
	user_id integer
);


--
-- Name: url_type; Type: TYPE; Schema: acless; Owner: -
--

CREATE TYPE acless.url_type AS ENUM (
    'Создание',
    'Изменение',
    'Удаление',
    'Чтение'
);


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: access_right; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE acless.access_right (
    url_id smallint NOT NULL,
    user_id integer NOT NULL,
    is_allow boolean DEFAULT true NOT NULL,
    "values" character varying[]
);


--
-- Name: access_right_seq; Type: SEQUENCE; Schema: acless; Owner: -
--

CREATE SEQUENCE acless.access_right_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: default_right; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE acless.default_right (
    url_id integer NOT NULL,
    role acless.roles NOT NULL
);


--
-- Name: model_type; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE acless.model_type (
    id smallint NOT NULL,
    schema_name character varying NOT NULL,
    table_name character varying NOT NULL,
    dependent_tables_ids smallint[] DEFAULT '{}'::smallint[] NOT NULL,
    title character varying(255)
);


--
-- Name: tables_id_seq; Type: SEQUENCE; Schema: acless; Owner: -
--

CREATE SEQUENCE acless.tables_id_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tables_id_seq; Type: SEQUENCE OWNED BY; Schema: acless; Owner: -
--

ALTER SEQUENCE acless.tables_id_seq OWNED BY acless.model_type.id;


--
-- Name: url; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE acless.url (
    id integer NOT NULL,
    text character varying NOT NULL,
    name character varying,
    model_type_id smallint,
    type acless.url_type
);


--
-- Name: url_id_seq; Type: SEQUENCE; Schema: acless; Owner: -
--

CREATE SEQUENCE acless.url_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: url_id_seq; Type: SEQUENCE OWNED BY; Schema: acless; Owner: -
--

ALTER SEQUENCE acless.url_id_seq OWNED BY acless.url.id;


--
-- Name: urls_tmp; Type: VIEW; Schema: acless; Owner: -
--

CREATE VIEW acless.urls_tmp AS
 SELECT array_agg(u.id) AS url_ids,
    pu.pu AS user_id
   FROM ((acless.model_type t1
     LEFT JOIN acless.url u ON ((u.model_type_id = ANY (array_append(t1.dependent_tables_ids, t1.id)))))
     CROSS JOIN unnest(ARRAY[1]) pu(pu))
  WHERE (t1.id = 39)
  GROUP BY pu.pu;


--
-- Name: user_role; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE acless.user_role (
    role acless.roles NOT NULL,
    user_id integer NOT NULL
);


--
-- Name: model_type id; Type: DEFAULT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.model_type ALTER COLUMN id SET DEFAULT nextval('acless.tables_id_seq'::regclass);


--
-- Name: url id; Type: DEFAULT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.url ALTER COLUMN id SET DEFAULT nextval('acless.url_id_seq'::regclass);


--
-- Name: access_right access_right_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.access_right
    ADD CONSTRAINT access_right_pkey PRIMARY KEY (url_id, user_id);


--
-- Name: default_right default_right_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.default_right
    ADD CONSTRAINT default_right_pkey PRIMARY KEY (url_id, role);


--
-- Name: model_type tables_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.model_type
    ADD CONSTRAINT tables_pkey PRIMARY KEY (id);


--
-- Name: url url_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.url
    ADD CONSTRAINT url_pkey PRIMARY KEY (id);


--
-- Name: user_role user_role_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.user_role
    ADD CONSTRAINT user_role_pkey PRIMARY KEY (user_id);


--
-- Name: model_type_schema_name_table_name_idx; Type: INDEX; Schema: acless; Owner: -
--

CREATE UNIQUE INDEX model_type_schema_name_table_name_idx ON acless.model_type USING btree (schema_name, table_name);


--
-- Name: url_text_idx; Type: INDEX; Schema: acless; Owner: -
--

CREATE UNIQUE INDEX url_text_idx ON acless.url USING btree (text);


--
-- Name: access_right access_right_url_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.access_right
    ADD CONSTRAINT access_right_url_id_fkey FOREIGN KEY (url_id) REFERENCES acless.url(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: access_right access_right_user_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.access_right
    ADD CONSTRAINT access_right_user_id_fkey FOREIGN KEY (user_id) REFERENCES main."user"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: default_right default_right_url_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.default_right
    ADD CONSTRAINT default_right_url_id_fkey FOREIGN KEY (url_id) REFERENCES acless.url(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: url url_model_type_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.url
    ADD CONSTRAINT url_model_type_id_fkey FOREIGN KEY (model_type_id) REFERENCES acless.model_type(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_role user_role_user_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY acless.user_role
    ADD CONSTRAINT user_role_user_id_fkey FOREIGN KEY (user_id) REFERENCES main."user"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--
