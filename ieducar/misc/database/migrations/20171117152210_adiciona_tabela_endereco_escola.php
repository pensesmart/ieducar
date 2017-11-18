<?php

use Phinx\Migration\AbstractMigration;

class AdicionaTabelaEnderecoEscola extends AbstractMigration
{
	public function change()
	{
		$this->execute("CREATE TABLE cadastro.endereco_escola (
				ref_cod_escola integer NOT NULL,
				cep integer,
				idlog integer,
				numero integer,
				idbai integer,
				lat double precision,
				long double precision,
				ref_usuario_exc integer,
				ref_usuario_cad integer NOT NULL,
				nm_endereco_escola character varying(255) NOT NULL,
				data_cadastro timestamp without time zone NOT NULL,
				data_exclusao timestamp without time zone,
				ativo smallint DEFAULT (1)::smallint NOT NULL
			);

			ALTER TABLE cadastro.endereco_escola ADD CONSTRAINT endereco_escola_cep_endereco_fkey FOREIGN KEY (idbai, idlog, cep)
				REFERENCES urbano.cep_logradouro_bairro (idbai, idlog, cep) MATCH SIMPLE
				ON UPDATE RESTRICT ON DELETE RESTRICT;

			ALTER TABLE cadastro.endereco_escola ADD CONSTRAINT endereco_escola_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad)
				REFERENCES pmieducar.usuario (cod_usuario) MATCH SIMPLE
				ON UPDATE RESTRICT ON DELETE RESTRICT;

			ALTER TABLE cadastro.endereco_escola ADD CONSTRAINT endereco_escola_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc)
				REFERENCES pmieducar.usuario (cod_usuario) MATCH SIMPLE
				ON UPDATE RESTRICT ON DELETE RESTRICT;
			"
		);
	}
}
