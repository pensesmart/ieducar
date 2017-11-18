<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaLatLongEnderecoPessoa extends AbstractMigration
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

		// atualizar tabela inserindo campos
		$table = $this->table('endereco_pessoa');
		$table
			->addColumn('lat', 'float', ['precision' => true])
			->addColumn('long', 'float', ['precision' => true])
			->update();
	}
}
