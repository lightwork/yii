<?php 

class BaseCollectionMap extends CMap {

	/**
	 * Get a property from the first item of the collection having that property defined.
	 * @param string $attribute
	 * @return mixed
	 */
	public function getProperty($attribute) {
		foreach($this as $item) {
			$value = $item->$attribute;
			if(isset($value)) {
				return $value;
			}
		}
	}
	
	public function reverse() {
		$this->copyFrom(array_reverse($this->toArray()));
		return $this;
	}

	public function mergeBoth($thatData, $order = 'desc')
	{
		if(!is_array($thatData)) {
			if($thatData instanceof Traversable) {
				$thatData = $thatData->toArray();
			}
		}

		$thisData = $this->toArray();

		$sort = 'asort';
		if($order == 'desc') {
			$sort = 'arsort';
		}

		$keys = array_merge(array_keys($thisData), array_keys($thatData));	
		$sort($keys);

		// echo '<pre> ' . CVarDumper::dumpAsString($keys) . '</pre>';exit;

		$collection = new self;

		foreach($keys as $key) {
			$row = array();
			if(isset($thisData[$key])) {
				$row = $this->mergeArray($row, $thisData[$key]);
			}
			if(isset($thatData[$key])) {
				$row = $this->mergeArray($row, $thatData[$key]);
			}
			$collection->add($key, $row);
		}

		return $collection;

	}

	public function index($key)
	{
		$a = array();
		foreach($this as $row) {
			// echo '<pre> ' . CVarDumper::dumpAsString($row) . '</pre>';
			$rowKey = $row[$key];
			$a[$rowKey] = $row;
		}
		$this->copyFrom($a);
		return $this;
	}
	
	/**
	 * Gather the property value for each item in the collection into a new array.
	 * 
	 * If you do not pass in a default value then missing values will not be returned.
	 * 
	 * @param string $property
	 * @param mixed $default Override the default for missing values. Setting this value will
	 *		  cause items with a missing value to still be rendered into the array.
	 * @return array
	 */
	public function pluck($property, $default = null) {
		$values = array();
		foreach($this as $item) {
			$value = $item->$property;
			if(isset($value) || isset($default)) {
				if(is_numeric($value)) { $value = (float)$value; }
				$values[] = $value;
			}
		}
		return $values;
	}
	
	
	
	public function total($property) {
		
		$total = 0;
		foreach($this as $item) {
			$total+= $item->$property;
		}
		
		return $total;
		
	}

	public function toSimpleArray()
	{
		return array_values($this->toArray());
	}
	
}
	
?>
