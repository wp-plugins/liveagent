<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

    <head>

        <title>PHP HTML Form Processor Class - Contact Form</title>

        <style type="text/css"> body {font-family: tahoma, arial, verdana, sans; font-size: 12px; } </style>

    </head>

    <body>
    
    <p><strong>Quick tip: </strong>You can send all error messages to a single error block if you want!</p>

    <?php

        // require the htmlform class
        require '../class.htmlform.php';

        // instantiate the class
        $form = new HTMLForm('form', 'post');
        
        // add a label to the 'name' control - used in the template file as {controls.label_name}
        $form->add('label', 'label_name', 'name', 'Your name:');

        // add a text control named "name" - used in the template file as {controls.name}
        $obj = & $form->add('text', 'name');
        
        // set the field as mandatory
        $obj->setRule(array('mandatory' => array('e1', 'The above field is required!')));

        // add a label to the 'email' control - used in the template file as {controls.label_email}
        $form->add('label', 'label_email', 'name', 'Your email address:');

        // add a text control named "email" - used in the template file as {controls.email}
        $obj = & $form->add('text', 'email');

        // set the field as mandatory
        // also require a valid email address to be entered
        $obj->setRule(array('mandatory' => array('e2', 'The above field is required!'), 'email' => array('e2', 'Address seems to be invalid!')));

        // add a label to the 'message' control - used in the template file as {controls.label_message}
        $form->add('label', 'label_message', 'message', 'Message:');

        // add a text control named "message" - used in the template file as {controls.message}
        $obj = & $form->add('textarea', 'message', '', array('style' => 'width:200px;height:100px'));

        // set the field as mandatory
        $obj->setRule(array('mandatory' => array('e3', 'The above field is required!')));
        
        $form->add('submit', 'submit', 'Submit');

        // validate the form
        if ($form->validate()) {
        
            // code if form is valid
            print_r('Form is valid. Do your thing (write to db, send emails, whatever) and redirect.');

            die();
            
        }
        
        // display the form with the specified template
        $form->render('forms/contactform.xtpl');

        // IF YOU DO NOT NEED ANY SPECIAL FORMATTINGS IN YOUR FORM YOU CAN ALSO CALL THE RENDER METHOD WITHOUT SPECIFYING A TEMPLATE NAME
        // AND THUS LETTING THE SCRIPT TO AUTOMATICALLY GENERATE OUTPUT AND SAVING YOU OF EXTRA WORK PUT INTO DESIGNING THE FORM'S LAYOUT
        // COMMENT THE LINE ABOVE AND UNCOMMENT THE ONE BELOW TO SEE IT IN ACTION

        //$form->render();

    ?>

    </body>

</html>
