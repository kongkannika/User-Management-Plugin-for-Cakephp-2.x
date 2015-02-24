<?php

class ControllerListComponent extends Component {

	/**
	 * Used to get all controllers with all methods for permissions
	 *
	 * @access public
	 * @return array
	 */
	public function get() {
		$controllerClasses = App::objects('Controller');
		$superParentActions = get_class_methods('Controller');
		$parentActions = get_class_methods('AppController');
		//$this->_removePrivateActions($parentActions);
		//$parentActionsDefined = $this->_removePrivateActions($parentActions);
		//$parentActionsDefined = array_diff($parentActionsDefined, $superParentActions);
		$controllers = array();
		foreach ($controllerClasses as $controller) {
			$controllerName = str_replace('Controller', '', $controller);
			$actions = $this->__getControllerMethods($controllerName, $superParentActions, $parentActions);
			if (!empty($actions)) {
				$actions = array_values($actions);
				$controllers[$controllerName] = $actions;
			}
		}
		$plugins = App::objects('plugins');
		foreach ($plugins as $p) {
			$pluginAppContMethods = array();
			$pluginControllerClasses = App::objects($p . '.Controller');
			foreach ($pluginControllerClasses as $controller) {
				if (strpos($controller, 'AppController') !== false) {
					$controllerName = str_replace('Controller', '', $controller);
					$pluginAppContMethods = $this->__getControllerMethods($controllerName, $superParentActions, $parentActions, $p);
				}
			}
			foreach ($pluginControllerClasses as $controller) {
				$controllerName = str_replace('Controller', '', $controller);
				$actions = $this->__getControllerMethods($controllerName, $superParentActions, $parentActions, $p);
				if (strpos($controller, 'AppController') === false && is_array($actions)) {
					$actions = array_diff($actions, $pluginAppContMethods);
				}
				if (!empty($actions)) {
					$actions = array_values($actions);
					$controllers[$controllerName] = $actions;
				}
			}
		}
		return $controllers;
	}

	/**
	 * Used to delete private actions from list of controller's methods
	 *
	 * @access private
	 * @param array $actions Controller's action
	 * @return array
	 */
	private function _removePrivateActions($actions) {
		foreach ($actions as $k => $v) {
			if ($v{0} == '_') {
				unset($actions[$k]);
			}
		}
		return $actions;
	}

	/**
	 * Used to get methods of controller
	 *
	 * @access private
	 * @param string $controllerName Controller name
	 * @param array $superParentActions Controller class methods
	 * @param array $parentActions App Controller class methods
	 * @param string $p plugin name
	 * @return array
	 */
	private function __getControllerMethods($controllerName, $superParentActions, $parentActions, $p = null) {
		if (empty($p)) {
			App::import('Controller', $controllerName);
		}
		else {
			App::import('Controller', $p . '.' . $controllerName);
		}
		$actions = get_class_methods($controllerName . "Controller");
		if (!empty($actions)) {
			$actions = $this->_removePrivateActions($actions);
			$actions = ($controllerName == 'App') ? array_diff($actions, $superParentActions) : array_diff($actions, $parentActions);
		}
		return $actions;
	}

	/**
	 *  Used to get controller's list
	 *
	 * @access public
	 * @return array
	 */
	public function getControllers() {
		$controllerClasses = App::objects('Controller');
		foreach ($controllerClasses as $key => $value) {
			$controllerClasses[$key] = str_replace('Controller', '', $value);
		}
		$controllerClasses[-2] = "Select Controller";
		$controllerClasses[-1] = "All";
		$plugins = App::objects('plugins');
		foreach ($plugins as $p) {
			$pluginControllerClasses = App::objects($p . '.Controller');
			foreach ($pluginControllerClasses as $controller) {
				$controllerClasses[] = str_replace('Controller', '', $controller);
			}
		}
		ksort($controllerClasses);
		return $controllerClasses;
	}

	/**
	 *  Used to get controllers with methods
	 *
	 * @access public
	 * @return array
	 */
	public function getControllerWithMethods() {
		$res1 = $this->get();
		$res2 = array();
		foreach ($res1 as $key => $value) {
			foreach ($value as $ac) {
				$res2[] = $key . '/' . $ac;
			}
		}
		return $res2;
	}

}
