<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Entity
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

namespace CoreExt;

use Exception;
use CoreExt\Locale;
use CoreExt\CoreExt_DataMapper;
use CoreExt\Entity\EntityValidatable;
use CoreExt\Validate\CoreExt_Validate_Abstract;
use CoreExt\Validate\CoreExt_Validate_Interface;
use CoreExt\Exception\InvalidArgumentException;

/**
 * Entity abstract class.
 *
 * Um layer supertype para objetos da camada de dom�nio de todos os namespaces
 * da aplica��o.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @link      http://martinfowler.com/eaaCatalog/layerSupertype.html
 * @package   Entity
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @todo      Possibilitar uso opcional do campo identidade, �til para casos
 *   de compound primary keys
 * @version   @@package_version@@
 */
abstract class Entity implements EntityValidatable
{
	/**
	* Define se uma inst�ncia � "nova" ou "velha" (caso seja carregada via
	* CoreExt_DataMapper).
	* @var bool
	*/
	protected $_new = TRUE;

	/**
	* Array associativo onde os �ndices se comportar�o como atributos p�blicos
	* gra�as a implementa��o dos m�todos m�gicos de overload.
	*
	* @var array
	*/
	protected $_data = array();

	/**
	* Array associativo onde os �ndices identificam o tipo de dado de uma
	* propriedade p�blica tal qual declarada em $_data.
	*
	* @var array
	* @see _getValue() Cont�m a lista de tipos atualmente suportados
	*/
	protected $_dataTypes = array();

	/**
	* @var CoreExt_DataMapper
	*/
	protected $_dataMapper = NULL;

	/**
	* Array associativo para refer�ncias a objetos que ser�o carregados com
	* lazy load.
	*
	* Uma reference pode ser de um dos tipos:
	* - CoreExt_DataMapper
	* - Enum
	*
	* Toda vez que uma reference for requisitada e acessada pela primeira vez,
	* ela n�o far� mais lookups (buscas SQL, acesso a arrays de valores).
	*
	* <code>
	* <?php
	* $_references = array(
	*   'area' => array(
	*     'value' => 1,
	*     'class' => 'AreaConhecimento_Model_AreaDataMapper',
	*     'file'  => 'AreaConhecimento/Model/AreaDataMapper.php'
	*   )
	* );
	* </code>
	*
	* @link http://martinfowler.com/eaaCatalog/lazyLoad.html Lazy load
	* @var array
	*/
	protected $_references = array();

	/**
	* Cole��o de validadores para as propriedades $_data de Entity.
	* @var array
	*/
	protected $_validators = array();

	/**
	* Cole��o de mensagens de erro retornado pelos validadores de $_validators
	* durante a execu��o do m�todo isValid().
	*
	* @var array
	*/
	protected $_errors = array();

	/**
	* Array com inst�ncias para classes pertecentes ao namespace iEd_*.
	*
	* <code>
	* <?php
	* $_classStorage = array(
	*   'stdclass' => array(
	*     'class'    => 'stdClass',
	*     'instance' => NULL,
	*   )
	* );
	* </code>
	*
	* @see Entity#addClassToStorage($class, $instance = NULL, $file = NULL)
	* @var array
	*/
	protected static $_classStorage = array();

	/**
	* @var Locale
	*/
	protected $_locale = NULL;

	/**
	* Construtor.
	*
	* @param array $options Array associativo para inicializar os valores dos
	*   atributos do objeto
	*/
	public function __construct($options = array())
	{
		$this->_createIdentityField()->setOptions($options);
	 }

	 /**
	 * Adiciona um campo identidade como atributo da inst�ncia.
	 *
	 * @link   http://martinfowler.com/eaaCatalog/identityField.html
	 * @return Entity Prov� interface flu�da
	 */
	 protected function _createIdentityField()
	 {
		 $id = array('id' => NULL);
		 $this->_data = array_merge($id, $this->_data);
		 return $this;
	}

	/**
	* Atribui valor para cada atributo da classe que tenha correspond�ncia com
	* o indice do array $options passado como argumento.
	*
	* @param  array $options
	* @return Entity Prov� interface flu�da
	*/
	public function setOptions($options = array())
	{
		foreach ($options as $key => $value)
		{
			$this->$key = $value;
		}
		return $this;
	}

