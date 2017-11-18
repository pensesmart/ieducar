--
-- Name: avaliacao_conjunto_opcoes_cod_avaliacao_conjunto_opcoes_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.avaliacao_conjunto_opcoes_cod_avaliacao_conjunto_opcoes_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: avaliacao_opcao_cod_avaliacao_opcao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.avaliacao_opcao_cod_avaliacao_opcao_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: avaliacao_pergunta_cod_avaliacao_pergunta_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.avaliacao_pergunta_cod_avaliacao_pergunta_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: avaliacao_questionario_cod_avaliacao_questionario_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.avaliacao_questionario_cod_avaliacao_questionario_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: avaliacao_conjunto_opcoes; Type: TABLE; Schema: modules; Owner: -
--

CREATE TABLE pmieducar.avaliacao_conjunto_opcoes (
	cod_avaliacao_conjunto_opcoes integer DEFAULT nextval('avaliacao_conjunto_opcoes_cod_avaliacao_conjunto_opcoes_seq'::regclass) NOT NULL,
	nm_conjunto character varying(255) NOT NULL,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_avaliacao_pergunta integer NOT NULL
);

--
-- Name: avaliacao_opcao; Type: TABLE; Schema: modules; Owner: -
--

CREATE TABLE pmieducar.avaliacao_opcao (
	cod_avaliacao_opcao integer DEFAULT nextval('avaliacao_opcao_cod_avaliacao_opcao_seq'::regclass) NOT NULL,
	nm_opcao character varying(255) NOT NULL,
	ordem smallint,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_avaliacao_conjunto_opcoes integer NOT NULL,
	valor character varying(50) NOT NULL
);

--
-- Name: avaliacao_pergunta; Type: TABLE; Schema: modules; Owner: -
--

CREATE TABLE pmieducar.avaliacao_pergunta (
	cod_avaliacao_pergunta integer DEFAULT nextval('avaliacao_pergunta_cod_avaliacao_pergunta_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_pergunta character varying(255) NOT NULL,
	tipo integer NOT NULL,
	ordem integer NOT NULL,
	conjunto_opcoes integer NOT NULL,
	obrigatorio integer NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_avaliacao_questionario integer NOT NULL
);

--
-- Name: avaliacao_questionario; Type: TABLE; Schema: modules; Owner: -
--

CREATE TABLE pmieducar.avaliacao_questionario (
	cod_avaliacao_questionario integer DEFAULT nextval('avaliacao_questionario_cod_avaliacao_questionario_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_questionario character varying(255) NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_componente_curricular integer
);

--
-- Name: avaliacao_conjunto_opcoes_pkey; Type: CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_conjunto_opcoes
	ADD CONSTRAINT avaliacao_conjunto_opcoes_pkey PRIMARY KEY (cod_avaliacao_conjunto_opcoes);

--
-- Name: avaliacao_opcao_pkey; Type: CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_opcao
	ADD CONSTRAINT avaliacao_opcao_pkey PRIMARY KEY (cod_avaliacao_opcao);

--
-- Name: avaliacao_pergunta_pkey; Type: CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_pergunta
	ADD CONSTRAINT avaliacao_pergunta_pkey PRIMARY KEY (cod_avaliacao_pergunta);

--
-- Name: avaliacao_questionario_pkey; Type: CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_questionario
	ADD CONSTRAINT avaliacao_questionario_pkey PRIMARY KEY (cod_avaliacao_questionario);

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.avaliacao_conjunto_opcoes
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.avaliacao_opcao
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.avaliacao_pergunta
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.avaliacao_questionario
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: avaliacao_conjunto_opcoes_ref_cod_avaliacao_pergunta_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_conjunto_opcoes
	ADD CONSTRAINT avaliacao_conjunto_opcoes_ref_cod_avaliacao_pergunta_fkey FOREIGN KEY (ref_cod_avaliacao_pergunta) REFERENCES avaliacao_pergunta(cod_avaliacao_pergunta) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_opcao_ref_cod_avaliacao_conjunto_opcoes_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_opcao
	ADD CONSTRAINT avaliacao_opcao_ref_cod_avaliacao_conjunto_opcoes_fkey FOREIGN KEY (ref_cod_avaliacao_conjunto_opcoes) REFERENCES avaliacao_conjunto_opcoes(cod_avaliacao_conjunto_opcoes) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_pergunta_ref_cod_avaliacao_questionario_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_pergunta
	ADD CONSTRAINT avaliacao_pergunta_ref_cod_avaliacao_questionario_fkey FOREIGN KEY (ref_cod_avaliacao_questionario) REFERENCES avaliacao_questionario(cod_avaliacao_questionario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_pergunta_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_pergunta
	ADD CONSTRAINT avaliacao_pergunta_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_pergunta_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_pergunta
	ADD CONSTRAINT avaliacao_pergunta_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_questionario_ref_cod_componente_curricular_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_questionario
	ADD CONSTRAINT avaliacao_questionario_ref_cod_componente_curricular_fkey FOREIGN KEY (ref_cod_componente_curricular) REFERENCES componente_curricular(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_questionario_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_questionario
	ADD CONSTRAINT avaliacao_questionario_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: avaliacao_questionario_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY avaliacao_questionario
	ADD CONSTRAINT avaliacao_questionario_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER SEQUENCE pmieducar.avaliacao_conjunto_opcoes_cod_avaliacao_conjunto_opcoes_seq
	MINVALUE 0;
SELECT setval('pmieducar.avaliacao_conjunto_opcoes_cod_avaliacao_conjunto_opcoes_seq', 1, false);

ALTER SEQUENCE pmieducar.avaliacao_opcao_cod_avaliacao_opcao_seq
	MINVALUE 0;
SELECT setval('pmieducar.avaliacao_opcao_cod_avaliacao_opcao_seq', 1, false);

ALTER SEQUENCE pmieducar.avaliacao_pergunta_cod_avaliacao_pergunta_seq
	MINVALUE 0;
SELECT setval('pmieducar.avaliacao_pergunta_cod_avaliacao_pergunta_seq', 1, false);

ALTER SEQUENCE pmieducar.avaliacao_questionario_cod_avaliacao_questionario_seq
	MINVALUE 0;
SELECT setval('pmieducar.avaliacao_questionario_cod_avaliacao_questionario_seq', 1, false);
