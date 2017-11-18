<?php


use Phinx\Migration\AbstractMigration;

class AdicionaTabelaCadastroEscola extends AbstractMigration
{
	public function change()
	{
		$this->execute("CREATE SEQUENCE cadastro.escola_cod_escola_seq
				INCREMENT BY 1
				MINVALUE 0
				NO MAXVALUE
				CACHE 1;

			--
			-- Name: escola; Type: TABLE; Schema: cadastro; Owner: -
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
			-- Name: escola_pkey; Type: CONSTRAINT; Schema: cadastro; Owner: -
			--

			ALTER TABLE ONLY cadastro.escola
				ADD CONSTRAINT escola_pkey PRIMARY KEY (cod_escola);

			--
			-- Name: fcn_aft_update; Type: TRIGGER; Schema: cadastro; Owner: -
			--

			CREATE TRIGGER fcn_aft_update
				AFTER INSERT OR UPDATE ON cadastro.escola
				FOR EACH ROW
				EXECUTE PROCEDURE pmieducar.fcn_aft_update();

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
		");
	}
}
