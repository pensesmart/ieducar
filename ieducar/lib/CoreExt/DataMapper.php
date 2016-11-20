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
 * @package   CoreExt_DataMapper
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

/**
 * CoreExt_DataMapper abstract class.
 *
 * Implementa alguns dos conceitos do pattern Data Mapper de forma simples. A
 * inten��o � o de tornar o mapeamento objeto-relacional mais simples,
 * permitindo a cria��o de objetos de dom�nio novos que interajam com objetos
 * de dom�nio legados.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   CoreExt_DataMapper
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @todo      Refactoring dos m�todos get*Statment() com extract method
 * @todo      Testes para ordena��o em findAll()
 * @version   @@package_version@@
 */

namespace CoreExt;

use Exception;
use CoreExt\Locale;
use CoreExt\Entity;
use CoreExt\Exception\InvalidArgumentException;
use CoreExt\DataMapper\DataMapperException;

abstract class CoreExt_DataMapper
{
	/**
	* Classe CEntitypara este data mapper.
	* @var string
	*/
	protected $_entityClass = '';

	/**
	* Mapeamento objeto-relacional, atributos-campos.
	* @var array
	*/
	protected $_attributeMap = array();

	/**
	* Atributos n�o-persist�veis.
	* @var array
	*/
	protected $_notPersistable = array();

	/**
	* Define as chaves prim�rias da tabela. Configurada automaticamente para
	* usar o campo identidade de CoEntity
	* @see CoEntity_createIdentityField()
	* @var array
	*/
	protected $_primaryKey = array('id');

	/**
	* Objeto de conex�o com o banco de dados.
	* @var clsBanco
	*/
	protected $_dbAdapter = NULL;

	/**
	* Inst�ncia padr�o para uso em objetos CoreExt_DataMapper. Isso possibilita
	* que a mesma inst�ncia do adapter de conex�o com o banco de dados seja
	* reutilizado em v�rias inst�ncias de CoreExt_DataMapper.
	*
	* @var clsBanco
	*/
	protected static $_defaultDbAdapter = NULL;

	/**
	* Nome da tabela em que o objeto � mapeado.
	* @var string
	*/
	protected $_tableName = '';

	/**
	* Nome do schema da tabela.
	* @var string
	*/
	protected $_tableSchema = '';

	/**
	* @var CLocale
	*/
	protected $_locale = NULL;

	/**
	* Construtor.
	* @param clsBanco $db
	*/
	public function __construct(clsBanco $db = NULL)
	{
		if (!is_null($db))
		{
			$this->_setDbAdapter($db);
		}
	}

	/**
	* Setter para configura��o de um adapter de banco de dados padr�o usado
	* nas inst�ncias concretas de CoreExt_DataMapper quando nenhuma inst�ncia de
	* clsBanco � passada ao construtor.
	* @param clsBanco $db
	*/
	public static function setDefaultDbAdapter(clsBanco $db = NULL)
	{
		self::$_defaultDbAdapter = $db;
	}

	/**
	* Reseta o adapter padr�o, fazendo com que CoreExt_DataMapper instancie
	* automaticamente uma inst�ncia de clsBanco quando necess�rio.
	*/
	public static function resetDefaultDbAdapter()
	{
		self::setDefaultDbAdapter(NULL);
	}

	/**
	* Setter para o objeto de adapter respons�vel pela intera��o com o banco de
	* dados.
	*
	* @param  clsBanco $db
	* @return CoreExt_DataMapper Prov� interface flu�da
	*/
	protected function _setDbAdapter(clsBanco $db)
	{
		$this->_dbAdapter = $db;
		return $this;
	}