	/**
	* Implementa��o do m�todo m�gico __set().
	*
	* Esse m�todo � um pouco complicado devido a l�gica de configura��o das
	* refer�ncias para lazy loading.
	*
	* @link   http://php.net/manual/en/language.oop5.overloading.php
	* @param  string $key
	* @param  mixed  $val
	* @return bool|null TRUE caso seja uma refer�ncia v�lida ou NULL para o fluxo
	*   normal do m�todo
	*/
	public function __set($key, $val)
	{
		if ($this->_hasReference($key))
		{
			// Se houver uma refer�ncia e ela pode ser NULL, atribui NULL quando
			// a refer�ncia for carregada por CoreExt_DataMapper (new = FALSE).
			// Se for uma refer�ncia a CoreExt_DataMapper, 0 ser� equivalente a NULL.
			// Aqui, nem inst�ncia tem, nem lazy load acontecer�.
			if (isset($this->_references[$key]['null']) && $this->_references[$key]['null'] &&
			(is_null($val) || (FALSE == $this->_new && "NULL" == $val) || 
			($this->_isReferenceDataMapper($key) && (is_numeric($val) && 0 == $val))))
			{
				$this->_references[$key]['value'] = NULL;
				return TRUE;
			}

			// Se a refer�ncia for num�rica, usa-a, marcando apenas a refer�ncia e
			// deixando o atributo NULL para o lazy load.
			if (is_numeric($val))
			{
				$this->_references[$key]['value'] = $this->_getValue($key, $val);
				return TRUE;
			}

			// Se for uma inst�ncia de Entity e tiver um identificador,
			// usa-o. Refer�ncias sem um valor poder�o ser consideradas como novas
			// numa implementa��o de save() de CoreExt_DataMapper que leve em
			// considera��o as refer�ncias, salvando-as ou atualizando-as.
			elseif ($val instanceof Entity && isset($val->id))
			{
				$this->_references[$key]['value'] = $this->_getValue($key, $val->id);
				// N�o retorna, queremos aproveitar a inst�ncia para n�o mais carreg�-la
				// em __get().
			}

			// Aqui, identificamos que o atributo n�o se encaixa em nenhum dos itens
			// anteriores, lan�ando um Exce��o. Como Enum n�o cont�m um
			// estado (o valor corrente, por ser um Enum!), aceitamos apenas
			// inst�ncias de Entity como par�metro
			elseif (!($val instanceof Entity))
			{
				throw new InvalidArgumentException('O argumento passado para o atributo "' . $key
				. '" � inv�lido. Apenas os tipos "int" e "Entity" s�o suportados.');
			}
		}

		// Se o atributo n�o existir, lan�a exce��o
		if (!array_key_exists($key, $this->_data))
		{
			throw new InvalidArgumentException('A propriedade '
			. $key . ' n�o existe em ' . __CLASS__ . '.');
		}

		// Se for string vazia, o valor � NULL
		if ('' == trim($val))
		{
			$this->_data[$key] = NULL;
		} else {
			// Chama _getValue(), para fazer convers�es que forem necess�rias
			$this->_data[$key] = $this->_getValue($key, $val);
		}
	}

	/**
	* Implementa��o do m�todo m�gico __get().
	*
	* @link   http://php.net/manual/en/language.oop5.overloading.php
	* @param  string $key
	* @return mixed
	*/
	public function __get($key)
	{
		if ('id' === $key)
		{
			return floatval($this->_data[$key]) > 0  ? floatval($this->_data[$key]) : NULL;
		}

		if ($this->_hasReference($key) && !isset($this->_data[$key]))
		{
			$this->_data[$key] = $this->_loadReference($key);
		}

		return $this->_data[$key];
	}

	/**
	* Getter. N�o resolve refer�ncias com lazy load, ao inv�s disso, retorna
	* o valor da refer�ncia.
	*
	* @param  string $key
	* @return mixed
	*/
	public function get($key)
	{
		if ($this->_hasReference($key))
		{
			return $this->_getReferenceValue($key);
		}
		return $this->__get($key);
	}


