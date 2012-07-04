<?php 

class BaseCollection extends CList {

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

	public function index($key)
	{
		$a = array();
		foreach($this as $row) {
			echo '<pre> ' . CVarDumper::dumpAsString($row) . '</pre>';
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
	
}
	
?>
