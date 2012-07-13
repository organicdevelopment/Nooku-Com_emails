
Nooku-Com_emails
================

3 tables:

jos_emails_tempaltes <- these are your content templates, body copy
jos_emails_layouts <- overal template layout
jos_emails_emails <- tracking table contains emails sent

Invoke using the following:

// Build KCommand Context for email controller
$context = new KCommandContext(
	array(
		'data' => array(
			'fields' => array(
				'name' => 'jon doe',
				'age' => '25'
			),
			'component' 	=> 'com_foo', //component identifier, references the component column in the templates table
			'type' 		=> 'bar', //type identifier, references the type column in templates table
			'recipients' 	=> array ( 'jon@doe.com' )
		)
	)
);

// Get email controller and send the email
$this->getService( 'com://site/emails.controller.template' )->send( $context );