	/**
	* Getter para o objeto de adapter de banco de dados.
	*
	* Se nenhuma inst�ncia foi explicitamente passada ao construtor,
	* tenta atribuir uma inst�ncia por padr�o, na seguinte ordem:
	*
	* - Usando o adapter provido pelo m�todo est�tico setDefaultDbAdapter
	* (�til para usar v�rias inst�ncias de CoreExt_DataMapper sem a instancia��o
	* da classe clsBanco)
	* - Ou, instanciando a classe clsBanco
	*
	* Usar o setter est�tico tem a vantagem de reduzir o overhead causado pela
	* instancia��o a clsBanco a cada novo objeto CoreExt_DataMapper.
	*
	* @return clsBanco
	*/
	protected function _getDbAdapter()
	{
		if (is_null($this->_dbAdapter))
		{
			if (!is_null(self::$_defaultDbAdapter))
			{
				$adapter = self::$_defaultDbAdapter;
			} else {
				$adapter = new clsBanco(array('fetchMode' => clsBanco::FETCH_ASSOC));
			}
			$this->_setDbAdapter($adapter);
		}
		return $this->_dbAdapter;
	}

	/**
	* Getter p�blico para o objeto de adapter de banco de dados.
	* @return clsBanco
	*/
	public function getDbAdapter()
	{
		return $this->_getDbAdapter();
	}

	/**
	* Retorna o nome da tabela. Se o $_tableSchema for informado, retorna o
	* nome da tabela no formato 'schema.table'.
	*
	* @return string
	*/
	protected function _getTableName()
	{
		return $this->_tableSchema != '' ?
			$this->_tableSchema . '.' . $this->_tableName : $this->_tableName;
	}

	/**
	* Retorna o nome do recurso, isto � o nome da tabela sem '_',
	* Ex: transporte_aluno => transporte aluno.
	*
	* @return string
	*/
	public function resourceName()
	{
    return strtolower(str_replace('_', ' ', $this->_tableName));
	}

	/**
	* Retorna os nomes das colunas da tabela em um array, de acordo com o array
	* de dados associativo $data.
	*
	* Caso nenhum array seja passado, � usado o array de atributos da classe
	* CorEntity qual o data mapper mapeia.
	*
	* @param array $data
	* @return array
	*/
	protected function _getTableColumnsArray(array $data = array())
	{
		$columns = array();

		if (0 == count($data))
		{
			$tempEntity = new $this->_entityClass();
			$data = $tempEntity->toDataArray();
		}

		$tempColumns = array_map(array($this, '_getTableColumn'), array_keys($data));

		// Remove colunas n�o-persist�veis
		foreach ($tempColumns as $key => $column)
		{
			if (is_null($column))
			{
				continue;
			}
			$columns[] = $column;
		}

		return $columns;
	}

	/**
	* Retorna o nome do campo da tabela caso o identificador do atributo
	* esteja mapeado em $_attributeMap.
	*
	* Caso contr�rio, retorna o pr�prio identificador do atributo.
	*
	* @param string $key
	* @return string|null NULL para coluna n�o persist�vel
	*/
	protected function _getTableColumn($key)
	{
		if (in_array($key, $this->_notPersistable))
		{
			return NULL;
		}

		if (array_key_exists($key, $this->_attributeMap))
		{
			return $this->_attributeMap[$key];
		}
		return $key;
	}

	/**
	* Retorna os nomes das colunas da tabela separados por v�rgula e espa�o (', ').
	*
	* @param array $data
	* @return string
	*/
	protected function _getTableColumns(array $data = array())
	{
		return join(', ', $this->_getTableColumnsArray($data));
	}

