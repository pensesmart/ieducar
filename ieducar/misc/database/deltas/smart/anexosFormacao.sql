--
-- Name: anexos_formacao_cod_anexos_formacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.anexos_formacao_cod_anexos_formacao_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

-- Table: portal.anexos_formacao

-- DROP TABLE portal.anexos_formacao;

CREATE TABLE anexos_formacao (
	cod_anexos_formacao integer DEFAULT nextval('anexos_formacao_cod_anexos_formacao_seq'::regclass) NOT NULL,
	ref_ref_cod_pessoa_fj integer DEFAULT 0 NOT NULL,
	nm_anexo character varying(255) DEFAULT ''::character varying NOT NULL,
	descricao text,
	caminho character varying(255) DEFAULT ''::character varying NOT NULL,
	tipo_arquivo character(3) DEFAULT ''::bpchar NOT NULL,
	data_hora timestamp without time zone
);

--
-- Name: anexos_formacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.anexos_formacao
	ADD CONSTRAINT anexos_formacao_pkey PRIMARY KEY (cod_anexos_formacao);

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.anexos_formacao
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: anexos_formacao_ref_ref_cod_pessoa_fj_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.anexos_formacao
	ADD CONSTRAINT anexos_formacao_ref_ref_cod_pessoa_fj_fkey FOREIGN KEY (ref_ref_ref_cod_pessoa_fj) REFERENCES portal.funcionario(ref_cod_pessoa_fj) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER SEQUENCE pmieducar.anexos_formacao_cod_anexos_formacao_seq
	MINVALUE 0;
SELECT setval('pmieducar.anexos_formacao_cod_anexos_formacao_seq', 1, false);
