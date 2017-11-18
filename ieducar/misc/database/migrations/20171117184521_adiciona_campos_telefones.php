<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCamposTelefones extends AbstractMigration
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
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'pmicontrolesis']));

		// atualizar tabela inserindo campos
		$table = $this->table('telefones');
		$table
			->addColumn('responsavel',	'string')
			->addColumn('ddd_numero', 	'integer',	['limit' => 3])
			->addColumn('ddd_celular',	'integer',	['limit' => 3])
			->addColumn('celular',		'string')
			->addColumn('email',		'string',	['limit' => 255])
			->addColumn('endereco',		'string')
			->update();
	}
}
