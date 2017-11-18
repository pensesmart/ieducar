<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoPreInscritoAluno extends AbstractMigration
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
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'pmieducar']));

		$table = $this->table('aluno');
		$table
			->addColumn('ref_cod_inscrito',	'integer')
			->addIndex(['ref_cod_inscrito'], ['unique' => true])
			->addForeignKey('ref_cod_inscrito',
				'selecao_inscrito',
				'cod_inscrito', ['constraint' => 'aluno_ref_cod_inscrito', 'delete'=> 'RESTRICT', 'update'=> 'RESTRICT'])
			->update();
	}
}
