<?php 

/**
 * Base class for all typed lists.
 * 
 * Fixes some oddities and missing features of the basic CTypedList.
 *
 */
class BaseTypedCollection extends BaseCollection {

	protected $_type;
	
	/**
	 * Constructor.
	 * 
	 * Why are we overriding the constructor? The base class does not allow us access to
	 * the type. So we make it a protected value so all child classes can know about it.
	 * 
	 * @param string $type class type
	 */
	public function __construct($type, $data = array())
	{
		$this->_type=$type;
		foreach($data as $i => $item) {
			$this->insertAt($i, $item);
		}
		parent::__construct();
	}
	
	public function getType() {
		return $this->_type;
	}

	/**
	 * Inserts an item at the specified position.
	 * This method overrides the parent implementation by
	 * checking the item to be inserted is of certain type.
	 * @see CTypedList::insertAt
	 * @param integer $index the specified position.
	 * @param mixed $item new item
	 * @throws CException If the index specified exceeds the bound,
	 * the list is read-only or the element is not of the expected type.
	 */
	public function insertAt($index,$item)
	{
		if($item instanceof $this->_type)
			parent::insertAt($index,$item);
		else
			throw new CException(Yii::t('yii','CTypedList<{type}> can only hold objects of {type} class.',
				array('{type}'=>$this->_type)));
	}
	
}
	
?>