<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Login
 *
 * @author pindorama
 */
class Form_RegisterForm extends Zend_Form {
      
    
    public function __construct($options = null) {
        parent::__construct($options);
        
        $this->setName('login');
        
        
        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Your Username:');
        $username->setAttrib('size', 35);
        $username->setRequired(true);
        $username->removeDecorator('label');
        $username->removeDecorator('htmlTag');
        $username->addValidator('Alnum'); 
        $username->removeDecorator('Errors');
        $username->addErrorMessage('Your username can consist solely of letters and numbers');


        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Your E-mail Address:');
        $email->setAttrib('size', 35);
        $email->setRequired(true);
        $email->addValidator('EmailAddress');
        $email->removeDecorator('label');
        $email->removeDecorator('htmlTag');
        $email->removeDecorator('Errors');
        $email->addErrorMessage('Please provide a valid e-mail address');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password:');
        $password->setAttrib('size', 35);
        $password->setRequired(true);
        $password->addValidator('StringLength', false, array('min' => 6));
        $password->removeDecorator('label');
        $password->removeDecorator('htmlTag');
        $password->removeDecorator('Errors');
        $password->addErrorMessage('Please provide a valid password');

        $confirmPswd = new Zend_Form_Element_Password('confirm_pswd');
        $confirmPswd->setLabel('Confirm Password:');
        $confirmPswd->setAttrib('size', 35);
        $confirmPswd->removeDecorator('label');
        $confirmPswd->removeDecorator('htmlTag');
        $confirmPswd->addValidator('Identical', false, array('token' => 'password'));
        $confirmPswd->removeDecorator('Errors');
        $confirmPswd->addErrorMessage('The passwords do not match');
        
        $gender = new Zend_Form_Element_Radio('gender');
        $gender->setLabel('Gender:')
            ->addMultiOptions(array(
                    'male' => 'Male',
                    'female' => 'Female' 
                        ))
            ->setSeparator('');    

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Create Your Account');
        $submit->removeDecorator('DtDdWrapper');
        
        
        //add elements
        $this->addElements(array($username,$email, $password, $confirmPswd, $gender, $submit ));
        $this->setMethod('post');
        //pay attention hier the path Zend_Controller_Front::getInstance()->getBaseUrl() = the home path
        //submit "action" send to authentication/login
        $this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl().'/authentication/checklogin');
       
         //$this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl());
    }
    
}

?>
