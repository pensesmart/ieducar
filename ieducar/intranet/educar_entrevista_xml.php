<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
*																		 *
*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
*	junto  com  este  programa. Se não, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
	header('Content-type: text/xml');

	require_once("include/clsBanco.inc.php");
	require_once("include/funcoes.inc.php");

	require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
	Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<query xmlns=\"colecoes\">\n";

	if(is_numeric($_GET["esc"]) && is_numeric($_GET["idpes"]))
	{
		$db = new clsBanco();
		$db->Consulta("
			SELECT
				cod_vps_responsavel_entrevista,
				nm_responsavel
			FROM
				pmieducar.vps_responsavel_entrevista
			WHERE
				ativo = 1
				AND ref_cod_escola = '{$_GET["esc"]}'
				AND ref_idpes = '{$_GET["idpes"]}'
			ORDER BY
				nm_responsavel ASC
		");

		if ($db->numLinhas())
		{
			while ($db->ProximoRegistro())
			{
				list($cod, $nome) = $db->Tupla();
				$nome = str_replace('&', 'e', $nome);
				echo "	<vps_responsavel_entrevista cod_vps_responsavel_entrevista=\"{$cod}\" >{$nome}</vps_responsavel_entrevista>\n";
			}
		}
	}
	echo "</query>";
?>
