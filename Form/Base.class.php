<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

abstract class liveagent_Form_Base extends liveagent_Base {

	const TYPE_FORM = 'default';
	const TYPE_TEMPLATE = 'template';

	/**
	 * @var HTMLForm
	 */
	protected $form;
	private $settings;
	private $formName;
	protected $connectionSucc = false;

	protected function onConnectionFailed() {
		$message = __('Unable to connect, check your account settings', LIVEAGENT_PLUGIN_NAME);
		$this->_log($message);
		$this->parseBlock('login_check_failed', array('connection-error' => $message));
	}
	
	protected function getNoAccountMessage() {
		return __('No account selected. Enter your existing account credetials, or create new free trial <a href="http://www.qualityunit.com/liveagent/pricing/hosted/#wordpress" target="_blank">free trial</a>. (No credit card required)', LIVEAGENT_PLUGIN_NAME);
	}
	
	protected function onNoAccount() {		 
		$this->parseBlock('login_check_failed', array('connection-error' => $this->getNoAccountMessage()));
	}

	public function __construct($name = null, $action = null) {
		$this->formName = $name;
		if ($name !== null && $action !== null) {
			$this->loadSettingsString($name);
			$this->form = new HTMLForm($name, 'post', $action, '', $this->getType());
		} else {
			$this->form = new HTMLForm('', '', '', '', $this->getType());
		}
		$this->initForm();
	}

	private function loadSettingsString($name) {
		ob_start();
		settings_fields($name);
		$this->settings = ob_get_contents();
		ob_end_clean();
	}

	protected abstract function getType();

	protected function initForm() {
		$settings = new liveagent_Settings();
		$auth = new liveagent_Auth();
		if (!strlen(trim($settings->getLiveAgentUrl())) || !strlen(trim($settings->getOwnerEmail()))) {						
			$this->onNoAccount();
			return;
		}
		try {
			$auth->ping();
			$settings->getOwnerSessionId();
			$this->connectionSucc = true;
		} catch (liveagent_Exception_ConnectProblem $e) {
			$this->showConnectionError();
		}
	}

	protected abstract function getTemplateFile();

	protected function addSubmit() {
		$this->form->add('submit', 'submit', __('Save', LIVEAGENT_PLUGIN_NAME), array('class'=>'button-primary'));
	}

	protected function addHtml($name, $code) {
		$this->form->add('html', $name, $code);
	}

	protected function getOption($name) {
		return get_option($name);
	}

	protected function addCheckbox($name, $templateName = null, $additionalCode = '') {
		if ($this->getOption($name) == 'true') {
			$checked = 'checked';
		} else {
			$checked = '';
		}
		if ($templateName === null) {
			$templateName = $name;
		}
		$this->form->add('html', $templateName, '<input type="checkbox" name="'.$name.'" id="'.$name.'_" value="true" '.$checked.' '.$additionalCode.'></input>');
	}

	protected function parseBlock($name, $variables) {
		$this->form->parseBlock($name, $variables);
	}

	protected function addSelect($name, $options) {
		//options = assoc. arr, key(value) and value(name) od select option
		$select = $this->form->add('select', $name, $this->getOption($name));
		$select->addOptions($options);
		return $select;
	}

	protected function addPassword($name, $size = 20) {
		$this->form->add('password', $name, '', array('size' => $size, 'value' => $this->getOption($name)));
	}

	protected function addTextBox($name, $size = 20) {
		$this->form->add('text', $name, '', array('size' => $size, 'value' => $this->getOption($name)));
	}

	public function render($toVar = false) {
		if ($this->formName != null) {
			$this->form->add('html', 'form-settings', $this->settings);
		}
		return $this->form->render($this->getTemplateFile(), $toVar);
	}

	public function renderTemplate($templateFile) {
		$this->form->render($templateFile);
	}
}
?>