	/**
	* Implementa��o do m�todo m�gico __isset().
	*
	* @link   http://php.net/manual/en/language.oop5.overloading.php
	* @param  string $key
	* @return bool
	*/
	public function __isset($key)
	{
		$val = $this->get($key);
		return isset($val);
	}

	/**
	* Implementa��o do m�todo m�gico __unset().
	*
	* @link  http://php.net/manual/en/language.oop5.overloading.php
	* @param string $key
	*/
	public function __unset($key)
	{
		$this->_data[$key] = NULL;
	}

	/**
	* Implementa��o do m�todo m�gico __toString().
	*
	* @link http://br2.php.net/manual/en/language.oop5.magic.php#language.oop5.magic.tostring
	* @return string
	*/
	public function __toString()
	{
		return get_class($this);
	}


	/**
	* Carrega um objeto de uma refer�ncia, usando o CoreExt_DataMapper
	* especificado para tal.
	*
	* @param  string $key
	* @return Entity
	* @todo   Se mais classes puderem ser atribu�das como references, implementar
	*         algum design pattern para diminuir a complexidade ciclom�tica
	*         desse m�todo e de setReferenceClass().
	* @todo   Caso a classe n�o seja um CoreExt_DataMapper ou Enum,
	*         lan�ar uma Exception.
	* @todo   Refer�ncias Enum s� podem ter seu valor atribu�do na
	*         instancia��o. Verificar se isso � desejado e ver possibilidade
	*         de flexibilizar esse comportamente. Ver para CoreExt_DataMapper
	*         tamb�m.
	*/
	protected function _loadReference($key)
	{
		$reference = $this->_references[$key];

		// Se a refer�ncia tiver valor NULL
		$value = $reference['value'];
		if (in_array($value, array(NULL), true))
		{
			return $value;
		}

		// Verifica se a API da classe para saber qual tipo de instancia��o usar
		$class = $reference['class'];
		if ($this->_isReferenceDataMapper($key))
		{
			$class  = new $class();
		} elseif ($this->_isReferenceEnum($key)) {
			$class = $class . '::getInstance()';
			eval('?><?php $class = ' . $class . '?>');
		}

		// Faz a chamada a API, recupera o valor original (objetos). Usa a inst�ncia
		// da classe.
		if ($class instanceof CoreExt_DataMapper)
		{
			$return = $class->find($value);
		} elseif ($class instanceof Enum) {
			if (!isset($class[$value])) {
				return NULL;
			}
			$return = $class[$value];
		}

		return $return;
	}

	/**
	* Verifica se existe uma refer�ncia para uma certa chave $key.
	* @param string $key
	* @return bool
	*/
	protected function _hasReference($key)
	{
		return array_key_exists($key, $this->_references);
	}

	/**
	* Configura ou adiciona uma nova refer�ncia para possibilitar o lazy load
	* entre objetos.
	*
	* @param  string  $key   O nome do atributo que mapeia para a refer�ncia
	* @param  array   $data  O array com a especifica��o da refer�ncia
	* @return Entity Prov� interface flu�da
	* @throws InvalidArgumentException
	*/
	public function setReference($key, $data)
	{
		if (!array_key_exists($key, $this->_data))
		{
			throw new InvalidArgumentException('Somente � poss�vel '
			. 'criar refer�ncias para atributos da classe.');
		}

		$layout = array('value' => NULL, 'class' => NULL, 'file' => NULL, 'null' => NULL);

		$options       = array_keys($layout);
		$passedOptions = array_keys($data);

		if (0 < count($diff = array_diff($passedOptions, $options)))
		{
			throw new InvalidArgumentException("" . implode(', ', $diff));
		}

		if (!array_key_exists($key, $this->_references))
		{
			$this->_references[$key] = $layout;
		}
		if (isset($data['value']))
		{
			$this->setReferenceValue($key, $data['value']);
		}
		if (isset($data['class']))
		{
			$this->setReferenceClass($key, $data['class']);
		}
		if (isset($data['file']))
		{
			$this->setReferenceFile($key, $data['file']);
		}

		return $this;
	}

