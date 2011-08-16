<?php

require_once(TOOLKIT . '/class.administrationpage.php');
require_once(TOOLKIT . '/class.sectionmanager.php');

class contentExtensionDynamic_Section_EditorData extends AdministrationPage {
	
	function view(){
		
		$callback = $this->_Parent->getPageCallback();
		$section_id = $callback['context'][0];
		$sectionManager = new SectionManager(Symphony::Engine());
		
		if(!is_numeric($section_id)){
			$section_id = $sectionManager->fetchIDFromHandle($section_id);
		}		
		
		$section = $sectionManager->fetch($section_id);
		if(is_object($section)){
			$fields = $section->fetchFields();
			foreach($fields as $field){
				if($field->get('dse_dynamic') == 'yes'){
					$return[] = Array(
						'id' => $field->get('id'),
						'link_id' => $field->get('dse_link_id'),
						'show' => $field->get('dse_show'),
						'value' => explode(', ',(is_null($field->get('dse_filter_value'))?"":$field->get('dse_filter_value')))
					);
				}
			}
			echo json_encode($return);
		}
		else{
			echo json_encode(false);
		}		
		exit();
	}
}