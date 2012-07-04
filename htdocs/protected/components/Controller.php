<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public $pageTitle = '';

	/**
	 * The mootools js selector for the target element of the main action widget.
	 * If null, no widget will be auto-init. If set, then a widget located in
	 * App.ControllerId.ActionId will be auto-init using this selector as the
	 * element to target.
	 *
	 * @see getMyJsClassName
	 *
	 * @var string
	 */
	public $targetElementSelector;

	public $jsClassName;

	public function getRandomId($prefix = 'monkeys_') {
		return uniqid($prefix);
	}

	/**
	 * Useful for creating highcharts formatted dates.
	 *
	 * @param int $y
	 * @param int $m
	 * @param int $d
	 * @return string
	 */
	protected function dateAsJsUtc($y, $m, $d)
	{
		return sprintf('js:Date.UTC(%s, %s, %s)', $y, $m - 1, $d);
	}

	public function createUrl($route, $params=array(), $ampersand='&') {

		$url=parent::createUrl($route,$params,$ampersand);

		if(Yii::app()->request->isJsonpRequest) {
			if(strpos($url,'http')===FALSE) {
				$url = Yii::app()->getRequest()->getHostInfo('').$url;
			}
		}

		return $url;
	}


	public function init() {

		Yii::app()->getClientScript()->coreScriptPosition = CClientScript::POS_END;

	}



	public function getRoute()
	{

		$route=$this->getId();
		if ($this->getAction()) {
			$route .= '/' . $this->getAction()->getId();
		}
		if(strpos($route,'/')===false)
			$route=$this->getId().'/'.$route;
		if($route[0]!=='/' && ($module=$this->getModule())!==null)
			$route=$module->getId().'/'.$route;
		return trim($route,'/');

	}



	/**
	 *
	 * @return User
	 */
	public function getUser()
	{
		return Yii::app()->user->getUser();
	}


	protected function afterRender($view, &$output)
	{
		if(!Yii::app()->request->IsAjaxRequest && !Yii::app()->request->IsJsonpRequest) {
			Yii::app()->globaljs->render();
		}
		parent::afterRender($view, $output);
	}


	protected function loginRoutine($json = false) {

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_REQUEST['LoginForm']))
		{
			$model->attributes=$_REQUEST['LoginForm'];
			// validate user input and redirect to the previous page if valid

			if($model->validate() && $model->login()) {
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}

		return $model;

	}

	public function getMyJsClassName() {
		return $this->getJsClassName();
	}

	/**
	 * For actions that abide by the js framework rules, where each action has
	 * a main widget located in App.ControllerId.ActionId, this will return
	 * the class namespace.
	 * @retrun string
	 */
	public function getJsClassName() {
		if(!isset($this->jsClassName)) {
			$this->jsClassName = ucwords($this->id) . "." . ucwords($this->action->id);
		}
		return $this->jsClassName;
	}

	public function getJsClassNamePath()
	{
		$a = explode('.', $this->JsClassName);
		foreach($a as $key=>$part) { $a[$key] = lcfirst($part); }
		return implode('/', $a);
	}

	/**
	 * Register a js widget with the app sandbox.
	 *
	 * @param string $class The widget class, e.g. App.Widget.Name
	 * @param string $selector The dom selector to scope the widget to.
	 * @param array $options Options object for widget.
	 * @param array $packages Any packages the widget depends upon.
	 */
	public function registerJsWidget($class, $selector = '', $options = array(), $packages = array())
	{
		$cs = Yii::app()->clientScript;

		foreach($packages as $package) {
			$cs->registerPackage($package);
		}

		if ($selector === true) {
			$selector = '#'.$this->getId();
		}

		$opts = CJSON::encode((object)$options);

		$cs->registerScript(
			sprintf('%s.%s.%s.%s', $this->getId(), $class, $selector, 'sandbox'),
			"window.addEvent('sandboxReady', function(sandbox){ sandbox.addWidget({$class}, '$selector', $opts);});",
			$cs->coreScriptPosition
		);
	}

}