	/**
	* Setter para o valor de refer�ncia de uma reference.
	* @param  string $key
	* @param  int    $value
	* @return Entity Prov� interface flu�da
	*/
	public function setReferenceValue($key, $value)
	{
		$this->_references[$key]['value'] = (int) $value;
		return $this;
	}

	/**
	* Setter para uma classe ou nome de classe de um CoreExt_DataMapper da
	* reference.
	* @param  string $key
	* @param  CoreExt_DataMapper|Enum|string $class
	* @return Entity Prov� interface flu�da
	* @throws InvalidArgumentException
	*/
	public function setReferenceClass($key, $class)
	{
		if (!is_string($class) && !($class instanceof CoreExt_DataMapper || $class instanceof Enum))
		{
			throw new InvalidArgumentException('Uma classe de refer�ncia '
			. ' precisa ser especificada pelo seu nome (string), ou, uma inst�ncia de CoreExt_DataMapper ou Enum.');
		}
		
		$this->_references[$key]['class'] = $class;
		return $this;
	}

	/**
	* Setter para o arquivo da classe CoreExt_DataMapper da classe de reference
	* informada por setReferenceClass.
	* @param  string $key
	* @param  int    $value
	* @return Entity Prov� interface flu�da
	*/
	public function setReferenceFile($key, $file)
	{
		$this->_references[$key]['file'] = $file;
		return $this;
	}

	/**
	* Getter.
	* @param  string $key
	* @return mixed
	*/
	protected function _getReferenceValue($key)
	{
		return $this->_references[$key]['value'];
	}

	/**
	* Getter.
	* @param string $key
	* @return string
	*/
	protected function _getReferenceClass($key)
	{
		return $this->_references[$key]['class'];
	}

	/**
	* Verifica se a classe da refer�ncia � uma inst�ncia de CoreExt_DataMapper.
	* @param string $key
	* @return bool
	*/
	protected function _isReferenceDataMapper($key)
	{
		$class = $this->_getReferenceClass($key);

		return $this->_isReferenceOf($class, $this->_references[$key]['file'], 'CoreExt_DataMapper');
	}

	/**
	* Verifica se a classe da refer�ncia � uma inst�ncia de Enum.
	* @param string $key
	* @return bool
	*/
	protected function _isReferenceEnum($key)
	{
		$class = $this->_getReferenceClass($key);

		return $this->_isReferenceOf($class, $this->_references[$key]['file'], 'Enum');
	}

	/**
	* Verifica se a refer�ncia � subclasse de $parentClass.
	*
	* @param string $subClass
	* @param string $subClassFile
	* @param string $parentClass
	* @return bool
	*/
	private function _isReferenceOf($subClass, $subClassFile, $parentClass)
	{
		static $required = array();

		if (is_string($subClass))
		{
			if (!in_array($subClassFile, $required))
			{
				// Inclui o arquivo com a defini��o de subclasse para que o interpretador
				// tenha o s�mbolo de compara��o.
				require_once $subClassFile;
				$required[] = $subClassFile;
			}

			return (is_subclass_of($subClass, $parentClass));
		}
		return FALSE;
	}

	/**
	* Setter.
	* @param CoreExt_DataMapper $dataMapper
	* @return Entity
	*/
	public function setDataMapper(CoreExt_DataMapper $dataMapper)
	{
		$this->_dataMapper = $dataMapper;
		return $this;
	}

	/**
	* Getter.
	* @return CoreExt_DataMapper|null
	*/
	public function getDataMapper()
	{
		return $this->_dataMapper;
	}

