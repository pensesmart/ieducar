<?php


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																		 *
*	@author Smart Consultoria e Desenvolvimento WEB						 *
*	@updated 17/09/2016													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2016	Smart Consultoria e Desenvolvimento Web			 *
*						medaumoi@pensesmart.com							 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

require_once 'CoreExt/Enum.php';

class App_Model_PrioridadeVPS extends CoreExt_Enum
{
	const SEM_ENTREVISTA_0	= 0;
	const MUITO_BAIXA_1		= 1;
	const BAIXA_2			= 2;
	const BAIXA_3			= 3;
	const BAIXA_4			= 4;
	const MEDIA_5			= 5;
	const MEDIA_6			= 6;
	const MEDIA_7			= 7;
	const ALTA_8			= 8;
	const ALTA_9			= 9;
	const MUITO_ALTA_10		= 10;

	protected $_data = array(
		self::SEM_ENTREVISTA_0		=> '0 - N�o enviar entrevistas',
		self::MUITO_BAIXA_1			=> '1 - Muito Baixa',
		self::BAIXA_2				=> '2 - Baixa',
		self::BAIXA_3				=> '3 - Baixa',
		self::BAIXA_4				=> '4 - Baixa',
		self::MEDIA_5				=> '5 - M�dia',
		self::MEDIA_6				=> '6 - M�dia',
		self::MEDIA_7				=> '7 - M�dia',
		self::ALTA_8				=> '8 - Alta',
		self::ALTA_9				=> '9 - Alta (Participou poucas entrevistas)',
		self::MUITO_ALTA_10			=> '10 - Alta (N�o foi enviado entrevista)'
	);

	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}
}