	/**
	* Retorna uma query SQL de recupera��o de todos os registros de uma tabela.
	*
	* @param array $data
	* @param array $where
	* @param array $orderBy
	* @return string
	*/
	protected function _getFindAllStatment(array $data = array(), array $where = array(),
		array $orderBy = array())
	{
		$whereArg = $where;
		$where    = array();
		$order    = array();

		if (0 < count($whereArg))
		{
			foreach ($whereArg as $key => $value)
			{
				$whereName = $this->_getTableColumn($key);

				preg_match('/[<,=,>]/', $value, $matches);
				$hasComparisonSign = ! empty($matches);

				// Caso $value contenha <, > ou =, ex: '> $1', n�o adiciona sinal de igual.
				if($hasComparisonSign)
				$where[] = sprintf("%s %s", $whereName, $value);

				// Caso $value contenha parametros para consulta preparada ($1, $2...), n�o adiciona $value entre aspas.
				elseif(strpos($value, '$') > -1)
				$where[] = sprintf("%s = %s", $whereName, $value);

				else
				$where[] = sprintf("%s = '%s'", $whereName, $value);
			}
		} else {
			$where[] = '1 = 1';
		}

		if (0 < count($orderBy))
		{
			foreach ($orderBy as $key => $value)
			{
				$order[] = sprintf('%s %s', $this->_getTableColumn($key), $value);
			}
			$order = sprintf('ORDER BY %s', implode(',', $order));
		} else {
			$order = '';
		}

		return sprintf("SELECT %s FROM %s WHERE %s %s", $this->_getTableColumns($data),
			$this->_getTableName(), implode(' AND ', $where), $order);
	}

	/**
	* Retorna uma query SQL de recupera��o de registro baseada na identidade.
	*
	* Converte o argumento $pkey para float, como forma de
	* evitar os problemas do tipo int em ambientes 32 bit (mais especificamente,
	* a aus�ncia de um tipo long).
	*
	* @link   http://php.net/manual/en/language.types.integer.php
	* @link   http://php.net/manual/en/function.intval.php
	* @param  array|int $pkey
	* @return string
	*/
	protected function _getFindStatment($pkey)
	{
		$where = array();

		if (!is_array($pkey))
		{
			$where[] = sprintf("id = '%d'", floatval($pkey));
		} elseif (is_array($pkey)) {
			foreach ($pkey as $key => $pk)
			{
				if (is_int($key))
				{
					$whereName = $this->_getTableColumn($this->_primaryKey[$key]);
				} elseif (is_string($key)) {
					$whereName = $this->_getTableColumn($key);
				}
				$where[] = sprintf("%s = '%d'", $whereName, floatval($pk));
			}
		}

		return sprintf("SELECT %s FROM %s WHERE %s", $this->_getTableColumns(),
			$this->_getTableName(), implode(' AND ', $where));
	}

	/**
	* Retorna uma query SQL para a opera��o INSERT. Utiliza para isso os
	* atributos da inst�ncia CoreEntitym o cuidado de remover o
	* campo identidade.
	*
	* Uma query gerada por esse m�todo segue a forma:
	* <code>
	* INSERT INTO [schema.]table (column) VALUES ('value');
	* </code>
	*
	* @param CoreEEntitytance
	* @return string
	*/
	protected function _getSaveStatment(Entity $instance)
	{
		$sql = 'INSERT INTO %s (%s) VALUES (%s)';
		$data = $this->_getDbAdapter()->formatValues($instance->toDataArray());

		// Remove o campo identidade e campos n�o-persistentes
		$data = $this->_cleanData($data);

		// Pega apenas os valores do array
		$values = array_values($data);

		// Colunas para formar a query
		$columns = $this->_getTableColumns($data);

		// Trata os valores NULL diferentemente dos outros, para evitar erro
		// de execu��o query
		$valuesStmt = array();
		for ($i = 0, $count = count($values); $i < $count; $i++)
		{
			$value = $values[$i];
			if (is_null($value))
			{
				$value = "NULL";
				$replaceString = "%s";
			} else {
				$replaceString = "'%s'";
			}
			$valuesStmt[] = sprintf($replaceString, $value);
		}

		$valuesStmt = join(", ", $valuesStmt);
		return sprintf($sql, $this->_getTableName(), $columns, $valuesStmt);
	}

