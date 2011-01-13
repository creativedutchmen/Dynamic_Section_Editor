<?php

	require_once(TOOLKIT . "/class.sectionmanager.php");

	Class extension_dynamic_section_editor extends Extension{
		
		protected $_section;

		public function about(){
			return array(
				'name' => 'Dynamic section editor',
				'version' => '1.0',
				'release-date' => '2011-10-01',
				'author' => array(
					'name' => 'Huib Keemink',
					'website' => 'http://www.creativedutchmen.com',
					'email' => 'huib.keemink@creativedutchmen.com'
				)
			);
		}
		
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'AdminPagePostGenerate',
					'callback' => 'doMagic'
				),
				array(
					'page' => '/blueprints/sections/',
					'delegate' => 'FieldPostEdit',
					'callback' => 'saveFieldPrefs'
				),
				array(
					'page' => '/blueprints/sections/',
					'delegate' => 'FieldPostCreate',
					'callback' => 'saveFieldPrefs'
				),
				array(
					'page' => '/publish/edit/',
					'delegate' => 'EntryPreRender',
					'callback' => 'addFilterClass'
				),
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'addScriptsAndStyles'
				),
			);
		}
		
		public function doMagic(&$context){
			$callback = $context['parent']->getPageCallback();
			if ($callback['driver'] == 'blueprintssections' && in_array($callback['context'][0], array('edit', 'new'))){
				
				$sectionManager = new SectionManager();
				$this->_section = $sectionManager->fetch($callback['context'][1]);
				
				if(is_object($this->_section)){
					$this->addPreferences($context);
					if($this->_section->get('dse_dynamic') == 'yes'){
						$this->addFieldOptions($context);
					}
				}
			}
		}
		
		public function addPreferences(&$context){
			
			$dom = DOMDocument::loadHTML($context['output']);
			$xpath = new DOMXPath($dom);
			
			$meta = $xpath->query("//input[@name='meta[hidden]']")->item(0);
			
			$label = $dom->createElement('label');
			
			$hidden = $dom->createElement('input');
			$hidden->setAttribute('type', 'hidden');
			$hidden->setAttribute('value', 'no');
			$hidden->setAttribute('name', 'meta[dse_dynamic]');
			$meta->parentNode->parentNode->appendChild($hidden);
			
			$input = $dom->createElement('input');
			$input->setAttribute('type', 'checkbox');
			$input->setAttribute('value', 'yes');
			$input->setAttribute('name', 'meta[dse_dynamic]');
			if($this->_section->get('dse_dynamic') == 'yes'){
				$input->setAttribute('checked', 'yes');
			}
			
			$label->appendChild($input);
			$label->appendChild(new DomText(__(' Make this a dynamic section. This will enable you to hide/show fields based on the value of another field.')));
			
			$meta->parentNode->parentNode->appendChild($label);

			$context['output'] = $dom->saveHTML();
			
		}
		
		public function addFieldOptions(&$context){
			$dom = DOMDocument::loadHTML($context['output']);
			$xpath = new DOMXPath($dom);
			
			$domFields = $xpath->query("//li[substring-before(@class, '-') = 'field']");
			
			$order = 0;
			foreach($domFields as $domField){
				
				$field = null;
				foreach($this->_section->fetchFields() as $sym_field){
					if($sym_field->get('sortorder') == $order){
						$field = $sym_field;
					}
				}
				
				$div = $dom->createElement('div');
				$div->setAttribute('class', 'dse_settings');
				
				$label = $dom->createElement('label');
				$label->setAttribute('class', 'meta dse_dynamic');
			
				$hidden = $dom->createElement('input');
				$hidden->setAttribute('type', 'hidden');
				$hidden->setAttribute('value', 'no');
				$hidden->setAttribute('name', 'options_' . $field->get('id') . '[dse_dynamic]');
				$domField->appendChild($hidden);
				
				$input = $dom->createElement('input');
				$input->setAttribute('type', 'checkbox');
				$input->setAttribute('value', 'yes');
				$input->setAttribute('class', 'dse_watch');
				$input->setAttribute('name', 'options_' . $field->get('id') . '[dse_dynamic]');
				if($field->get('dse_dynamic') == 'yes'){
					$input->setAttribute('checked','true');
				}
				
				$label->appendChild($input);
				$label->appendChild(new DomText(__(' Show/hide this field based on the value of another field.')));
				$div->appendChild($label);
				
				$label = $dom->createElement('label');
				$label->setAttribute('class', 'meta dse_hide');
				
				$select = $dom->createElement('select');
				$select->setAttribute('name','options_' . $field->get('id') . '[dse_show]');
				$select->setAttribute('class','dse_options_show');
				
				$option = $dom->createElement('option');
				$option->setAttribute('value','yes');
				$option->appendChild(new DomText(__('show')));
				if($field->get('dse_show') != 'no'){
					$option->setAttribute('selected','yes');
				}
				$select->appendChild($option);
				
				$option = $dom->createElement('option');
				$option->setAttribute('value','no');
				if($field->get('dse_show') == 'no'){
					$option->setAttribute('selected','yes');
				}
				$option->appendChild(new DomText(__('hide')));
				$select->appendChild($option);
				
				$label->appendChild(new DomText(__('Dynamic preferences')));
				$label->appendChild($select);
				$div->appendChild($label);
				
				$text = $dom->createElement("p");
				$text->appendChild(new DomText(__(' if ')));
				$div->appendChild($text);
				
				$label = $dom->createElement('label');
				$label->setAttribute('class', 'meta dse_link');
				$select = $dom->createElement('select');
				$select->setAttribute('class','dse_options_link');
				$select->setAttribute('name','options_' . $field->get('id') . '[dse_link_id]');
				
				foreach($this->_section->fetchFields() as $sym_field){
					//we do not want to hide the field based on its own value. That would be.. wrong.
					if($sym_field->get('sortorder') != $order){
						$option = $dom->createElement('option');
						$option->setAttribute('value', $sym_field->get('id'));
						if($sym_field->get('id') == $field->get('dse_link_id')){
							$option->setAttribute('selected', 'yes');
						}
						
						$option->appendChild(new DomText($sym_field->get('label')));
						$select->appendChild($option);
					}
				}
				$label->appendChild($select);
				$div->appendChild($label);
				
				$text = $dom->createElement("p");
				$text->appendChild(new DomText(__(' is ')));
				$div->appendChild($text);
				
				$label = $dom->createElement('label');
				$label->setAttribute('class', 'meta dse_filter_value');
				
				$input = $dom->createElement('input');
				$input->setAttribute('name','options_' . $field->get('id') . '[dse_filter_value]');
				$input->setAttribute('value', $field->get('dse_filter_value'));
				$label->appendChild($input);
				
				$div->appendChild($label);
				
				$domField->appendChild($div);
				
				$context['output'] = $dom->saveHTML();
				
				$order++;
			}
		}
		
		public function saveFieldPrefs(&$context){	
			$field = $context['field'];
			$id = $field->get('id');
			if(isset($_POST['options_' . $id])){
				Symphony::Database()->update($_POST['options_' . $id], 'tbl_fields', " `id` = $id");
			}
		}
		
		public function addFilterClass(){
		}
		
		public function addScriptsAndStyles(&$context){
			$sectionManager = new SectionManager();
			$callback = $context['parent']->getPageCallback();
			$section = $sectionManager->fetch($sectionManager->fetchIDFromHandle($callback['context']['section_handle']));
			
			if(is_object($section)){
				if($section->get('dse_dynamic')){
					$context['parent']->Page->addScriptToHead(URL . '/extensions/dynamic_section_editor/assets/dse_filter.js', 222);
				}
			}
		}

		public function install(){
			if(!Symphony::Database()->query("ALTER TABLE `tbl_sections` ADD `dse_dynamic` ENUM( 'yes', 'no' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no'")) return false;
			if(!Symphony::Database()->query(
				"ALTER TABLE `tbl_fields` ADD `dse_dynamic` ENUM( 'yes', 'no' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
				ADD `dse_link_id` INT( 11 ) UNSIGNED NULL ,
				ADD `dse_show` ENUM( 'yes', 'no' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
				ADD `dse_filter_value` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL"
			)) return false;
			return true;
		}			
	
		public function uninstall(){
			if(!Symphony::Database()->query("ALTER TABLE `tbl_sections` DROP `dse_dynamic`")) return false;
			if(!Symphony::Database()->query(
				"ALTER TABLE `tbl_fields`
				DROP `dse_dynamic` ,
				DROP `dse_link_id` ,
				DROP `dse_show` ,
				DROP `dse_filter_value`"
			)) return false;
			return true;
		}
	}