<?php

/**
 * @property boolean $isSaving Flags whether or not the record is currently in the "save" mode
 * @property boolean $isNewRecord Flags whether or not the record is transient
 */
class LWActiveRecord extends CActiveRecord
{


	static $useSetter;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuctionItem the static model class
	 */
	public static function model($className=__CLASS__)
	{
		$model = parent::model($className);

		// Prefix all selects with the table alias.
		// We need this for complex queries, especially in reporting
		// where we add alot of custom selects and we don't want conflicting
		// column names.
		$t = $model->getTableAlias(false, false);
		$model->getDbCriteria()->select = array($t.'.*');
		return $model;
	}

	public static function getNowDateTime() {
		return date('Y-m-d H:i:s', time());
	}

	/**
	 * Given a timezone, such as 'US/Pacific' or '-7:00' this will adjust
	 * all date values from current timezone to the desired timezone.
	 *
	 * @param string $tz The destination timezone.
	 * @param string $from The current timezone in which the data is saved.
	 * @return LWActiveRecord
	 */
	public function convertDateTZ($tz, $from = 'UTC')
	{
		$dateNames = $this->getDateAttributeNames();
		$nonDateNames = array_diff($this->attributeNames(), $dateNames);

		$t = $this->getTableAlias(false, false);

		// Convert the select to an array so we can add/remove to it.
		$select = $this->getDbCriteria()->select;
		if(!is_array($select)) { $select = explode(',', $select); }

		$newSelect = array();

		foreach($select as $key=>$selectItem) {
			// If we have a select *, replace with a select for all non-date attributes
			// We want to avoid having a "t.date, ..., CONVENT_TZ(...) AS date"
			// which results in duplicate column names in result.
			if($selectItem == $t.'.*') {
				foreach($nonDateNames as $name) {
					$newSelect[] = "$t.$name";
				}
			}
			// If this is a date-column, ignore it as we'll add a converted one later
			else if(!in_array($selectItem, $dateNames)) {
				$newSelect[] = $selectItem;
			}
		}

		if(isset($dateNames) && count($dateNames) > 0) {
			foreach($dateNames as $name) {
				$newSelect[] = "CONVERT_TZ($t.$name, '$from', '$tz') AS $name";
			}
		}

		// We cannot select the same column twice. Remove duplicates.
		$newSelect = array_unique($newSelect);

		$this->getDbCriteria()->select = $newSelect;
		// $this->getDbCriteria()->mergeWith(array('select'=>$newSelect));

		return $this;
	}

	/**
	 * Get the date field sql. Automatically adds in the timezone convert
	 * if the app has a timezone set.
	 *
	 * @param string $columnName The name of the date column
	 * @return string
	 */
	public function getDateFieldSql($columnName)
	{
		$t = $this->getTableAlias(false, false);
		$sql = "$t.$columnName";
		if($timezone = Yii::app()->getParam('timezone')) {
		   $sql = "CONVERT_TZ($sql, 'UTC', '$timezone')";
		}
		return $sql;
	}

	public function getDateAttributeNames()
	{
		return array(
			'entrydate', 'timestamp',
		);
	}

	/**
	 * Is this relation defined on this model?
	 * @param string $relationName
	 * @return boolean
	 */
	public function hasRelation($relationName) {
		$md=$this->getMetaData();
		return isset($md->relations[$relationName]);
	}

	/**
	 * We want to override the before validate method so we can set the
	 * EntryDate and TimeStamp fields when appropriate.
	 *
	 * @override
	 */
	public function onBeforeValidate(CModelEvent $event) {

		if($this->isNewRecord && $this->hasAttribute('entrydate') && $this->getAttribute('entrydate') === null) {
			$this->entrydate = self::getNowDateTime();
		}
		if($this->hasAttribute('timestamp')) {
			$this->timestamp = self::getNowDateTime();
		}

		return parent::onBeforeValidate($event);
	}

