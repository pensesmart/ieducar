<?php

use Phinx\Migration\AbstractMigration;

class RemoveColunaDataInstituicao extends AbstractMigration
{
	public function up()
	{
		// Alterando de schema
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'pmieducar']));

		$table = $this->table('instituicao');
		$table
			->removeColumn('data_base_remanejamento')
			->removeColumn('data_base_transferencia')
			->update();
	}

	public function down()
	{
		// Alterando de schema
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'pmieducar']));

		$table = $this->table('instituicao');
		$table
			->addColumn('data_base_remanejamento', 'date')
			->addColumn('data_base_transferencia', 'date')
			->update();
	}
}
