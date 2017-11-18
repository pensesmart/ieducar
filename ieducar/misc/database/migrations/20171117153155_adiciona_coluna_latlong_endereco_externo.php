<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaLatLongEnderecoExterno extends AbstractMigration
{
	public function change()
	{
		// Alterando de schema
		$this->getAdapter()->setOptions(array_replace($this->getAdapter()->getOptions(), ['schema' => 'cadastro']));

		// atualizar tabela inserindo campos
		$table = $this->table('endereco_externo');
		$table
			->addColumn('lat', 'float', ['precision' => true])
			->addColumn('long', 'float', ['precision' => true])
			->update();
	}
}
