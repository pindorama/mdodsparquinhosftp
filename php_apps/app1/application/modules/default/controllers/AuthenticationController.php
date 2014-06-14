<?php

class Default_AuthenticationController extends Zend_Controller_Action {

    protected $_statusMessenger = null;
    protected $_errorMessenger = null;
    protected $_form = null;

    public function init() {

        $this->view->documentClasses = array('default', 'authentication');
        $this->_statusMessenger = $this->_helper->flashMessenger;
        $this->_errorMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
        $this->_errorMessenger->setNamespace('error');
        $this->view->statusMessages = $this->_statusMessenger->getMessages();

        $form = $this->_form = new Form_LoginForm();
        $this->_helper->layout()->varname = $form;
    }

    public function indexAction() {
        // action body
    }

    public function loginAction() {
        $this->view->documentClasses = array('default', 'login');
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_statusMessenger->addMessage('Sie sind eingeloggt.');
            $this->_redirect('/default');
        }
        // $this->view->form = $this->_getLoginForm();
        $this->view->errorMessages = $this->_errorMessenger->getMessages();
    }

    public function checkloginAction() {

        // Zurueck zu index, falls kein POST-Request
        // hereinkommt
        if (!$this->_request->isPost()) {
            $this->_errorMessenger->addMessage('Sie k&ouml;nnen sich nur &uuml;ber das Login-Formular anmelden.');
            return $this->_helper->redirector('index');
        }


        // falls das Formular falsch ausgefuellt ist, zurueck!
        if (!$this->_form->isValid($this->_request->getPost())) {
            $this->view->form = $this->_form;
            return $this->render('login');
        }

        $authAdapter = new Zend_Auth_Adapter_DbTable(
                Zend_Db_Table::getDefaultAdapter(), 'users', 'username', 'password'
        );
        $params = $this->_form->getValues();
        $authAdapter->setIdentity($params['username'])
                ->setCredential(md5($params['password']));
        $auth = Zend_Auth::getInstance();
        $authValid = $auth->authenticate($authAdapter)->isValid();

        if (!$authValid) {
            $this->view->form = $this->_form;
            $this->_errorMessenger->addMessage('Falsche Benutzerdaten.');
            return $this->_helper->redirector('login');
        } else {
            $identity = $authAdapter->getResultRowObject();
            //we need some kind of persistent storage
            //we get from zend_Auth::getInstance

            $authStorage = $auth->getStorage();
            //write the identiy in a persinten store, by default we use zend session
            $authStorage->write($identity);

            $this->_redirect('http://mundodosparquinhos.org/');
        }
    }

    public function logoutAction() {
        //all the authentication is now cleared
        Zend_Auth::getInstance()->clearIdentity();
        //redirect to default/index/index
        $this->_redirect('authentication/login');
    }

    /**
     * Create a new User account
     *
     *
     *
     */
    public function registerAction() {
        // Instantiate the registration form model
        $formRegister = new Form_RegisterForm();
        $role = "users";

        $post = $this->_request->getPost();
        // Has the form been submitted?
        if ($post) {
            // If the form data is valid, process it
            if (!$this->view->form->isValid($post)) {

                //$this->view->form->populate($post);
            } elseif ($post['password'] != $post['confirm_pswd']) {
                $this->view->form->confirm_pswd->addError('the passwords are not the same');


                //$this->view->form->populate($post);
            } else {
                // verarbeite Daten

                $values = $this->view->formRegister->getValues();
                $values['username'] = $values['username'];
                $values['email'] = $values['email'];
                $values['password'] = md5($values['password']);
                $values['gender'] = $values['gender'];
               

                // Save the User to the database
                $user = new Application_Model_User($values);
                $user->getMapper()->save($user);

                if (null !== $user->id) {
                    $this->_statusMessenger->addMessage(
                            'Benutzer ' . $user->username . ' gespeichert.'
                    );
                } else {
                    $this->_errorMessenger->addMessage(
                            'Fehler beim Speichern von ' . $user->username
                    );
                }

                // Create a new mail object
                $mail = new Zend_Mail();

                // Set the e-mail from address, to address, and subject
                $mail->setFrom(Zend_Registry::get('config')->email->support);
                $mail->addTo($user->getEmail(), "{$user->getUsername()}");
                $mail->setSubject('mundosdosparquinhos.org: Confirm Your Account');

                // Retrieve the e-mail message text
                include "_email_confirm_address.phtml";

                // Set the e-mail message text
                $mail->setBodyText($email);

                // Send the e-mail
                $mail->send();

                // Set the flash message
                $this->_helper->flashMessenger->addMessage(
                        Zend_Registry::get('config')->messages->register->successful
                );

                // Redirect the user to the home page

                $this->_redirect('/admin/user');
            }
        }
    }

    public function confirmAction() {
        // action body
    }
    public function passwordAction() {
        // action body
    }

}

