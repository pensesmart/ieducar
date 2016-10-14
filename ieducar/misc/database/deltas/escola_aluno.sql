--
-- Name: escola_cod_escola_seq; Type: SEQUENCE; Schema: cadastro; Owner: -
--

CREATE SEQUENCE cadastro.escola_cod_escola_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: cadastro.escola; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE cadastro.escola (
	cod_escola integer DEFAULT nextval('escola_cod_escola_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_escola character varying(255) NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	idmun integer NOT NULL
);

--
-- Name: cadastro.endereco_escola; Type: TABLE; Schema: cadastro; Owner: -; Tablespace: 
--

CREATE TABLE cadastro.endereco_escola (
	ref_cod_escola integer NOT NULL,
	cep integer,
	idlog integer,
	numero integer,
	idbai integer,
	lat double precision,
	long  double precision,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_endereco_escola character varying(255) NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL
);

--
-- Name: escola_pkey; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cadastro.escola
	ADD CONSTRAINT escola_pkey PRIMARY KEY (cod_escola);

--
-- Name: endereco_escola_pkey; Type: CONSTRAINT; Schema: cadastro; Owner: -; Tablespace: 
--

ALTER TABLE ONLY cadastro.endereco_escola
	ADD CONSTRAINT endereco_escola_pkey PRIMARY KEY (ref_cod_escola);

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON cadastro.escola
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: cadastro; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON cadastro.endereco_escola
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: escola_idmun_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.escola
	ADD CONSTRAINT escola_idmun_fkey FOREIGN KEY (idmun) REFERENCES public.municipio(idmun) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: escola_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.escola
	ADD CONSTRAINT escola_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: escola_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.escola
	ADD CONSTRAINT escola_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: endereco_escola_cep_endereco_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_escola
	ADD CONSTRAINT endereco_escola_cep_endereco_fkey FOREIGN KEY (idbai, idlog, cep) REFERENCES urbano.cep_logradouro_bairro(idbai, idlog, cep) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: endereco_escola_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_escola
	ADD CONSTRAINT endereco_escola_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: endereco_escola_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: cadastro; Owner: -
--

ALTER TABLE ONLY cadastro.endereco_escola
	ADD CONSTRAINT endereco_escola_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


ALTER SEQUENCE cadastro.escola_cod_escola_seq
	MINVALUE 0;

SELECT setval('cadastro.escola_cod_escola_seq', 1, false);
