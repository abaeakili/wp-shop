<?php
abstract class RegistryFormat {

	protected static $instances = array ();

	public static function getInstance($type) {
		// Sanitize format type.
		$type = strtolower(preg_replace('/[^A-Z0-9_]/i', '', $type));
		
		// Only instantiate the object if it doesn't already exist.
		if (! isset(self::$instances[$type])) {
			// Only load the file the class does not exist.
			$class = 'RegistryFormat' . $type;
			
			if (! class_exists($class)) {
				$path = dirname(__FILE__) . '/format/' . $type . '.php';
				if (is_file($path)) {
					include_once $path;
				}
				else {
					throw new Exception(MText::_('LIB_REGISTRY_EXCEPTION_LOAD_FORMAT_CLASS'), 500, E_ERROR);
				}
			}
			
			self::$instances[$type] = new $class();
		}
		
		return self::$instances[$type];
	}

	abstract public function objectToString($object, $options = null);

	abstract public function stringToObject($data, $options = null);
}