	private $_reflection;

	/**
	 * Returns a reflection object for this model
	 * @return ReflectionClass
	 */
	public function getReflection() {
		if (empty($this->_reflection)) {
			$this->_reflection = new ReflectionClass($this);
		}

		return $this->_reflection;
	}


	public static function toDataArray($collection, $key, $value) {
		$array = array();
		foreach($collection as $item) {
			$array[$item->$key] = $item->$value;
		}
		return $array;
	}


	public function toKeyValue($attributes = null)
	{
		if(!isset($attributes)) { $attributes = $this->attributeNames(); }
		$out = array();
		foreach($attributes as $attr) {
			$out[$attr] = $this->$attr;
		}

		return $out;

	}



	public function __set($name, $value)
	{
		$class = get_class($this);

		if (is_array($class::$useSetter) && in_array($name, $class::$useSetter))
		{
			$methodName = 'set'.ucfirst($name);
			return $this->$methodName($value);
		} else {
			return parent::__set($name, $value);
		}

	}


	public function attributeAsCurrency($attribute)
	{
		return self::$cnf->formatCurrency($this->$attribute, 'USD');
	}



	public function findByAttribute($attribute, $value) {
		return $this->findByAttributes(array($attribute => $value));
	}



	public function __get($name)
	{

		$methodName = 'get'.ucfirst($name);
		// dot notated names
		if (strpos($name, '.') !== false) {

			$name = explode('.', $name);
			$attr = array_shift($name);
			if (empty($name)) {
				return $this->$attr;
			} else {
				$name = implode('.', $name);
				return $this->$attr->$name;
			}
		// get accessors
		} elseif ($this->getReflection()->hasMethod($methodName)
				&& $this->getReflection()->getMethod($methodName)->isPublic()
				&& $this->getReflection()->getMethod($methodName)->getNumberOfRequiredParameters() <= 0) {
			return $this->$methodName();
		} elseif (preg_match('/(.+)AsTitleCase/', $name, $matches))
		{
			return ucwords($this->{$matches[1]});
		} elseif (preg_match('/(.+)DateFormat/', $name, $matches ))
		{
			return $this->DateFieldFormat($matches[1]);
		} elseif (preg_match ('/(.+)AsCurrency/', $name, $matches))
		{
			return $this->attributeAsCurrency($matches[1]);
		} elseif (preg_match ('/(.+)AsUTCTimeStamp/', $name, $matches))
		{
			$date = new DateTime($this->{$matches[1]});
			$date->setTimezone(new DateTimeZone('UTC'));
			return $date->getTimestamp();
		} elseif (preg_match ('/(.+)AsTimeStamp/', $name, $matches))
		{
			return strtotime($this->{$matches[1]});
		}elseif ($this->hasRelated($name))
		{
			return $this->getRelated($name);
		} else {
			return parent::__get($name);
		}
	}



	public function DateFieldFormat($field, $format = 'F j, Y')
	{
		try {
			return date($format, strtotime($this->$field));
		} catch (Exception $e) {}
		try {
			$field = $field.'Date';
			return date($format, strtotime($this->$field));
		} catch (Exception $e) {}

		throw $e;

	}


	/**
	 * Fetches the current query as a data provider
	 * @param Criteria $ CDbCriteria
	 * @return CActiveDataProvider
	 */
	public function getDataProvider($criteria=null, $pagination=null) {
		if ((is_array ($criteria)) || ($criteria instanceof CDbCriteria) )
			$this->getDbCriteria()->mergeWith($criteria);
		$pagination = CMap:: mergeArray (array ('pageSize' => 10), (array) $pagination);
		return new CActiveDataProvider(get_class($this), array(
				'criteria'=>$this->getDbCriteria(),
				'pagination' => $pagination
		));
	}



