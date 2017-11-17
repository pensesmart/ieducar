<?php

use Phinx\Migration\AbstractMigration;

class RemovePortalBanner extends AbstractMigration
{
	public function change()
	{
		$this->execute("
			DROP TABLE IF EXISTS portal_banner;
			DROP SEQUENCE IF EXISTS  portal_banner_cod_portal_banner_seq;"
		);
	}
}