	/**
	* Retorna uma query SQL para a opera��o UPDATE. Utiliza para isso os
	* atributos da inst�ncia CoreExtEntityo o atributo identidade
	* para especificar qual registro atualizar na tabela.
	*
	* Uma query gerada por esse m�todo segue a forma:
	* <code>
	* UPDATE [schema.]table SET column='value' WHERE id = 'idValue';
	* </code>
	*
	* @param CoreExt_Entityce
	* @return string
	*/
	protected function _getUpdateStatment(Entity $instance)
	{
		$sql = 'UPDATE %s SET %s WHERE %s';
		$data = $this->_getDbAdapter()->formatValues($instance->toDataArray());

		// Remove o campo identidade e campos n�o-persistentes
		$data = $this->_cleanData($data);

		// Trata os valores NULL diferentemente dos outros, para evitar erro
		// de execu��o query
		$columns = array();
		foreach ($data as $key => $value)
		{
			$columnName = $this->_getTableColumn($key);
			if (is_null($value))
			{
				$value = "NULL";
				$replaceString = "%s = %s";
			} else {
				$replaceString = "%s = '%s'";
			}
			$columns[] = sprintf($replaceString, $columnName, $value);
		}

		$where = array();
		foreach ($this->_primaryKey as $pk)
		{
			$whereName = $this->_getTableColumn($pk);
			$where[] = sprintf("%s = '%d'", $whereName, $instance->get($pk));
		}

		return sprintf($sql, $this->_getTableName(), implode(', ', $columns), implode(' AND ', $where));
	}

	/**
	* Retorna uma query SQL para a opera��o DELETE. Utiliza para isso o
	* atributo identidade "id" (caso seja passada uma inst�ncia de CoreExt_EnEntitypar�metro) ou o valor inteiro passado como par�metro.
	*
	* Uma query gerada por esse m�todo segue a forma:
	* <code>
	* DELETE FROM [schema.]table WHERE id = 'idValue';
	* </code>
	*
	* @param mixed $instance
	* @return string
	*/
	protected function _getDeleteStatment($instance)
	{
		$sql = 'DELETE FROM %s WHERE %s';

		$where = array();
		if ($instance instanceof Entity)
		{
			foreach ($this->_primaryKey as $pk)
			{
				$whereName = $this->_getTableColumn($pk);
				//$where[] = sprintf("%s = '%d'", $whereName, $instance->get($pk)); estoura o decimal. valor 9801762824 retornando 1211828232
				$where[] = sprintf("%s = '%s'", $whereName, $instance->get($pk));
			}
		} elseif (is_numeric($instance)) {
			$where[] = sprintf("%s = '%d'", 'id', floatval($instance));
		} elseif (is_array($instance)) {
			foreach ($this->_primaryKey as $pk)
			{
				$whereName = $this->_getTableColumn($pk);
				$where[] = sprintf("%s = '%d'", $whereName, $instance[$pk]);
			}
		}

		return sprintf($sql, $this->_getTableName(), implode(' AND ', $where));
	}

	/**
	* Retorna todos os registros como objetos CoreExt_EntiEntitypela
	* query de _getFindAllStatment().
	*
	* @param  array $columns Atributos a serem carregados. O atributo id � sempre carregado.
	* @param  array $where
	* @param  array $orderBy
	* @param  array $addColumnIdIfNotSet Se true, adiciona a coluna 'id' caso n�o esteja definido no array $columns
	* @return array
	* @todo   Problema potencial com busca em registros com compount key. Testar.
	*/
	public function findAll(array $columns = array(), array $where = array(), array $orderBy = array(), $addColumnIdIfNotSet = true)
	{
		// Inverte chave valor, permitindo array simples como array('nome')
		if (0 < count($columns))
		{
			$columns = array_flip($columns);
			if (!isset($columns['id']) && $addColumnIdIfNotSet)
			{
				$columns['id'] = TRUE;
			}
		}

		// Reseta o locale para o default (en_US)
		$this->getLocale()->resetLocale();

		$this->_getDbAdapter()->Consulta($this->_getFindAllStatment($columns, $where, $orderBy));
		$list = array();

		// Retorna o locale para o usado no restante da aplica��o
		$this->getLocale()->setLocale();

		while ($this->_getDbAdapter()->ProximoRegistro())
		{
			$list[] = $this->_createEntityObject($this->_getDbAdapter()->Tupla());
		}

		return $list;
	}


