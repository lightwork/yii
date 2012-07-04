<?php 

/**
 * Group a bunch of collections into their own collection.
 * 
 * Useful for things such as collecting data and grouping by a specific property.
 * <code>
 * // Get all metrics for a user, grouping them by the day
 * $collection = $user->Stats->getFitnessMetricHistory('WEIGHT')->groupByDay();
 * 
 * // Your collection will now look like this, with a child collection for each day:
 * // GroupedCollection
 * //     - BaseCollection
 * //         - Item1
 * //         - Item2
 * //         - Item3
 * //     - BaseCollection
 * //         - Item4
 * //         - Item5
 * 
 * // You could then pluck the newest from each like this:
 * $greatestCollection = $collection->pluckGreatestBy('EntryDate');
 * 
 * // You now have:
 * // - BaseCollection
 * //     - Item2
 * //     - Item5
 * 
 * // If you only want the values, then pluck the desired value:
 * 
 * $greatestValues = $collection->pluckGreatestBy('EntryDate')->pluck('Value');
 * 
 * // You now have:
 * // - Array
 * //     - value
 * //     - value
 * </code>
 *
 */
Class GroupedCollections extends BaseTypedCollection {
	
	/**
	 * Flatten a group of collections into a single collection.
	 * @param string $class The new collection class. Defaults to BaseCollection
	 * @return CList A collection inheriting from CList
	 */
	public function flatten($class = 'BaseCollection') {
		$flatList = new $class;
		foreach($this as $collection) {
			foreach($collection as $item) {
				$flatList->add($item);
			}
		}
		return $flatList;
	}
	
	/**
	 * Gather the greates value of property for each item in the collection into a new array.
	 * 
	 * If you do not pass in a default value then missing values will not be returned.
	 * 
	 * @param string $property
	 * @param mixed $default Override the default for missing values. Setting this value will
	 *        cause items with a missing value to still be rendered into the array.
	 * @return array
	 */
	public function pluckGreatestBy($property, $class = 'BaseCollection') {
		$col = new $class;
		foreach($this as $collection) {
			$greatestItem = null;
			foreach($collection as $item) {
				$greatestItem = $this->getGreater($greatestItem, $item, $property);
			}
			$value = @$greatestItem->$property;
			if(isset($value)) { $col->add($greatestItem); }
		}
		return $col;
	}
	
	/**
	 * If a value can be translated to a time, it'll become an int. Otherwise
	 * false will result.
	 * @param mixed $value
	 * @return int An int on success, FALSE otherwise.
	 */
	private function isTime($value) {
		$int = strtotime($value);
		if($int !== FALSE) { return $int; }
		return false;
	}
	
	/**
	 * Find the item with the greater value for a numeric property.
	 * @param mixed $a Any object, or null for a non-existent object.
	 * @param mixed $b Any object, or null for a non-existent object.
	 * @param string $property The property name.
	 * @return mixed The object if found, null otherwise.
	 */
	private function getGreater($a, $b, $property) {
		
		// If only 1 input is defined, return it.
		if(!isset($a) && isset($b)) { return $b; }
		if(!isset($b) && isset($a)) { return $a; }
		
		// Try to gather the property from each. Because we use magic getters
		// so ofter here, we want to suppress errors.
		$aValue = @$a->$property;
		$bValue = @$b->$property;
		
		// If neither of them have the property, return.
		if(!isset($aValue) && !isset($bValue)) { return null; }
		
		// If the value is not numeric, try to turn it into a numeric value.
		if(!is_numeric($aValue)) { $aValue = $this->isTime($aValue); }
		if(!is_numeric($bValue)) { $bValue = $this->isTime($bValue); }
		
		// Return the item with the greater value.
		if($aValue > $bValue) { return $a; }
		return $b;
	}
		
}
	
?>