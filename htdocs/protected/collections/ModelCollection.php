<?php

class ModelCollection extends LWTypedMap {

	protected $parent = null;


	/**
	 *
	 * @var CActiveRelation
	 */
	protected $relationship;
	protected $memoize = array();

	public function add($index, $item) {
		parent::add($index, $item);
		$this->memoize = array();
	}

	public function setParent(LWActiveRecord $parent, $relationship) {
		$this->parent = $parent;
		$this->relationship = $parent->getActiveRelation($relationship);
	}

	public function getParent() {
		return $this->parent;
	}


	/**
	 * Returns an array that's safe to be exported by the API
	 * @return array;
	 */
	public function toAPIArray()
	{
		$out = array();

		foreach($this as $i => $v)
		{
			if (method_exists($v, 'toApiArray'))
			{
				$out[$i] = $v->toApiArray();
			} else {
				$out[$i] = $v->attributes;
			}
		}

		return $out;
	}



	public function toJsonList($attributes = array())
	{
		$out = array();
		if (empty($attributes))
		{
			foreach($this as $i => $v) { /* @var $v LWActiveRecord */
				$out[$i] = $v->getAttributes();
			}
		} else {
			foreach($this as $i => $v) { /* @var $v LWActiveRecord */
				foreach($attributes as $j => $k) {
					if(is_array($k)) {
						$value = $v->getRelated($j, false, array(), 'ModelCollection')->toJsonList($k);
						$out[$i][$j] = $value;
					}
					else {
						$value = $v->$k;
						if(is_numeric($value)) { $value = (float)$value; }
						$out[$i][$k] = $value;
					}
				}
			}
		}

		return $out;


	}



	protected function getLastElement() {

		$keys = $this->getKeys();
		if (!empty($keys)) {
			return $this[$keys[count($keys)-1]];
		} else {
			return null;
		}

	}




	/**
	 *
	 * Adds up all the values of the specified attribute of all the elements, then stores them in the memoize array
	 * @param string $memoKey
	 * @param string $property
	 * @return integer
	 */
	protected function totalAndMemoize($property)
	{

		$memoKey = '--total'.$property;
		if (!isset($this->memoize[$memoKey])) {
			$this->memoize[$memoKey] = $this->total($property);
		}
		return $this->memoize[$memoKey];
	}


	protected function getFirstElement()
	{
		$keys = $this->getKeys();
		if (!empty($keys)) {
			return $this[$keys[0]];
		} else {
			return null;
		}
	}


	protected function toUTCTimeStamp($value)
	{

		$date = new DateTime($value);
		$date->setTimezone(new DateTimeZone('UTC'));
		return $date->getTimestamp();

	}


	public function toKeyValueArray($keyAttribute = 'Id', $valueAttribute = 'Name')
	{
		$memoKey = 'keyValue';
		if (!isset($this->memoize[$memoKey])) {
			$this->memoize[$memoKey] = array();
			foreach($this as $model)
			{
				$this->memoize[$memoKey][$model->$keyAttribute] = $model->$valueAttribute;
			}
		}
		return $this->memoize[$memoKey];
	}



	/**
	 * Collects all the attributes of this collection
	 *
	 * @param string $attribute
	 * @param string $index If the target value is an object, this can be used to index the list using that attribute of all the objects
	 * @return array
	 */
	public function collectAttributes($attribute, $index = null)
	{
		$memoKey = 'collected-' . $attribute . $index;
		if (!isset($this->memoize[$memoKey])) {
			$this->memoize[$memoKey] = array();
			foreach($this as $model)
			{
				if ($index) {
					$this->memoize[$memoKey][$model->$attribute->$index] = $model->$attribute;
				} else {
					$this->memoize[$memoKey][] = $model->$attribute;
				}
			}
		}
		return $this->memoize[$memoKey];
	}




	public function filterByAttribute($attribute, $value, $indexAttribute = null)
	{

		return $this->filterCollection(function ($item) use ($value, $attribute) {
			return $item->$attribute == $value;
		}, $indexAttribute);


	}




	/**
	 * helper function for creating subsets
	 *
	 * @param CollectionFilter $filter
	 * @param string $indexAttribute
	 */
	protected function createFormFilter(CollectionFilter $filter, $indexAttribute = null)
	{
		$collection = new static($this->_type);
		foreach($filter as $i => $item) {
			$index = isset($indexAttribute) ? $item->$indexAttribute : $i;
			$collection->add($index, $item);
		};

		return $collection;
	}



	protected function filterCollection($callback, $indexAttribute = null)
	{
		$filter = new CollectionFilter($this->getIterator(), $callback);
		return $this->createFormFilter($filter, $indexAttribute);
	}



}




class CollectionFilter extends FilterIterator
{
	/* @var array */
	private $filter;

	public function __construct(Iterator $iterator, $filter )
	{
		parent::__construct($iterator);
		$this->filter = $filter;
	}

	public function accept()
	{
		$item = $this->getInnerIterator()->current();
		return call_user_func($this->filter, $item);
	}
}