	/**
	* Retorna todos os registros como objetos CoreExt_EntitEntityela
	* query de _getFindAllStatment() (usando consulta preparada, util para evitar sql injection).
	*
	* @param  array $columns Atributos a serem carregados. O atributo id � sempre carregado.
	* @param  array $where   Condicoes preparadas ex: array('arg1 = $1', 'arg2 = $2');
	* @param  array $params  Valor das condi�oes ($1, $2 ...) ex: array('1', '3');
	* @param  array $orderBy
	* @param  array $addColumnIdIfNotSet Se true, adiciona a coluna 'id' caso n�o esteja definido no array $columns
	* @return array
	* @todo
	*/
	public function findAllUsingPreparedQuery(array $columns = array(), array $where = array(), array $params = array(), array $orderBy = array(), $addColumnIdIfNotSet = true)
	{
		$list = array();

		// Inverte chave valor, permitindo array simples como array('nome')
		if (0 < count($columns))
		{
			$columns = array_flip($columns);
			if (!isset($columns['id']) && $addColumnIdIfNotSet)
			{
				$columns['id'] = TRUE;
			}
		}

		// Reseta o locale para o default (en_US)
		$this->getLocale()->resetLocale();

		$sql = $this->_getFindAllStatment($columns, $where, $orderBy);

		if ($this->_getDbAdapter()->execPreparedQuery($sql, $params) != false)
		{
			// Retorna o locale para o usado no restante da aplica��o
			$this->getLocale()->setLocale();

			while ($this->_getDbAdapter()->ProximoRegistro())
			{
				$list[] = $this->_createEntityObject($this->_getDbAdapter()->Tupla());
			}
		}

		return $list;
	}


	/**
	* Retorna um registro que tenha como identificador (chave �nica ou composta)
	* o valor dado por $pkey.
	*
	* @param  array|int $pkey
	* @return CoreExt_EntityEntityxception
	*/
	public function find($pkey)
	{
		$this->_getDbAdapter()->Consulta($this->_getFindStatment($pkey));

		if ($this->_getDbAdapter()->ProximoRegistro() === false)
		{
			$table = $this->_tableSchema . '.' . $this->_tableName;
			throw new Exception("Nenhum registro encontrado na tabela '$table' com a(s) chaves(s) informada(s): '$pkey'.");
		}
		return $this->_createEntityObject($this->_getDbAdapter()->Tupla());
	}

	/**
	* Salva ou atualiza um registro atrav�s de uma inst�ncia de CoreExt_Entity.Entitym  CoreExt_Entity Entityreturn bool
	* @throws DataMapperException|Exception
	*/
	public function save(Entity $instance)
	{
		if (!$instance->isValid())
		{
			throw new Exception('A inst�nca de "' . get_class($instance) . '" cont�m erros de valida��o.');
		}

		// Coumpound key, todos os valores precisam estar setados, seja para
		// INSERT ou UPDATE. A inst�ncia precisa ser marcada explicitamente
		// como "old" para que UPDATE seja chamado.
		if (1 < count($this->_primaryKey))
		{
			foreach ($this->_primaryKey as $pk)
			{
				$value = $instance->get($pk);
				if (!isset($value))
				{
					throw new DataMapperException('Erro de compound key. Uma das primary keys tem o valor NULL: "' . $pk . '"');
				}
			}
		}
		// Field identity, se estiver presente, marca inst�ncia como "old".
		elseif (1 == count($this->_primaryKey))
		{
			if (isset($instance->id))
			{
				$instance->markOld();
			}
		}

		// Reseta o locale para o default (en_US)
		$this->getLocale()->resetLocale();

		if ($instance->isNew())
		{
			return $this->_getDbAdapter()->Consulta($this->_getSaveStatment($instance));
		} else {
			return $this->_getDbAdapter()->Consulta($this->_getUpdateStatment($instance));
		}

		// Retorna o locale para o usado no restante da aplica��o
		$this->getLocale()->setLocale();
	}