	public function findAll($condition='',$params=array())
	{
		$found = parent::findAll($condition,$params);
		if ($this->forceCollection === true && empty($this->_collectionClass)) {
			$this->asCollection();
		}

		if (!empty($this->_collectionClass)) {
			$found = $this->createCollection($this->_collectionClass, get_class($this), $found);
			$this->_collectionClass = false;
		}
		return $found;
	}


	private $relatedCollection = array();
	public function getRelated($name, $refresh=false, $params=array(), $asCollection = false) {

		$related = parent::getRelated($name, $refresh, $params);
		if (!is_array($related)) { return $related; }

		# we need to figure out the relationship's class, so we know whether to turn it into a collection
		$relation = $this->getActiveRelation($name);
		$class = $relation->className;
		$relationModel = $class::model();

		# if $forceCollection or $asCollection is set, then we'll turn it into that collection
		if ($relationModel->forceCollection || $asCollection) {

			# this means the user only wishes to use the default collection
			if (empty($asCollection)) { $asCollection = $relationModel->getDefaultCollectionClass(); }

			# before create the actual collection, we need to make sure that it's not cached
			if (!isset($this->relatedCollection[$name][$asCollection]) || $refresh === true || $params !== array()) {
				Yii::trace("Memoizing $name $asCollection");
				$related = $this->createCollection($asCollection, $class, $related, $name);

				$this->relatedCollection[$name][$asCollection] = $related;
			} else {
				return $this->relatedCollection[$name][$asCollection];
			}
		}
		return $related;
	}


	private function & createCollection($class, $type, $data, $relationship = false) {

		/* @var $collection LWRelatedTypedMap; */
		$collection = new $class($type);
		if (method_exists($collection, 'setParent') && $relationship !== false) {
			$collection->setParent($this, $relationship);
		}
		foreach($data as $i=>$j) {
			$collection->add($i, $j);
		}

		return $collection;

	}




	private $_collectionClass;
	protected $forceCollection = false;
	public function asCollection($class = true) {
		if ($class === true) {
			 $class = $this->getDefaultCollectionClass();
		}

		$this->_collectionClass = $class;
		return $this;
	}



	/**
	 * Default collection class instantiated when findAll is called
	 */
	public function getDefaultCollectionClass() {
		return 'ModelCollection';
	}

	/**
	 *
	 *
	 * @param $attribute
	 * @return array:multitype
	 */
	public function getAllowedValues($attribute) {

		$values = array();

		if (preg_match('/\((.*)\)/',@$this->tableSchema->columns[$attribute]->dbType,$matches)) {
			foreach(explode(',', $matches[1]) as $value)
			{
				$value=str_replace(array("'", '"'),array(null,null),$value);
				$values[$value]=Yii::t('enumItem',$value);
			}
		}

		return $values;

	}


	private $relatedIndexedMemoized = array();

	/**
	 * returns all the related record for a given relationship, indexed by the specified attributes
	 * @param string $name Name of the relationship
	 * @param string $property Name of the attribute to use index with
	 * @param boolean $reload
	 * @param array $params
	 */
	public function getRelatedIndexed($name, $property = 'Id', $reload = false, $params = array()) {

		if (empty($reload) && empty($params) && !empty($this->relatedIndexedMemoized[$name])) {
			return $this->relatedIndexedMemoized[$name];
		}

		$indexed = array();
		foreach($this->getRelated($name, $reload, $params) as $related) {
			$indexed[$related->$property] = $related;
		}

		return $indexed;

	}


	public function getKeyValue($attribute, $keyAttribute, $valueAttribute) {

		$keyvalue = array();
		foreach($this->$attribute as $elem) {
			$keyvalue[$elem->$keyAttribute] = $elem->$valueAttribute;
		}

		return $keyvalue;

	}

