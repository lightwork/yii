<?php 

/**
 * Encapsulates a range of time and provides for convenient methods for working with it.
 * 
 * <code>
 *	   // If you want to inclcude the full end day, be sure to set the full time like this:
 *	   $dateRange = new DateRange('2012-01-03', '2012-01-04 23:59:59');
 *	   // This is equivelant to:
 *	   $dateRange = new DateRange('2012-01-03 00:00:00', '2012-01-04 23:59:59');
 *	   
 *	   // You can also specify dates using pretty-strings
 *	   $dateRange = new DateRange('yesterday', 'tomorrow');
 *	   // Which is the same as
 *	   $dateRange = new DateRange('2012-01-03 00:00:00', '2012-01-05 00:00:00');
 *	   
 *	   // Or try relative times:
 *	   $dateRange = new DateRange('-10days', '+0days');
 *	   // Which is equivelant to this (notice it uses the current time)
 *	   $dateRange = new DateRange('2011-12-25 15:33:06', '2012-01-04 15:33:06');
 *	   
 *	   // If you want to force the range to include the full-days then use
 *	   $dateRange->forceFullDays();
 * </code>
 */
class DateRange extends CComponent implements Iterator {
	
	const MYSQL_DATE = 'Y-m-d';
	const MYSQL_DATETIME = 'Y-m-d H:i:s';
	const PRETTY_DATE = 'F j, Y';
	const PRETTY_DATETIME = 'F j, Y, g:i a';
	
	private $_begin;
	private $_end;
	private $_format = self::MYSQL_DATE;

	private $_iterationDuration = 86400; // one day

	/**
	 * Create an instance of DateRange.
	 * @param int|string $begin Any value that can be interpreted by `strtotime`.
	 * @param int|string $end Any value that can be interpreted by `strtotime`.
	 */
	public function __construct($begin = null, $end = null) {
		if(isset($begin))
			$this->setBegin($begin);
		if(isset($end)) 
			$this->setEnd($end);
	}
	
	public static function parseTime($value) {
		return (is_int($value)) ? $value : strtotime($value);
	}
	
	public static function formatTime($value, $format = self::MYSQL_DATETIME) {
		$value = self::parseTime($value);
		return date($format, $value);
	}
	
	/**
	 * Set the duration of each iteration when iterating over the date range.
	 * e.g. set it to 3600 seconds to iterate over the date range per hour.
	 * @param number $number
	 */
	public function setIterationDuration($number) {
		$this->_iterationDuration = $number;
		$this->resetArray();
		return $this;
	}
	
	/**
	 * Convenience function for setting the iteration duration to 24 hours
	 */
	public function iterateByDay() {
		$this->IterationDuration = 60 * 60 * 24;
		return $this;
	}
	
	/**
	 * Convenience function for setting the iteration duration to 1 hour
	 */
	public function iterateByHour() {
		$this->IterationDuration = 60 * 60;
		return $this;
	}
	
	public function resetArray() {
		$this->_array = null;
		return $this;
	}
	
	protected $_array;
	
	/**
	 * Get the date range as an array.
	 * You do not need to call this if you wish to iterate over the date range, this class
	 * is iterable.
	 * @param boolean $forceFull Should we force the array to include all time, even the last portion
	 *	   which may not cover the full iteration duration?
	 * @return array
	 */
	public function toArray($forceFull = true) {
		if(!isset($this->_array)) {
			$time = $this->Begin;
			while($time <= $this->End) {
				$this->_array[] = $time;
				$time += $this->_iterationDuration;
			}
			
			// Should we push the very last time onto the stack, even if
			// it's not 100% of the iteration duration?
			if($forceFull && ($time < ($this->End + $this->_iterationDuration))) {
				$this->_array[] = $this->End;
			}
		}
		return $this->_array;
	}
	
	/**
	 * Force the date range to encompass the full day for begin and end date.
	 * @return DateRange
	 */
	public function forceFullDays() {
		return $this->forceFullDayBegin()->forceFullDayEnd();
	}
	
	/**
	 * Force the begin date to encompass the full day.
	 * example: turns '2011-12-25 15:33:06' into '2011-12-25 00:00:00'
	 * @return DateRange
	 */
	public function forceFullDayBegin() {
		$this->_begin = strtotime(date(self::MYSQL_DATE . ' 00:00:00', $this->_begin));
		$this->resetArray();
		return $this;
	}
	
	/**
	 * Force the end date to encompass the full day.
	 * example: turns '2011-12-25 15:33:06' into '2011-12-25 23:59:59'
	 * @return DateRange
	 */
	public function forceFullDayEnd() {
		$this->_end = strtotime(date(self::MYSQL_DATE . ' 23:59:59', $this->_end));
		$this->resetArray();
		return $this;
	}
	
	public function getNumberOfDays() {
		$diff = $this->End - $this->Begin;
		return round($diff / 86400);
	}
	
	/**
	 * Set the format string to be used by the `date` method.
	 * @param string $string
	 */
	public function setFormat($string) { 
		$this->_format = $format;
		return $this;
	}
	
	/**
	 * Get the begin date/time.
	 * Returns unix timestamp by default.
	 * If you pass it true, the default forma is used.
	 * If you pass in a string, then it will be used to format the date.
	 * @param mixed $format True for default format, String for custom format, null/false for unix time.
	 * @return mixed The time or formatted date/time.
	 */
	public function getBegin($format = false) {
		return $this->getDateValue($this->_begin, $format);
	}
	
	/**
	 * Set the begin time
	 * @param int|string $value Any value that can be interpreted by `strtotime`.
	 */
	public function setBegin($value) {
		$this->_begin = (is_int($value)) ? $value : strtotime($value);
		$this->resetArray();
		return $this;
	}
	
	/**
	 * Get the end date/time.
	 * Returns unix timestamp by default.
	 * If you pass it true, the default forma is used.
	 * If you pass in a string, then it will be used to format the date.
	 * @param mixed $format True for default format, String for custom format, null/false for unix time.
	 * @return mixed The time or formatted date/time.
	 */
	public function getEnd($format = false) {
		return $this->getDateValue($this->_end, $format);
	}
	
	
	public function getRandom() {
		
		return rand($this->_begin, $this->_end);
		
	}
	
	
	/**
	 * Set the end time
	 * @param int|string $value Any value that can be interpreted by `strtotime`.
	 */
	public function setEnd($value) {
		$this->_end = (is_int($value)) ? $value : strtotime($value);
		$this->resetArray();
		return $this;
	}
	
	protected function getDateValue($date, $format = null) {
		if($format === true) {
			return date($this->format, $date);
		}
		else if(is_string($format)) {
			return date($format, $date);
		}
		return $date;
	}
	
	public function contains($date) {
		$time = self::parseTime($date);
		return ($this->Begin <= $time) && ($time <= $this->End);
	}
	
	public function __toString() {
		return $this->getBegin(self::MYSQL_DATETIME) . ' - ' . $this->getEnd(self::MYSQL_DATETIME);
	}

	public function toString()
	{
		return $this->__toString();
	}
	
	/**** Iterable Support ****/
	
	protected $position = 0;
	
	public function rewind()
	{
		$this->position = 0;
	}

	public function current()
	{
		$array = $this->toArray();
		return $array[$this->position];
	}

	public function key()
	{
		return $this->position;
	}

	public function next()
	{
		++$this->position;
	}

	public function valid()
	{
		$array = $this->toArray();
		return isset($array[$this->position]);
	}

}
