<?php

$cfg->setVar('page_title', 'Contact Us');

$kForm = new kForm($_SERVER['REQUEST_URI'], 'post');
$kForm->heading = '{k_this_pg_contents}';
$kForm->addText('Email Address', 'f_email', '', 50, 255);
$kForm->addTextarea('Comments', 'f_comments');
$kForm->addCaptcha(DIR_BASE . 'captcha.php');

$kForm->addHidden('a');

$kForm->addRule('captcha', 'captcha');
$kForm->addRule('f_email', 'email');
$kForm->addRule('f_comments', 'required');
            
$kForm->title = 'Contact Us';
$kForm->addSubmit('Send Comment');


if((!empty($_REQUEST['a'])) && ($_REQUEST['a']) == 'comment')
{
    if(!$kForm->validate($_POST))
    {
        $kForm->heading = '<div class="msg1">Please fix any errors and submit again.</div>';
        $kForm->renderForm($_POST);    
    }
    else 
    {
        $msg_fields = array('f_comments' => stripslashes($_REQUEST['f_comments']), 'f_email' => $_REQUEST['f_email']);
        
        $message = getMessage('msg_email_contactnotify', $msg_fields);
        $email_subject = $message['title'];
        $email_message = $message['text'];
        mail($cfg->getVar('site_email'), $email_subject, $email_message, 'From:'.$_REQUEST['f_email']);
        
        $kForm->heading = '<div class="msg1">Thank you, your message has been sent.</div>';
        $kForm->renderForm(array('a' => 'comment'));
    }
}
else
{
    $kForm->renderForm(array('a' => 'comment'));
}

?>