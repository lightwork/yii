<?php

abstract class LWWidget extends CWidget
{
	/**
	 * @var string URL where to look assets.
	 */
	private $assetsUrl;
	
	/**
	 * @var string id of the widget.
	 */
	private $_id;
	
	private $_reflection;
	
	/**
	 * Returns a reflection object for this model
	 * @return ReflectionClass
	 */
	public function getReflection() {
		if (empty($this->_reflection)) {
			$this->_reflection = new ReflectionClass($this);
		}
		return $this->_reflection;
	}
	
	/**
	 * Base init method for widget.
	 * All child classes *must* call this using parent::init(dirname(__FILE__));
	 * passing allong the location where we'll find their assets folder.
	 * 
	 * (non-PHPdoc)
	 * @see CWidget::init()
	 * 
	 * @param string $file The file path to folder containing the child widget class.
	 */
	public function init()
	{
		parent::init();
		$this->registerAssets();
		// this method is called by CController::beginWidget()
	}

	/**
	 * Sets the ID of the widget.
	 * @param string $value id of the widget.
	 */
	public function setId($value)
	{
		parent::setId($value);
		$this->_id=$value;
	}

	/**
	 * @override
	 * 
	 * Adjust this method to be more random. Removes the relience on an internal counter, which can
	 * result in two items both with id of yw0 if new widget loaded via ajax.
	 * 
	 * @param boolean $autoGenerate whether to generate an ID if it is not set previously
	 * @return string id of the widget.
	 */
	public function getId($autoGenerate=true)
	{
		if($this->_id !== null) {
			return $this->_id;
		}
		else {
			$this->_id = parent::getId($autoGenerate);
			return $this->_id .= mt_rand(0, 9999999);
		}
	}
	
	
	public function getAssetsUrl() {
		if($this->assetsUrl===null) {
			$path = $this->Reflection->getFileName();
			$dir = pathinfo($path, PATHINFO_DIRNAME);
			$this->assetsUrl = Yii::app()->getAssetManager()->publish($dir.'/assets',false,-1,YII_DEBUG);
		}
		
		return $this->assetsUrl;
	}
	
	/*
	 * $cs = Yii::app()->clientScript;
	 * $cs->registerCoreScript( 'jquery' );
	 * $cs->registerCssFile($this->assetsUrl.'/socialAssetFrame.css');
	 * $cs->registerScriptFile($this->assetsUrl.'/slidingMenu.js', Yii::app()->getClientScript()->coreScriptPosition);
	 */
	public function registerAssets()
	{
	}
}

?>