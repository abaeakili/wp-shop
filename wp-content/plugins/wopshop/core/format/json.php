<?php
class RegistryFormatJSON extends RegistryFormat {

	public function objectToString($object, $options = array()) {
		return json_encode($object);
	}

	public function stringToObject($data, $options = array('processSections' => false)) {
		// Fix legacy API.
		if (is_bool($options)) {
			$options = array (
							'processSections' => $options 
			);
			
			// Deprecation warning.
			MLog::add('MRegistryFormatJSON::stringToObject() second argument should not be a boolean.', MLog::WARNING, 'deprecated');
		}
		
		$data = trim($data);
		if ((substr($data, 0, 1) != '{') && (substr($data, - 1, 1) != '}')) {
			$ini = RegistryFormat::getInstance('INI');
			$obj = $ini->stringToObject($data, $options);
		}
		else {
			$obj = json_decode($data);
		}
		return $obj;
	}
}