	/**
	 * A mixture of `findAll` and `query`, where the results are not loaded into a class,
	 * but instead we get the raw values selected by from mysql as a key->value array.
	 *
	 * @example
	 * <code>
	 *	   // Create criteria
	 *	   $critera = new CDbCriteria();
	 *
	 *	   // add stuff to criteria...
	 *
	 *	   // Merge the criteria.
	 *	   $model->getDbCriteria()->mergeWith($criteria);
	 *
	 *	   // Query for the results.
	 *	   $results = $model->queryAll();
	 * </code>
	 *
	 * @return array An array of arrays loaded from MySQL.
	 */
	public function queryAll($condition='',$params=array()) {
		$criteria=$this->getCommandBuilder()->createCriteria($condition, $params);
		$this->applyScopes($criteria);
		$command=$this->getCommandBuilder()->createFindCommand($this->getTableSchema(),$criteria);
		$results = $command->queryAll();
		return $results;
	}

	public function getCommand($condition='',$params=array())
	{
		$criteria=$this->getCommandBuilder()->createCriteria($condition,$params);
		$this->applyScopes($criteria);
		if(empty($criteria->with))
		{
			$command=$this->getCommandBuilder()->createFindCommand($this->getTableSchema(),$criteria);
			return $command;
		}
	}


	/**
	 *
	 *
	 * @var CNumberFormatter
	 */
	static protected $cnf;
	public static function initVars() {
		self::$cnf = new CNumberFormatter('en_US');
	}


	private $memoized = array();

	/**
	 *
	 * Memoizer
	 *
	 * @param string $property
	 * @param callback $callback
	 * @param boolean $force
	 * @return multitype:
	 */
	public function getMemoized($property, $callback, $force = false)
	{

		if (!array_key_exists($property, $this->memoized) || $force == true) {
			$this->memoized[$property] = call_user_func($callback, $this);
		}

		return $this->memoized[$property];

	}


	private $_isSaving = false;
	protected function beforeSave() {
		$this->_isSaving = true;
		return parent::beforeSave();
	}


	protected function afterSave() {
		$this->_isSaving = false;
		return parent::afterSave();
	}


	public function getIsSaving() {
		return $this->_isSaving;
	}

	/**
	 * Useful for the CDetailView, to get the pretty attribute labels
	 * for each attribute.
	 *
	 * The attribute must be specified in the format of "Name:Type:Label",
	 * where "Type" and "Label" are optional.
	 *
	 * @return array An array of arrays.
	 */
	public function getDisplayAttributeLabels()
	{
		$a = array();
		foreach($this->attributeLabels() as $attr=>$label) {
			$a[] = array('name'=>$attr, 'label'=>$label);
		}
		return $a;
	}

	/**
	 * Useful for the CDetailView, to get the pretty values of all attributes.
	 *
	 * @return array A key=>value array.
	 */
	public function getDisplayAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Parse an ENUM column type into an array. Optionally key the array
	 * to the enum values.
	 *
	 * Example: column type ENUM('ON','OFF','DUMP') will become
	 *
	 * <code>
	 *	 array(
	 *	   'ON'=>'ON',
	 *	   'OFF'=>'OFF',
	 *	   'DUMP'=>'DUMP',
	 *	 );
	 * </code>
	 *
	 * @param string $columnName The name of a column on this table
	 * @param boolean $keyToName Should we set the array keys to enum value?
	 * @return array
	 */
	public function parseEnumNames($columnName, $keyToName = true) {

		$array = array();

		$columnType = $this->tableSchema->getColumn($columnName)->dbType;

		if(strpos(strtolower($columnType), 'enum(') !== FALSE) {
			$beginStr=strpos($columnType,"(")+1;
			$endStr=strpos($columnType,")");
			$temp=substr($columnType,$beginStr,$endStr-$beginStr);
			$temp=str_replace("'","",$temp);
			$array = explode(',',$temp);

			if($keyToName) {
				$array = array_combine ( $array, $array );
			}

		}

		return $array;

	}




}

LWActiveRecord::initVars();
