<?php 

/**
 * Wrapper for working with arrays using dot notation.
 *
 * <code>
 *     $a = new DotArray();
 *     $a->set('xAxis.categories', array(...));
 *     //or
 *     $a->set('title.text', 'The Title');
 * </code>
 * 
 * Get a value using the simple getter:
 * 
 * <code>
 *     $a->get('xAxis.categories');
 * </code>
 */
class DotArray extends CComponent {
	
	private $array = array();
	
	public function __construct($array = array()) {
		$this->array = $array;
	}
	
	public function toArray() {
		return $this->array;
	}
	
	/**
	 * Set a value using dot notation. Example, to make the tooltip shared, do this:
	 * <code>
	 *     $hc = new DotArray();
	 *     $hc->set('tooltip.shared', true);
	 * </code>
	 * 
	 * @param string $path
	 * @param mixed $value
	 * @return DotArray The instance is returned to enable chaining.
	 */
	public function set($path, $value) {
		$this->setValueForPath($value, $path);
		return $this;
	}
	
	public function setMany($array) {
		foreach($array as $path => $value) {
			$this->set($path, $value);
		}	
	}
	
	/**
	 * Get an item located at the specified path.
	 * @param string $path The path of data to get.
	 * @return DotArray The instance is returned to enable chaining.
	 */
	public function get($path) {
		return $this->getItemAtPathByReference($path, false);
		return $this;
	}
	
	/**
	 * Add a value using dot notation. Example, to make the tooltip shared, do this:
	 * The value will be appended to the array specified by the path string.
	 * @param string $path
	 * @param mixed $value
	 * @return DotArray The instance is returned to enable chaining.
	 */
	public function add($path, $value) {
		$this->addValueForPath($value, $path);
		return $this;
	}
	
	public function addMany($array) {
		foreach($array as $path => $value) {
			$this->add($path, $value);
		}	
	}
	
	/**
	 * Set a value using dot notation.
	 * 
	 * @param mixed $value
	 * @param string $path
	 */
	private function setValueForPath($value, $path) {
		$item = &$this->getItemAtPathByReference($path);
		$item = $value;
	}
	
	/**
	 * Add a value using dot notation.
	 * 
	 * @param mixed $value
	 * @param string $path
	 */
	private function addValueForPath($value, $path) {
		$item = &$this->getItemAtPathByReference($path);
		if(is_array($item)) {
			$item[] = $value;
		}
	}
	
	/**
	 * Get the item in the array given a path of array keys.
	 * This allows you to use dot notation to access deeply nested array elements.
	 * So, a string such as `title.text` will be translated to `$this->graphOptions['title']['text']`
	 * 
	 * At each point in the path, if the item does not exist, it is made as an empty array.
	 * 
	 * @param string $path A path of array keys, separated by dots.
	 * @return array|mixed The array, or value, located at the end of the path.
	 */
	private function &getItemAtPathByReference($path, $autocreate = true) {
		$parts = explode('.', $path);
		
		$item = &$this->array;
		
		foreach($parts as $part) {
			if(!isset($item[$part]) && !$autocreate) { return null; }
			if(!isset($item[$part]) && $autocreate) {
				$item[$part] = array();
			}
			$item = &$item[$part];
		}
		
		return $item;
	}
}
