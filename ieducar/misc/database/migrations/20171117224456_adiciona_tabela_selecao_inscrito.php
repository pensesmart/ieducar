<?php


use Phinx\Migration\AbstractMigration;

class AdicionaTabelaSelecaoInscrito extends AbstractMigration
{
	public function change()
	{
		$this->execute("CREATE SEQUENCE pmieducar.selecao_inscrito_cod_selecao_inscrito_seq
				START WITH 1
				INCREMENT BY 1
				NO MAXVALUE
				MINVALUE 0
				CACHE 1;

			--
			-- Name: selecao_inscrito; Type: TABLE; Schema: pmieducar; Owner: -
			--

			CREATE TABLE pmieducar.selecao_inscrito
			(
				cod_inscrito integer DEFAULT nextval('selecao_inscrito_cod_selecao_inscrito_seq'::regclass) NOT NULL,
				ref_usuario_exc integer,
				ref_usuario_cad integer NOT NULL,
				nome character varying(255) NOT NULL,
				data_nasc date,
				cpf numeric(11,0),
				rg numeric,
				sexo character(1),
				ddd_telefone_1 numeric(2,0),
				telefone_1 numeric(10,0),
				ddd_telefone_mov numeric(2,0),
				telefone_mov numeric(10,0),
				ddd_telefone_2 numeric(2,0),
				telefone_2 numeric(10,0),
				email character varying(255),
				serie numeric(2,0),
				egresso numeric(4,0),
				turno numeric(1,0),
				guarda_mirim boolean DEFAULT false,
				indicacao character varying(255),
				nm_responsavel character varying(255),
				copia_rg numeric(1,0) DEFAULT 0,
				copia_cpf numeric(1,0) DEFAULT 0,
				copia_residencia numeric(1,0) DEFAULT 0,
				copia_historico numeric(1,0) DEFAULT 0,
				copia_renda numeric(1,0) DEFAULT 0,
				data_cadastro timestamp without time zone NOT NULL,
				data_exclusao timestamp without time zone,
				ativo smallint DEFAULT (1)::smallint NOT NULL,
				ref_cod_escola integer,
				ref_ano integer,
				etapa_1 numeric(1,0) DEFAULT (-1),
				bairro character varying(255),
				encaminhamento numeric(1,0) DEFAULT 0,
				etapa_2 numeric(1,0) DEFAULT (-1),
				etapa_3 numeric(1,0) DEFAULT (-1),
				CONSTRAINT ck_inscrito_sexo CHECK ((((sexo = 'M'::bpchar) OR (sexo = 'F'::bpchar)) OR (sexo = 'I'::bpchar)))
			);

			--
			-- Name: selecao_inscrito_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
			--

			ALTER TABLE ONLY pmieducar.selecao_inscrito
				ADD CONSTRAINT selecao_inscrito_pkey PRIMARY KEY (cod_inscrito);

			--
			-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
			--

			CREATE TRIGGER fcn_aft_update
				AFTER INSERT OR UPDATE ON pmieducar.selecao_inscrito
				FOR EACH ROW
				EXECUTE PROCEDURE fcn_aft_update();

			--
			-- Name: selecao_inscrito_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
			--

			ALTER TABLE ONLY pmieducar.selecao_inscrito
				ADD CONSTRAINT selecao_inscrito_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


			--
			-- Name: selecao_inscrito_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
			--

			ALTER TABLE ONLY pmieducar.selecao_inscrito
				ADD CONSTRAINT selecao_inscrito_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


			--
			-- Name: selecao_inscrito_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
			--

			ALTER TABLE ONLY pmieducar.selecao_inscrito
				ADD CONSTRAINT selecao_inscrito_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;


			--
			-- Name: selecao_inscrito_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
			--

			ALTER TABLE ONLY pmieducar.selecao_inscrito
				ADD CONSTRAINT selecao_inscrito_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola, ref_ano) REFERENCES pmieducar.escola_ano_letivo(ref_cod_escola, ano) ON UPDATE RESTRICT ON DELETE RESTRICT;
		");
	}
}
