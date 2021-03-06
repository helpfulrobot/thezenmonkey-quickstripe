<?php

class ContactUsPage extends Page implements HiddenClass {

	static $db = array(
		
	);

	static $has_many = array(
		
	);

	/**
	 * Change the contact-us page to ContactUsPage type
	 */
	
	function requireDefaultRecords() {
		if(!SiteTree::get_by_link("contact-us")){
			$contactpage = new HomePage();
			$contactpage->Title = "Contact Us";
			$contactpage->URLSegment = "contact-us";
			$contactpage->Sort = 3;
			$contactpage->write();
			$contactpage->publish('Stage', 'Live');
			$contactpage->flushCache();
			DB::alteration_message('Contact Us created', 'created');
		} else {
			$contactpage = SiteTree::get_by_link("contact-us");
			if($contactpage->ClassName != "ContactUsPage") {
				$contactpage = $contactpage->newClassInstance("ContactUsPage");
				$contactpage->write();
				$contactpage->publish('Stage', 'Live');
				$contactpage->flushCache();
				DB::alteration_message('Contact Us changed to ContactUsPage', 'changed');
			}
		}
	
		parent::requireDefaultRecords();
	}
	
}

class ContactUsPage_Controller extends Page_Controller 
{
	private static $allowed_actions = array (
		"SimpleContactForm"
	);
	
	
	public function init() {
 		
 		parent::init();
 	}
	
	public function SimpleContactForm() {
        return SimpleContactForm::create($this, 'SimpleContactForm');
    }
	
			
}