	/**
	* Adiciona uma classe para o reposit�rio de classes est�tico, instanciando
	* caso n�o seja passada uma inst�ncia expl�cita e carregando o arquivo
	* em que a classe est� armazenada caso seja informado.
	*
	* Quando uma inst�ncia n�o � passada explicitamente, verifica-se se a
	* inst�ncia j� existe, retornado-a caso positivo e/ou instanciando uma nova
	* (sem passar argumentos para seu construtor) e retornando-a.
	*
	* Permite armazenar apenas uma inst�ncia de uma classe por vez. Por usar
	* armazenamento est�tico, pode ter efeitos indesejados ao ser usado por
	* diferentes objetos.
	*
	* Caso seja necess�rio instanciar a classe passando argumentos ao seu
	* construtor, instancie a classe e passe a referencia na chamada ao m�todo:
	*
	* <code>
	* <?php
	* $obj = new Entity(array('key1' => 'value1'));
	* Entity::addClassToStorage('Entity', $obj);
	* </code>
	*
	* @param  string  $class     O nome da classe
	* @param  mixed   $instance  Uma inst�ncia da classe
	* @param  string  $file      O nome do arquivo onde se encontra a classe
	* @param  bool    $sticky    Se a inst�ncia da classe de ser "grundenda",
	*   n�o podendo ser posteriormente substitu�da por uma chamada subsequente
	* @return mixed
	* @throws InvalidArgumentException
	*/
	public static function addClassToStorage($class, $instance = NULL, $file = NULL, $sticky = FALSE)
	{
		$search = strtolower($class);
		
		if (array_key_exists($search, self::$_classStorage) === true)
		{
			self::_setStorageClassInstance($search, $instance, $sticky);
		} else {
			if (!is_null($file))
			{
				require_once $file;
			}

			self::$_classStorage[$search] = array(
				'class' => $class,
				'instance' => NULL,
				'sticky' => FALSE
			);

			self::_setStorageClassInstance($class, $instance, $sticky);
		}
		return self::$_classStorage[$search]['instance'];
	}

	/**
	* Instancia uma classe de $class ou atribui uma inst�ncia passada
	* explicitamente para o reposit�rio de classes est�tico.
	*
	* @param string $class
	* @param mixed $instance
	* @return mixed
	* @throws InvalidArgumentException
	*/
	protected static function _setStorageClassInstance($class, $instance = NULL, $sticky = FALSE)
	{
		if (!is_null($instance))
		{
			if (!($instance instanceof $class))
			{
				throw new InvalidArgumentException('A inst�ncia '
				. 'passada como argumento precisa ser uma inst�ncia de "' . $class . '".');
			}

			if (FALSE == self::$_classStorage[strtolower($class)]['sticky'])
			{
				self::$_classStorage[strtolower($class)]['instance'] = $instance;
				self::$_classStorage[strtolower($class)]['sticky']   = $sticky;
			}
			// Se for sticky, s� sobrescreve por outro
			elseif (TRUE == self::$_classStorage[strtolower($class)]['sticky'] && TRUE == $sticky)
			{
				self::$_classStorage[strtolower($class)]['instance'] = $instance;
				self::$_classStorage[strtolower($class)]['sticky']   = $sticky;
			}
		} else {
			if (is_null(self::$_classStorage[strtolower($class)]['instance']))
			{
				self::$_classStorage[strtolower($class)]['instance'] = new $class();
				self::$_classStorage[strtolower($class)]['sticky']   = $sticky;
			}
		}
	}

	/**
	* Getter.
	* @param string $class
	* @return mixed|null
	*/
	public static function getClassFromStorage($class)
	{
		if (self::hasClassInStorage($class))
		{
			return self::$_classStorage[strtolower($class)]['instance'];
		}
		return NULL;
	}

