<?php

use Phinx\Migration\AbstractMigration;

class AlteraCnpjOpcionalJuridica extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * Write your reversible migrations using this method.
	 *
	 * More information on writing migrations is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
	 *
	 * The following commands can be used in this method and Phinx will
	 * automatically reverse them when rolling back:
	 *
	 *    createTable
	 *    renameTable
	 *    addColumn
	 *    renameColumn
	 *    addIndex
	 *    addForeignKey
	 *
	 * Remember to call "create()" or "update()" and NOT "save()" when working
	 * with the Table class.
	 */
	public function change()
	{
		// Alterando de schema
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'cadastro']));

		$this->execute("DROP VIEW v_pessoafj_count;");
		$this->execute("DROP VIEW v_pessoa_juridica;");
		$this->execute("DROP VIEW v_pessoa_fj;");

		$cadastro = $this->table('juridica');
		$cadastro
			->changeColumn('cnpj', 'integer', ['limit' => 14, 'null' => true])
			->update();

		$this->execute("CREATE VIEW v_pessoafj_count AS
			SELECT cadastro.fisica.ref_cod_sistema, cadastro.fisica.cpf AS id_federal FROM cadastro.fisica UNION ALL SELECT NULL::integer AS ref_cod_sistema, cadastro.juridica.cnpj AS id_federal FROM cadastro.juridica;");

		$this->execute("CREATE VIEW v_pessoa_juridica AS
			SELECT j.idpes, j.fantasia, j.cnpj, j.insc_estadual, j.capital_social, (SELECT cadastro.pessoa.nome FROM cadastro.pessoa WHERE (cadastro.pessoa.idpes = j.idpes)) AS nome FROM cadastro.juridica j;");

		$this->execute("CREATE VIEW v_pessoa_fj AS
			SELECT p.idpes, p.nome, (SELECT cadastro.fisica.ref_cod_sistema FROM cadastro.fisica WHERE (cadastro.fisica.idpes = p.idpes)) AS ref_cod_sistema,
			(SELECT cadastro.juridica.fantasia FROM cadastro.juridica WHERE (cadastro.juridica.idpes = p.idpes)) AS fantasia, p.tipo, COALESCE((SELECT cadastro.fisica.cpf FROM cadastro.fisica WHERE (cadastro.fisica.idpes = p.idpes)), (SELECT cadastro.juridica.cnpj FROM cadastro.juridica WHERE (cadastro.juridica.idpes = p.idpes))) AS id_federal FROM pessoa p;");

		// Alterando de schema
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'historico']));

		$historico = $this->table('juridica');
		$historico
			->changeColumn('cnpj', 'integer', ['limit' => 14, 'null' => true])
			->update();
	}
}