	/**
	* Apaga um registro atrav�s de uma inst�ncia CoreExt_Entity. PoEntityrecebendo uma inst�ncia com as chaves prim�rias setadas ou um array
	* associativo de chaves prim�rias e seus valores.
	*
	* Exemplo:
	* <code>
	* <?php
	* $instance   = new CoreExt_Entity(arraEntity'pk2' => 2));
	* $dataMapper = new CoreExt_DataMapper();
	*
	* // Por valor do campo identidade 'id'
	* $dataMapper->delete(1);
	*
	* // Pelas chaves prim�rias de uma inst�ncia
	* $dataMapper->delete($instance);
	*
	* // Por um array de chaves associativas de chaves prim�rias
	* $dataMapper->delete(array('pk1' => 1, 'pk2' => 2));
	* </code>
	*
	* @param mixed $instance
	* @return bool
	*/
	public function delete($instance)
	{
		return $this->_getDbAdapter()->Consulta($this->_getDeleteStatment($instance));
	}

	/**
	* Retorna uma nova inst�ncia de CoreExt_Entity. A inEntityda n�o
	* produz efeito algum no comportamento de CoreExt_DataMapper.
	*
	* @return CoreExt_Entity
	*/
	public function  createNewEntityInstance(array $data = array())
	{
		return new $this->_entityClass($data);
	}

	/**
	*/
	protected function _cleanData(array $data)
	{
		if (array_key_exists('id', $data))
		{
			unset($data['id']);
		}

		// Remove dados n�o-persist�veis
		foreach ($this->_notPersistable as $field)
		{
			if (array_key_exists($field, $data))
			{
				unset($data[$field]);
			}
		}

		return $data;
	}

	/**
	* Cria um objeto CoreExt_Entity com os Entitympos relacionais
	* mapeados para os atributos da inst�ncia.
	*
	* @param array $data
	* @return CoreExt_Entity
	*/
	protected function _createEntityObject(array $data)
	{
		$instance = $this->createNewEntityInstance();
		$instance->markOld();
		$instance = $this->_mapData($data, $instance);

		return $instance;
	}

	/**
	* Mapeia os campos relacionais para os atributos de uma inst�ncia de
	* CoreExt_Entity.
	*
	* Entity$data
	* @param  CoreExt_Entity $instanceEntityreExt_Entity A inst�ncEntitybutos mapeados
	*/
	protected function _mapData($data, Entity $instance)
	{
		foreach($data as $key => $value)
		{
			try
			{
				$instance->$key = $value;
			} catch (CInvalidArgumentException$e) {
				// Caso o campo n�o tenha um atributo correspondente, procura no
				// mapa de atributos pelo equivalente e atribue.
				if (FALSE !== ($index = array_search($key, $this->_attributeMap)))
				{
					$instance->$index = $value;
				}
			}
		}
		return $instance;
	}

	/**
	* Setter.
	* @param CLocale$instance
	* @return CoreExt_DataMapper Prov� interface flu�da
	*/
	public function setLocale(Locale $instance)
	{
		$this->_locale = $instance;
		return $this;
	}

	/**
	* Getter.
	* @return CorLocale*/
	public function getLocale()
	{
		if (is_null($this->_locale))
		{
			$this->setLocale(Locale::getInstance());
		}
		return $this->_locale;
	}
}
