
SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

SET search_path = acless, pg_catalog;

ALTER TABLE IF EXISTS ONLY acless.user_role DROP CONSTRAINT IF EXISTS user_role_user_id_fkey;
ALTER TABLE IF EXISTS ONLY acless.default_right DROP CONSTRAINT IF EXISTS default_right_url_id_fkey;
ALTER TABLE IF EXISTS ONLY acless.access_right DROP CONSTRAINT IF EXISTS access_right_user_id_fkey;
ALTER TABLE IF EXISTS ONLY acless.access_right DROP CONSTRAINT IF EXISTS access_right_url_id_fkey;
DROP INDEX IF EXISTS acless.url_text_idx;
ALTER TABLE IF EXISTS ONLY acless.user_role DROP CONSTRAINT IF EXISTS user_role_pkey;
ALTER TABLE IF EXISTS ONLY acless.url DROP CONSTRAINT IF EXISTS url_pkey;
ALTER TABLE IF EXISTS ONLY acless.default_right DROP CONSTRAINT IF EXISTS default_right_pkey;
ALTER TABLE IF EXISTS ONLY acless.access_right DROP CONSTRAINT IF EXISTS access_right_pkey;
ALTER TABLE IF EXISTS acless.url ALTER COLUMN id DROP DEFAULT;
DROP TABLE IF EXISTS acless.user_role;
DROP SEQUENCE IF EXISTS acless.url_id_seq;
DROP TABLE IF EXISTS acless.url;
DROP TABLE IF EXISTS acless.default_right;
DROP SEQUENCE IF EXISTS acless.access_right_seq;
DROP TABLE IF EXISTS acless.access_right;
DROP TYPE IF EXISTS acless.roles;
DROP SCHEMA IF EXISTS acless;
--
-- Name: acless; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA acless;


SET search_path = acless, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: access_right; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE access_right (
    url_id smallint NOT NULL,
    user_id integer NOT NULL,
    is_allow boolean DEFAULT true NOT NULL,
    "values" integer[]
);


--
-- Name: access_right_seq; Type: SEQUENCE; Schema: acless; Owner: -
--

CREATE SEQUENCE access_right_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: default_right; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE default_right (
    url_id integer NOT NULL,
    role roles NOT NULL,
    is_allow boolean DEFAULT true NOT NULL,
    "values" integer[]
);


--
-- Name: url; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE url (
    id integer NOT NULL,
    text character varying NOT NULL,
    name character varying,
    filter character varying,
    filter_reference character varying
);


--
-- Name: url_id_seq; Type: SEQUENCE; Schema: acless; Owner: -
--

CREATE SEQUENCE url_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: url_id_seq; Type: SEQUENCE OWNED BY; Schema: acless; Owner: -
--

ALTER SEQUENCE url_id_seq OWNED BY url.id;


--
-- Name: user_role; Type: TABLE; Schema: acless; Owner: -
--

CREATE TABLE user_role (
    role roles NOT NULL,
    user_id integer NOT NULL
);


--
-- Name: url id; Type: DEFAULT; Schema: acless; Owner: -
--

ALTER TABLE ONLY url ALTER COLUMN id SET DEFAULT nextval('url_id_seq'::regclass);


--
-- Name: access_right access_right_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY access_right
    ADD CONSTRAINT access_right_pkey PRIMARY KEY (url_id, user_id);


--
-- Name: default_right default_right_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY default_right
    ADD CONSTRAINT default_right_pkey PRIMARY KEY (url_id, role);


--
-- Name: url url_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY url
    ADD CONSTRAINT url_pkey PRIMARY KEY (id);


--
-- Name: user_role user_role_pkey; Type: CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY user_role
    ADD CONSTRAINT user_role_pkey PRIMARY KEY (role, user_id);


--
-- Name: url_text_idx; Type: INDEX; Schema: acless; Owner: -
--

CREATE UNIQUE INDEX url_text_idx ON url USING btree (text);


--
-- Name: access_right access_right_url_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY access_right
    ADD CONSTRAINT access_right_url_id_fkey FOREIGN KEY (url_id) REFERENCES main.url(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: access_right access_right_user_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY access_right
    ADD CONSTRAINT access_right_user_id_fkey FOREIGN KEY (user_id) REFERENCES main."user"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: default_right default_right_url_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY default_right
    ADD CONSTRAINT default_right_url_id_fkey FOREIGN KEY (url_id) REFERENCES url(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: user_role user_role_user_id_fkey; Type: FK CONSTRAINT; Schema: acless; Owner: -
--

ALTER TABLE ONLY user_role
    ADD CONSTRAINT user_role_user_id_fkey FOREIGN KEY (user_id) REFERENCES main."user"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

