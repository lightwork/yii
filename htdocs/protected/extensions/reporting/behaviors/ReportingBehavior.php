<?php

class ReportingBehavior extends CActiveRecordBehavior {

	public $dateField = 'entrydate';
	public $collectionClass = 'ReportCollection';

	public $reportSelect = '*';
	public $countSelect = 'COUNT(*) AS COUNT';

	public $queryAlias = 'R';
	public $yearAlias = 'YEAR';
	public $monthAlias = 'MONTH';
	public $dayAlias = 'DAY';
	public $hourAlias = 'HR';
	public $minuteAlias = 'MIN';
	public $secondAlias = 'SEC';

	// Moved to the lw active record base class
	// public function getCommand($condition='',$params=array())
	// {
	//	$criteria=$this->getOwner()->getCommandBuilder()->createCriteria($condition,$params);
	//	$this->getOwner()->applyScopes($criteria);
	//	if(empty($criteria->with))
	//	{
	//		$command=$this->getOwner()->getCommandBuilder()->createFindCommand($this->getOwner()->getTableSchema(),$criteria);
	//		return $command;
	//	}
	// }

	public function setReportSelect($select)
	{
		$this->reportSelect = $select;
		return $this->getOwner();
	}

	public function addSelect($string)
	{
		if(is_string($this->reportSelect)) {
			$this->reportSelect .= ', ' . $string;
		}
		else {
			$this->reportSelect[] = $string;
		}
		return $this->getOwner();
	}

	public function setCountSelect($string)
	{
		$this->countSelect = $string;
		return $this->getOwner();
	}

	public function getReportSelect()
	{
		$s = $this->reportSelect;
		if(is_string($s)) {
			$s = explode(',', $s);
		}
		return array_merge($s, array($this->countSelect));
	}

	/**
	 * Get the date field sql. Automatically adds in the timezone convert
	 * if the app has a timezone set.
	 * @return string 
	 */
	public function getDateFieldSql()
	{
		return $this->getOwner()->getDateFieldSql($this->dateField);
	}

	public function addTimeSelect()
	{
		$t = $this->getOwner()->getTableAlias(false, false);
		$criteria = $this->getOwner()->getDbCriteria();
		$dateField = $this->getDateFieldSql();
		$select = array(
			"DATE_FORMAT($dateField, '%y') {$this->yearAlias}",
			"DATE_FORMAT($dateField, '%m') {$this->monthAlias}",
			"DATE_FORMAT($dateField, '%d') {$this->dayAlias}",
			"DATE_FORMAT($dateField, '%k') {$this->hourAlias}",
			"DATE_FORMAT($dateField, '%i') {$this->minuteAlias}",
			"DATE_FORMAT($dateField, '%s') {$this->secondAlias}",
		);
		if(is_array($criteria->select)) {
			$criteria->select = array_merge($criteria->select, $select);
		}
		else {
			$criteria->select .= ',' . PHP_EOL . implode(',' . PHP_EOL, $select);
		}
		return $this->getOwner();
	}

	public function inRange(DateRange $range)
	{
		$t = $this->getOwner()->getTableAlias(false, false);
		$dateField = $this->getDateFieldSql();
		$condition = "$dateField BETWEEN CAST('{$range->getBegin(DateRange::MYSQL_DATETIME)}' AS DATETIME) AND CAST('{$range->getEnd(DateRange::MYSQL_DATETIME)}' AS DATETIME)";
		$this->getOwner()->getDbCriteria()->addCondition($condition);
		return $this->getOwner();
	}

	public function countAll($criteria = array())
	{
		return $this->getOwner()->count($criteria);
	}

	public function queryByDay()
	{
		// Get a reference to the params set on the owner criteria
		$params = $this->getOwner()->getDbCriteria()->params;

		$this->getOwner()->addTimeSelect();

		$command = $this->getOwner()->getCommand()->bindValues($params)->text;

		$select = $this->getReportSelect();
		if(is_array($select)) { $select = implode(', ' . PHP_EOL, $select); }
		$sql = 'SELECT ' . $select;
		$sql .= PHP_EOL . 'FROM (' . $command . ') AS R';
		$sql .= PHP_EOL . 'GROUP BY ' . $this->getReportingTimeGroup($this->dayAlias);
		$sql .= PHP_EOL . 'ORDER BY ' . $this->getReportingTimeOrder($this->dayAlias);

		if(Yii::app()->request->getParam('show_sql', false) == true) {
			echo '<pre> ' . $sql . '</pre>';
		}

		$command = $this->getOwner()->getCommandBuilder()->createSqlCommand($sql);
		$command->bindValues($params);

		return $this->_query($command);
	}

	public function queryByHour()
	{
		// Get a reference to the params set on the owner criteria
		$params = $this->getOwner()->getDbCriteria()->params;

		$this->getOwner()->addTimeSelect();

		$command = $this->getOwner()->getCommand()->text;

		$select = $this->getReportSelect();
		if(is_array($select)) { $select = implode(', ' . PHP_EOL, $select); }

		$sql = 'SELECT ' . $select;
		$sql .= PHP_EOL . 'FROM (' . $command . ') AS R';
		$sql .= PHP_EOL . 'GROUP BY ' . $this->getReportingTimeGroup($this->hourAlias);
		$sql .= PHP_EOL . 'ORDER BY ' . $this->getReportingTimeOrder($this->hourAlias);

		//echo '<pre> ' . $sql . '</pre>';

		$command = $this->getOwner()->getCommandBuilder()->createSqlCommand($sql);
		$command->bindValues($params);

		return $this->_query($command);
	}

	private function _query($command)
	{
		$results = $command->queryAll();
		if(isset($this->collectionClass)) {
			$class = $this->collectionClass;
			$col = new $class($results);
			return $col;
		}
		return $results;
	}

	public function getReportingTimeGroup($time)
	{
		$parts = array(
			$this->yearAlias,
			$this->monthAlias,
			$this->dayAlias,
			$this->hourAlias,
			$this->minuteAlias,
			$this->secondAlias,
		);
		$columns = array();
		foreach($parts as $part) {
			$columns[] = $part;
			if($part == $time) { break; }
		}
		return $this->queryAlias . '.' . implode(', ' . $this->queryAlias . '.', $columns);
	}

	public function getReportingTimeOrder($time) {
		return $this->getReportingTimeGroup($time);
	}
}
