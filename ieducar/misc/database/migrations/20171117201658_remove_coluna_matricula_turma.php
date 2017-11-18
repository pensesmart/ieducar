<?php

use Phinx\Migration\AbstractMigration;

class RemoveColunaMatriculaTurma extends AbstractMigration
{
	public function up()
	{
		// Alterando de schema
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'pmieducar']));

		$table = $this->table('matricula_turma');
		$table
			->removeColumn('transferido')
			->removeColumn('remanejado')
			->update();
	}

	public function down()
	{
		// Alterando de schema
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'pmieducar']));

		$table = $this->table('matricula_turma');
		$table
			->addColumn('data_base_remanejamento', 'boolean')
			->addColumn('data_base_transferencia', 'boolean')
			->update();
	}
}
