<?php

Yii::import('gii.generators.model.ModelCode');
class LWModelCode extends ModelCode
{

	/**
	 * @override
	 * Fixed the broken test for id. We want attributes such as `user_bids` to
	 * render as `userBids`, and it was rendering `userBs`
	 * 
	 * Generate a name for use as a relation name (inside relations() function in a model).
	 * @param string the name of the table to hold the relation
	 * @param string the foreign key name
	 * @param boolean whether the relation would contain multiple objects
	 * @return string the relation name
	 */
	protected function generateRelationName($tableName, $fkName, $multiple)
	{	
		//if(strcasecmp(substr($fkName,-2),'id')===0 && strcasecmp($fkName,'id') && strlen($fkName) === 2)
		if(strcasecmp(substr($fkName,-3),'_id')===0 && strcasecmp($fkName,'_id'))
			$relationName=rtrim(substr($fkName, 0, -2),'_');
		else
			$relationName=$fkName;
		$relationName[0]=strtolower($relationName);

		if($multiple)
			$relationName=$this->pluralize($relationName);

		$names=preg_split('/_+/',$relationName,-1,PREG_SPLIT_NO_EMPTY);
		if(empty($names)) return $relationName;  // unlikely
		for($name=$names[0], $i=1;$i<count($names);++$i)
			$name.=ucfirst($names[$i]);

		$rawName=$name;
		$table=Yii::app()->db->schema->getTable($tableName);
		$i=0;
		while(isset($table->columns[$name]))
			$name=$rawName.($i++);

		return $name;
	}
	
	
	/**
	 * This chunk of code was copied from the parent...just to add the little bit of code at the end, which is responsible
	 * for generating child classes to the base parent class
	 * 
	 * (non-PHPdoc)
	 * @see ModelCode::prepare()
	 */
	public function prepare()
	{
		if(($pos=strrpos($this->tableName,'.'))!==false)
		{
			$schema=substr($this->tableName,0,$pos);
			$tableName=substr($this->tableName,$pos+1);
		}
		else
		{
			$schema='';
			$tableName=$this->tableName;
		}
		if($tableName[strlen($tableName)-1]==='*')
		{
			$tables=Yii::app()->db->schema->getTables($schema);
			if($this->tablePrefix!='')
			{
				foreach($tables as $i=>$table)
				{
					if(strpos($table->name,$this->tablePrefix)!==0)
						unset($tables[$i]);
				}
			}
		}
		else
			$tables=array($this->getTableSchema($this->tableName));
	
		$this->files=array();
		$templatePath=$this->templatePath;
		$this->relations=$this->generateRelations();
	
		foreach($tables as $table)
		{
			$tableName=$this->removePrefix($table->name);
			$className=$this->generateClassName($table->name);
			$params=array(
					'tableName'=>$schema==='' ? $tableName : $schema.'.'.$tableName,
					'modelClass'=>$className,
					'columns'=>$table->columns,
					'labels'=>$this->generateLabels($table),
					'rules'=>$this->generateRules($table),
					'relations'=>isset($this->relations[$className]) ? $this->relations[$className] : array(),
			);
			
			# the code up until this point is directly from the parent...they should be kept in sync with the parent.
			
			$this->files[]=new CCodeFile(
					Yii::getPathOfAlias($this->modelPath).'/base/'.$className.'Base.php',	# this line was edited to add the "Base" suffix to the main file
					$this->render($templatePath.'/model.php', $params)
			);
			
			# a child file of the base class, which is the user-editable file
			
			if (!file_exists(Yii::getPathOfAlias($this->modelPath).'/'.$className.'.php')) {
				$this->files[]=new CCodeFile(
						Yii::getPathOfAlias($this->modelPath).'/'.$className.'.php',
						$this->render($templatePath.'/child.php', $params)
				);
			}
		}
	}
	
}