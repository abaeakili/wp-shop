<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class CheckoutStepWshopModel extends WshopModel{    
    public function getNextStep($step){
		$config = Factory::getConfig();
		
		if ($step == 2){
			if ($config->without_shipping && $config->without_payment){
				$next = 5;
				return $next;
			}
			if ($config->without_payment){
				$next = 4;
				return $next;
			}

			if ($config->step_4_3){
				if ($config->without_shipping){
					$next = 3;
					return $next;
				}
				$next = 4;
				return $next;
			} else {
				$next = 3;
				return $next;
			}
		}
        
		if ($step == 3){
			if ($config->without_shipping) {
				$next = 5;
				return $next;				
			}
			
			if ($config->step_4_3){
				$next = 5;
				return $next;
			} else {
				$next = 4;
				return $next;
			}
		}
        
		if ($step == 4){
			if ($config->step_4_3 && !$config->without_payment){				
				$next = 3;
				return $next;
			} else {
				$next = 5;
				return $next;
			}
		}
	}
    
	public function getCheckoutUrl($step, $redirect = 1){
		$config = Factory::getConfig();
		if (preg_match('/^(\d)+$/', $step)){			
			$task = 'step'.$step;
		} else {
			$task = $step;
		}
        
		$url = SEFLink('controller=checkout&task='.$task, 0, $redirect, $config->use_ssl);
		return $url;
	}
}