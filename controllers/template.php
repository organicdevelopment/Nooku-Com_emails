<?php

defined( 'KOOWA' ) or die( 'Restricted Access' );

class ComEmailsControllerTemplate extends ComEmailsControllerResource
{

    /**
     * Uncomment this and run index.php?option=com_emails&view=template&action=test to test emailing
     *
     * Overridden actionGet to allow for executing the additems action via a get request
     * @param KCommandContext $context
     *
    protected function _actionGet( KCommandContext $context )
    {
        $request = clone( $this->getRequest() );
        $action = $request->action;

        // Set some allowed actions
        $allowed = array(
            'test'
        );

        if( in_array( $action, $allowed ) ) {
            unset( $request->option );
            unset( $request->view );
            unset( $request->layout );
            $request->_token = JUtility::getToken();
            $context->data = $request;
            $this->execute( $action, $context );
        }

        return parent::_actionGet( $context );
    }

    public function _actionTest( KCommandContext $context )
    {
        $subscription = $this->getService( 'com://site/subscriptions.model.subscriptions' )
            ->id( 1 )
            ->getItem();

        $data = new KCommandContext(
            array(
                'data'  => array(
                    'fields'        => array(
                        'join_text'                 => $subscription->plan->join_text,
                        'name'                      => $subscription->user->name,
                        'network_name'              => $subscription->plan->network->name,
                        'subscription_renewal_date' => date( 'jS F Y' )
                    ),
                    'template_id'   => 1,
                    'recipient'     => $subscription->user->email
                )
            )
        );

        return $this->getService( 'com://site/emails.controller.template' )
            ->send( $data );
    }
    */

    /**
     * Specialised display function.
     *
     * @param	KCommandContext	A command context object
     * @return 	string|false 	The rendered output of the view or false if something went wrong
     */
    protected function _actionGet(KCommandContext $context)
    {
        $context->setError(new KControllerException(
            JText::_( 'EMAILS_METHOD_NOT_ALLOWED' ), KHttpResponse::METHOD_NOT_ALLOWED
        ) );
    }

    /**
     * Send emails
     *
     * @param KCommandContext $context
     * @return bool
     */
    public function _actionSend( KCommandContext $context )
    {
        // Get the email template details
        $template = $this->getModel()
            ->id( $context->data->template_id )
            ->component( $context->data->component )
            ->type( $context->data->type )
            ->getItem();

		$html = (isset($context->data->html)) ? $context->data->html : true;

        // Set the mailer
        $mailer =& JFactory::getMailer();
        $mailer->isHTML( $html );

        // Set the sender
        if( $template->from_name && $template->from_email )
        {
           $mailer->setSender(
                array(
                    $template->from_email,
                    $template->from_name
                )
            );
        }

        else if($template->from_email)
        {
            $mailer->setSender($template->from_email);
        }

        // Set the email subject
        $subject = $template->subject;
        preg_match_all( '/\{\{([^\}]*)\}\}/', $subject, $matches_subject );
        foreach( $matches_subject[0] as $i => $match )
        {
            $subject = preg_replace( '/' . preg_quote( $match ) . '/', $context->data->fields->{$matches_subject[1][$i]}, $subject );
        }
        $mailer->setSubject( $subject );

        // Pass to the appropriate view
        $body = $this->render(
            array(
                'type' => 'html',
                'css' => $context->data->css,
                'template_id' => $context->data->template_id,
                'fields' => $context->data->fields
            )
        );

		$body_alt = $this->render(
            array(
                'type' => 'text',
                'template_id' => $context->data->template_id,
                'fields' => $context->data->fields
            )
        );

		// If over ridden to plain text mode, set the body plain text version
		if(!$html) {
			$body = $body_alt;
			$body_alt = ""; // For some reason plain text only works if body alt is empty?
		}

        $mailer->setBody( $body );
        $mailer->AltBody = $body_alt;

        // Send to each recipient
        $recipients = (array) KConfig::unbox($context->data->recipients);
        if($context->data->recipient) $recipients[] = $context->data->recipient;

        $statuses = array();
        $status = true;
	    $errors = array();

        foreach( $recipients as $recipient )
        {
            $mailer->clearAllRecipients();
            $mailer->addRecipient( $recipient );
            $sent = $mailer->Send();
            $statuses[] = array(
                'email'     => $recipient,
                'status'    => $sent,
                'error'     => $mailer->ErrorInfo
            );

            if(!$sent) $status = false;

	        if($context->log){
	            // Log the email
	            $context = $this->getCommandContext();
	            $context->data = array(
	                'date_sent' => date( 'Y-m-d H:i:s' ),
	                'from'      => $template->from_email,
	                'to'        => $recipient,
                    'subject'   => $subject,
	                'html'      => $body,
	                'text'      => $body_alt,
	                'sent'      => $sent,
	                'message'   => $mailer->ErrorInfo
	            );
	            $this->getService( 'com://site/emails.controller.email' )
	                ->add( $context );
	        }

	        if(!$sent && $mailer->ErrorInfo) $errors[] = $mailer->ErrorInfo;
	        $mailer->ErrorInfo = null;
        }

        $context->status = $status;
        $context->sent = $statuses;

	    if(!$status){
		    $context->setError(new KControllerException(
			    'Error sending mail: '.implode(', ',$errors)
		    ));
	    }

        return $mailer;
    }


	protected function _actionRender(KCommandContext $context)
	{
		$type = $context->data->get('type','html');
		if(!in_array($type, array('text','html'))) $type = 'html';

		// Pass to the appropriate view
		$this->_view = 'template';
		$this->getRequest()->format = $type;
		$view_html = clone( $this->getView() );

		if($type == 'html'){
			$view_html->css = $context->data->css;
		}

		return $view_html
			->id( $context->data->template_id )
            ->component( $context->data->component )
            ->type( $context->data->type )
			->set( 'fields', $context->data->fields )
			->display();
	}

}
