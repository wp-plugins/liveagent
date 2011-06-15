<?php
/**
 *   @copyright Copyright (c) 2011 Quality Unit s.r.o.
 *   @author Juraj Simon
 *   @package WpLiveAgentPlugin
 *   @version 1.0.0
 *
 *   Licensed under GPL2
 */

class liveagent_WidgetIntegrator extends liveagent_Base {
	/**
	 * @var liveagent_Settings
	 */
	private $settings;
	/**
	 * @var liveagent_helper_Buttons
	 */
	private $buttonHelper;

	public function __construct(liveagent_Settings $settings, liveagent_helper_Buttons $buttonHelper) {
		$this->settings = $settings;
		$this->buttonHelper = $buttonHelper;
	}

	public function initWidgets() {
		try {
		$buttons = $this->settings->getButtonsGridRecordset();
		} catch (Exception $e) {
			$this->_log(__('Unable to register widgets', LIVEAGENT_PLUGIN_NAME));
			return;
		}
		foreach ($buttons as $button) {
			if ($button->get('contenttype') != 'F') {
				$id = $button->get('id');
				$widget = new liveagent_widget_Button($id, $button->get('onlinecode'), $button->get('offlinecode'));
				wp_register_sidebar_widget(liveagent_widget_Button::WIDGET_PREFIX . $id, __('Live Agent Button', LIVEAGENT_PLUGIN_NAME) . ' (' . $id . ')', array($widget, 'showWidget'),
				array(
    					'description' => 'Live agent chat button ' . '(' . $this->buttonHelper->getTypeHumanReadable($button->get('contenttype')) . ')'
    					), $id
    					);
			}
		}
		$this->insertActiveWidgets();
	}

	private function getFirstSidebar() {
		$wp_registered_sidebars = wp_get_sidebars_widgets();
		if (count($wp_registered_sidebars) == 0) {
			return false;
		}
		foreach ($wp_registered_sidebars as $key => $sidebar) {
			if ($key == 'wp_inactive_widgets') {
				continue;
			}
			return $key;
		}
		return false;
	}

	private function isWidgetActive($widgetId) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( empty( $sidebars_widgets ) ) {
			return false;
		}
		foreach($sidebars_widgets as $sidebar => $widgets) {
			if(in_array($widgetId, $sidebars_widgets[$sidebar])) {
				return true;
			}
		}
	}

	private function activateWidget($widgetId, $sidebar) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( empty( $sidebars_widgets ) ) {
			return;
		}
		if ( empty( $sidebars_widgets[$sidebar] ) ) {
			$sidebars_widgets[$sidebar] = array();
		}
		if(!in_array($widgetId, $sidebars_widgets[$sidebar])) {
			$sidebars_widgets[$sidebar][] = $widgetId;
			wp_set_sidebars_widgets( $sidebars_widgets );
		}
	}

	private function deactivateWidget($widgetId) {
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( empty( $sidebars_widgets ) ) {
			return;
		}
		foreach($sidebars_widgets as $sidebar => $widgets) {
			if(in_array($widgetId, $sidebars_widgets[$sidebar])) {
				$key = array_search($widgetId, $sidebars_widgets[$sidebar]);
				unset($sidebars_widgets[$sidebar][$key]);
				$sidebars_widgets[$sidebar] = array_values($sidebars_widgets[$sidebar]);
				wp_set_sidebars_widgets( $sidebars_widgets );
				return;
			}
		}
	}

	private function insertActiveWidgets() {
		$buttons = $this->settings->getButtonsGridRecordset();
		$firstSidebar = $this->getFirstSidebar();
		if ($firstSidebar === false) {
			return;
		}
		foreach ($buttons as $button) {
			if ($button->get('contenttype')!='F' && $this->settings->buttonIsEnabled($button->get('id'))) {
				if (!$this->isWidgetActive(liveagent_widget_Button::WIDGET_PREFIX . $button->get('id'))) {
					$this->activateWidget(liveagent_widget_Button::WIDGET_PREFIX . $button->get('id'), $firstSidebar);
				}
			} else if ($button->get('contenttype')!='F' && !$this->settings->buttonIsEnabled($button->get('id'))) {
				$this->deactivateWidget(liveagent_widget_Button::WIDGET_PREFIX . $button->get('id'));
			}
		}
	}
}

?>