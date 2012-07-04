<?php

class ReportCollection extends BaseCollectionMap
{

	public function forHighcharts($attr = null)
	{
		if(!isset($attr)) { return $this->toArray(); }

		$array = array_map(function($data) use($attr) {
			$row = array();
			foreach($attr as $key=>$newKey) {
				// Get the right value
				if(is_numeric($key)) { $datum = @$data[$newKey]; }
				else { $datum = @$data[$key]; }
				// Try to auto-format as necessary
				if(is_numeric($datum)) { $datum = (float) $datum; }
				$row[$newKey] = $datum;
			}
			return $row;
		}, $this->toArray());

		return array_values($array);
	}

}
