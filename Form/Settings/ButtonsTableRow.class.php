<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_Form_Settings_ButtonsTableRow extends liveagent_Form_Base {
	/**
	 * @var liveagent_helper_Buttons
	 */
	private $buttonHelper;
	private $buttonId;
	private $buttonType;

	public function __construct($buttonid, $type) {
		$this->buttonId = $buttonid;
		$this->buttonType = $type;
		parent::__construct();
	}

	protected function getTemplateFile() {
		return $this->getTemplatesPath() . 'ButtonsTableRow.xtpl';
	}

	protected function getType() {
		return liveagent_Form_Base::TYPE_TEMPLATE;
	}

	protected function initForm() {
		$this->addHtml('la-htmlonlinebutton-id', $this->buttonId . '_ON');
		$this->addHtml('la-htmlofflinebutton-id', $this->buttonId . '_OF');
		$this->addHtml('buttonType', $this->buttonType);
		$this->addCheckbox(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME . '[' . $this->buttonId . ']', 'isEnabled');
	}

	protected function addCheckbox($name, $templateName = null, $additionalCode = '') {
		$value = $this->getOption(liveagent_Settings::BUTTONS_CONFIGURATION_SETTING_NAME);
		if (array_key_exists($this->buttonId, $value) && $value[$this->buttonId] == 'true') {
			$checked = 'checked';
		} else {
			$checked = '';
		}
		if ($templateName === null) {
			$templateName = $name;
		}
		$this->form->add('html', $templateName, '<input type="checkbox" name="'.$name.'" id="'.$name.'_" value="true" '.$checked.' '.$additionalCode.'></input>');
	}

	public function render() {
		return parent::render(true);
	}
}

?>