<?php

class SimpleContactForm extends Form
{
    
    public function __construct($controller, $name)
    {
        $fields = new FieldList(
            TextField::create("Name"),
            EmailField::create("Email")->setAttribute('type', 'email'),
            TextareaField::create("Company", "Message")->setAttribute('autocomplete', 'no'),
            TextareaField::create("EmailMessage", "Company")->addExtraClass("honeypot")->setAttribute('autocomplete', 'no')
        );
        
        if (!class_exists('FormSpamProtectionExtension')) {
            $fields->insertAfter(HiddenField::create("TimeLog", '', time()), 'EmailMessage');
        }
        
        $actions = new FieldList(FormAction::create("doSubmit")->setTitle("Submit")->addExtraClass('button')->setUseButtonTag(true));
         
        parent::__construct($controller, $name, $fields, $actions);
    }
    
    public function forTemplate()
    {
        return $this->renderWith(array(
          $this->class,
          'Form'
       ));
    }
    
    public function doSubmit(array $data, Form $form)
    {
        //basic spam protection
        if ($data['EmailMessage']) {
            $form->addErrorMessage('Message', 'We may have mistakenly marked your message as spam, please contact us via phone or email', 'warning');
            Controller::curr()->redirectBack();
        }
        
        if (!class_exists('FormSpamProtectionExtension')) {
            $time = time() - 20;
            if ($data['TimeLog'] <= $time) {
                $form->addErrorMessage('Message', 'We may have mistakenly marked your message as spam, please contact us via phone or email', 'warning');
                Controller::curr()->redirectBack();
            }
        }
        
        $siteConfig = SiteConfig::current_site_config();
        if ($siteConfig->SiteEmail) {
            $From = $siteConfig->SiteEmail;
        } else {
            $From = $siteConfig->MainEmail;
        }
        
        $To = $siteConfig->SiteEmail;
        $Subject = "Website Contact From ".$data['Name'];
        $Body = $data['Company']."<br>\n ".$data['Email'];
        $email = new Email($From, $To, $Subject, $Body);
        $email->replyTo($data['Email']);
        $email->send();
        $redirect = false;
        /*
if($siteConfig->DefaultThankYouID != 0 && !$data['CustomThankYou']) {
            $redirect = ThankYouPage::get()->byID($siteConfig->DefaultThankYouID);
        } elseif ($data['CustomThankYou']) {
            $redirect = ThankYouPage::get()->byID($data['CustomThankYou']);
        }
        
        if($redirect){
            Controller::curr()->redirect($redirect->URLSegment);
        } else {
            $form->addErrorMessage('Message', 'Thank you, someone from our office will contact you shortly', 'success');
            Controller::curr()->redirectBack();
        }
*/
        $form->addErrorMessage('Message', 'Thank you, someone from our office will contact you shortly', 'success');
        Controller::curr()->redirectBack();
    }
}
