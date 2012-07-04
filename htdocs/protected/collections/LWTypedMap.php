<?php

class LWTypedMap extends CTypedMap
{

	protected $_type;
	
	public function __construct($type)
	{
		parent::__construct($type);
		$this->_type = $type;
	}
	
	
	
	public function total($property) {

		$total = 0;
		foreach($this as $item) {
			$total+= $item->$property;
		}

		return $total;

	}

}