	/**
	* Verifica se uma classe existe no reposit�rio de classes est�tico.
	* @param string $class
	* @return bool
	*/
	public static function hasClassInStorage($class)
	{
		if (array_key_exists(strtolower($class), self::$_classStorage))
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Setter.
	* @param Locale $instance
	* @return CoreExt_DataMapper Prov� interface flu�da
	*/
	public function setLocale(Locale $instance)
	{
		$this->_locale = $instance;
		return $this;
	}

	/**
	* Getter.
	* @return Locale
	*/
	public function getLocale()
	{
		if (is_null($this->_locale))
		{
			$this->setLocale(Locale::getInstance());
		}
		return $this->_locale;
	}

	/**
	* Verifica se a propriedade informada por $key � v�lida, executando o
	* CoreExt_Validate_Interface relacionado.
	*
	* Utiliza lazy initialization para inicializar os validadores somente quando
	* necess�rio.
	*
	* @link    http://martinfowler.com/eaaCatalog/lazyLoad.html Lazy initialization
	* @param   string  $key  Propriedade a ser validade. Caso seja string vazia,
	*   executa todos os validadores da inst�ncia
	* @return  bool
	* @see     CoreExt_Validate_Validatable#isValid($key)
	*/
	public function isValid($key = '')
	{
		$this->_setDefaultValidatorCollection()->_setDefaultErrorCollectionItems();

		$key = trim($key);
		$return = NULL;

		if ('' != $key && !is_null($this->getValidator($key)))
		{
			$return = $this->_isValidProperty($key);
		} elseif ('' === $key) {
			$return = $this->_isValidEntity();
		}

		return $return;
	}

	/**
	* Verifica se uma inst�ncia � nula, isto �, quando todos os seus atributos
	* tem o valor NULL.
	*
	* @return bool
	*/
	public function isNull()
	{
		$data  = $this->toDataArray();
		$count = count($this->_data);
		$nils  = 0;

		foreach ($data as $value)
		{
			if (is_null($value))
			{
				$nils++;
			}
		}

		if ($nils == $count)
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	* Alias para setNew(FALSE).
	* @return Entity Prov� interface flu�da
	*/
	public function markOld()
	{
		return $this->setNew(FALSE);
	}

	/**
	* Setter.
	* @param bool $new
	* @return Entity Prov� interface flu�da
	*/
	public function setNew($new)
	{
		$this->_new = (bool) $new;
		return $this;
	}

	/**
	* Verifica se a inst�ncia � "nova".
	* @return bool
	* @see Entity#_new
	*/
	public function isNew()
	{
		return $this->_new;
	}

	/**
	* Verifica se uma propriedade da classe � v�lida de acordo com um validador
	* CoreExt_Validate_Interface.
	*
	* Utiliza o valor sanitizado pelo validador como valor de atributo.
	*
	* @param  string $key
	* @return bool
	*/
	protected function _isValidProperty($key)
	{
		try
		{
			$this->getValidator($key)->isValid($this->get($key));
			$this->$key = $this->getValidator($key)->getSanitizedValue();
			return TRUE;
		} catch (Exception $e) {
			$this->_setError($key, $e->getMessage());
			return FALSE;
		}
	}

	/**
	* Verifica se todas as propriedades da classe s�o v�lida de acordo com uma
	* cole��o de validadores CoreExt_Validate_Interface.
	*
	* @return bool
	*/
	protected function _isValidEntity()
	{
		$return = TRUE;

		// Como eu quero todos os erros de valida��o, apenas marco $return como
		// FALSE e deixo o iterador exaurir.
		foreach ($this->getValidatorCollection() as $key => $validator)
		{
			if (FALSE === $this->_isValidProperty($key))
			{
				$return = FALSE;
			}
		}

		return $return;
	}

	/**
	* @see CoreExt_Validate_Validatable#setValidator($key, $validator)
	*/
	public function setValidator($key, CoreExt_Validate_Interface $validator)
	{
		if (!array_key_exists($key, $this->_data))
		{
			throw new Exception('A propriedade ' . $key . ' n�o existe em ' . __CLASS__ . '.');
		}

		$this->_validators[$key] = $validator;
		$this->_setError($key, NULL);
		return $this;
	}

	/**
	* @see CoreExt_Validate_Validatable#getValidator($key)
	*/
	public function getValidator($key)
	{
		$return = NULL;

		if (isset($this->_validators[$key]))
		{
			$return = $this->_validators[$key];
		}

		return $return;
	}

	/**
	* @param $overwrite TRUE para que as novas inst�ncias sobrescrevam as j�
	*   existentes
	* @see EntityValidatable#setValidatorCollection($validators)
	*/
	public function setValidatorCollection(array $validators, $overwrite = FALSE)
	{
		foreach ($validators as $key => $validator)
		{
			if ($overwrite == FALSE && !is_null($this->getValidator($key)))
			{
				continue;
			}
			$this->setValidator($key, $validator);
		}
		return $this;
	}

	/**
	* @see EntityValidatable#getValidatorCollection()
	*/
	public function getValidatorCollection()
	{
		$this->_setDefaultValidatorCollection();
		return $this->_validators;
	}

	/**
	* Configura os validadores padr�o da classe.
	* @return Entity Prov� interface flu�da
	*/
	protected function _setDefaultValidatorCollection()
	{
		$this->setValidatorCollection($this->getDefaultValidatorCollection());
		return $this;
	}

	/**
	* Retorna um inst�ncia de um validador caso um atributo da inst�ncia tenha
	* seu valor igual ao da condi��o.
	*
	* @param  string $key                 O atributo a ser comparado
	* @param  mixed  $value               O valor para compara��o
	* @param  string $validatorClassName  O nome da classe de valida��o. Deve ser
	*   subclasse de CoreExt_Validate_Abstract
	* @param  array  $equalsParams        Array de op��es para o a classe de
	*   valida��o caso de $key ser igual a $value
	* @param  array  $notEqualsParams     Array de op��es para o a classe de
	*   valida��o caso de $key ser diferente de $value
	* @return CoreExt_Validate_Abstract
	* @throws InvalidArgumentException
	*/
	public function validateIfEquals($key, $value = NULL, $validatorClassName,
		array $equalsParams = array(), array $notEqualsParams = array())
	{
		if ($value == $this->get($key))
		{
			$params = $equalsParams;
		} else {
			$params = $notEqualsParams;
		}

		if (!is_subclass_of($validatorClassName, 'CoreExt_Validate_Abstract'))
		{
			throw new InvalidArgumentException('A classe "'
			. $validatorClassName . '" n�o � uma subclasse de CoreExt_Validate_Abstract'
			. ' e por isso n�o pode ser usada como classe de valida��o.');
		}

		return new $validatorClassName($params);
	}

	/**
	* Configura uma mensagem de erro.
	*
	* @param  string $key
	* @param  string|null $message
	* @return Entity Prov� interface flu�da
	*/
	protected function _setError($key, $message = NULL)
	{
		$this->_errors[$key] = $message;
		return $this;
	}

	/**
	* Retorna uma mensagem de erro de valida�ao para determinada propriedade.
	*
	* @param  string $key
	* @return mixed
	*/
	public function getError($key)
	{
		return $this->_errors[$key];
	}

	/**
	* Retorna um array de mensagens de erro de valida��o.
	* @return array
	*/
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	* Verifica se uma propriedade tem um erro de valida��o.
	*
	* @param string $key
	* @return bool
	*/
	public function hasError($key)
	{
		if (!is_null($this->getError($key)))
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	* Verifica se houve algum erro de valida��o geral.
	* @return bool
	*/
	public function hasErrors()
	{
		foreach ($this->getErrors() as $key => $error)
		{
			if ($this->hasError($key))
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	* Configura os itens padr�o do array de erros.
	* @return Entity Prov� interface flu�da
	*/
	protected function _setDefaultErrorCollectionItems()
	{
		$items = array_keys($this->getValidatorCollection());
		$this->_errors = array_fill_keys($items, NULL);
		return $this;
	}

	/**
	* Retorna o valor de uma propriedade do objeto convertida para o seu tipo
	* qual definido pelo array $_dataTypes.
	*
	* Atualmente suporte os tipos:
	* - boolean (informado como bool ou boolean)
	* - numeric (converte para n�mero, usando informa��o do locale atual e
	*  convertendo para n�mero com {@link http://br.php.net/floatval floatval())}
	*
	* <code>
	* <?php
	* class Example extends CoreExtEntity {
	*   protected $_data = array('hasChild' => NULL);
	*   protected $_dataTypes = array('hasChild' => 'bool');
	* }
	* </code>
	*
	* @param  string $key O nome da propriedade
	* @param  mixed  $val O valor original da propriedade
	* @return mixed  O valor convertido da propriedade
	*/
	protected function _getValue($key, $val)
	{
		if (!array_key_exists($key, $this->_dataTypes))
		{
			// Converte com floatval (que converte para int caso n�o tenha decimais,
			// para permitir formata��o correta com o locale da aplica��o)
			if (is_numeric($val))
			{
				$val = floatval($val);
			}
			return $val;
		}

		$cmpVal = strtolower($val);
		$return = NULL;

		switch (strtolower($this->_dataTypes[$key]))
		{
			case 'bool':
			case 'boolean':
			if ($cmpVal == 't') {
				$return = TRUE;
			} elseif ($cmpVal == 'f') {
				$return = FALSE;
			} else {
				$return = (bool) $cmpVal;
			}
			break;

			case 'numeric':
			$return = $this->getFloat($cmpVal);
			break;

			case 'string':
			$return = (string) $cmpVal;
			break;
		}
		return $return;
	}

	/**
	* Retorna um n�mero float, verificando o locale e substituindo o separador
	* decimal pelo separador compat�vel com o separador padr�o do PHP ("." ponto).
	*
	* @param int $value
	* @return float
	*/
	public function getFloat($value)
	{
		$locale = $this->getLocale();
		$decimalPoint = $locale->getCultureInfo('decimal_point');

		// Verifica se possui o ponto decimal do locale e substitui para o
		// padr�o do locale en_US (ponto ".")
		if (strstr($value, $decimalPoint) != false)
		{
			$value = strtr($value, $decimalPoint, '.');
		}

		return floatval($value);
	}

	/**
	* Retorna um array onde o �ndice � o valor do atributo $atr1 e o valor
	* � o pr�prio valor do atributo referenciado por $atr2. Se $atr2 n�o for
	* informado, retorna o valor referenciado por $atr1.
	*
	* Exemplo:
	* <code>
	* <?php
	* // class Pessoa extends Entity
	* protected $_data = array(
	*   'nome' => NULL,
	*   'sobrenome' => NULL
	* );
	*
	* // em um script:
	* $pessoa = new Pessoa(array('id' => 1, 'nome' => 'Carlos Santana'));
	* print_r($pessoa->filterAttr('id' => 'nome');
	*
	* // Iria imprimir:
	* // Array
	* // (
	* //    [1] => Carlos Santana
	* // )
	* </code>
	*
	* @param string $atr1
	* @param string $atr2
	* @return array
	*/
	public function filterAttr($atr1, $atr2 = '')
	{
		$data = array();

		if ('' == $atr2)
		{
			$atr2 = $atr1;
		}

		$data[$this->$atr1] = $this->$atr2;
		return $data;
	}

	/**
	* Retorna um array para cada inst�ncia de Entity, onde cada entrada
	* � um array onde o �ndice � o valor do atributo $atr1 e o valor
	* � o pr�prio valor do atributo referenciado por $atr2. Se $atr2 n�o for
	* informado, retorna o valor referenciado por $atr1.
	*
	* @param  Entity|array $instance
	* @param  string $atr1
	* @param  string $atr2
	* @return array
	* @see    Entity#filterAttr($atr1, $atr2)
	*/
	public static function entityFilterAttr($instance, $atr1, $atr2 = '')
	{
		$instances = $data = array();

		if (!is_array($instance))
		{
			$instances[] = $instance;
		} else {
			$instances = $instance;
		}

		foreach ($instances as $instance)
		{
			$arr = $instance->filterAttr($atr1, $atr2);
			$key = key($arr);
			$data[$key] = $arr[$key];
		}

		return $data;
	}

	/**
	* Retorna o estado (valores dos atributos) da inst�ncia em array. Se um
	* atributo for uma refer�ncia a um objeto, faz o lazy load do mesmo.
	* @return array
	*/
	public function toArray()
	{
		$data = array();
		foreach ($this->_data as $key => $val)
		{
			$data[$key] = $this->$key;
		}
		return $data;
	}

	/**
	* Retorna o estado (valores dos atributos) da inst�ncia. Se um atributo
	* for uma refer�ncia a um objeto, retorna o valor da refer�ncia.
	* @return array
	*/
	public function toDataArray()
	{
		$data = array();
		foreach ($this->_data as $key => $value)
		{
			if ($this->_hasReference($key))
			{
				$data[$key] = $this->_references[$key]['value'];
				continue;
			}
			$data[$key] = $value;
		}
		return $data;
	}
}
