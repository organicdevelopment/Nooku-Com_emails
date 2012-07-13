<?php

    defined( 'KOOWA' ) or die( 'Restricted Access' );

class ComEmailsViewTemplateView extends ComEmailsViewHtml
{
    protected function _initialize( KConfig $config )
    {
        parent::_initialize( $config );
        $config->template_filters = array_diff( KConfig::unbox( $config->template_filters ), array( 'style' ) );
    }

    public function display() {
        // Get the email template details
        $this->template = $this->getModel()
            ->id( $this->id )
            ->getItem();

        // Get the template layout and inject the mail body content
        $this->template_layout = $this->getService( 'com://site/emails.model.layouts' )
            ->id( $this->template->layout_id )
            ->getItem();

	    //Set the format
        $this->format = $this->getIdentifier()->name;

        // Set the email html/text body for the content part of the email
        $this->body = $this->template->{'body_'.$this->format};

	    //Set the layout
        $this->layout = $this->template_layout->{$this->format};

	    //Run striptags on body html
	    if( $this->format == 'text' && !$this->template->body_text )
	    {
		    $this->body = strip_tags( $this->template->body_html );
	    }

        // Merge any fields into the email html body
        $this->body = $this->mergeFields($this->fields, $this->body);

	    //And run merge fields again incase any of the merged fields contain merge fields themselves
        $this->body = $this->mergeFields($this->fields, $this->body);

		//Render
	    $output = parent::display();

	    //Run striptags on body html and remove lots of spaces
	    if( $this->format == 'text')
	    {
		    $output = strip_tags( $output );
		    $body = explode("\n", $output );
		    $newlines = 0;
		    $lines = array();
		    foreach($body AS $i => $b){
			    $b = trim($b);
			    if(strlen($b) == 0){
				    $newlines++;
			    }else{
				    $newlines = 0;
			    }
			    if($newlines <= 2) $lines[] = $b;
		    }
		    $output = implode("\n", $lines);
	    }

	    //Convert links/images to absolute paths
        $output = preg_replace_callback( '/(src=|href=)([\'|"])([^\'|"]*)([\'|"])/', array($this, '_relToAbs' ), $output);
        $this->output = $output;

        return $output;
    }


    public function _relToAbs($match)
    {
        $url = $match[3];

        if( !preg_match( '/#/', $url ) && !preg_match( '/^mailto:/', $url) )
        {

            $uri = $this->getService( 'koowa:http.url', array( 'url' => $url ) );

            if( !$uri->host )
            {
                $uri->host = (string) KRequest::url()->host;
            }

            if( !$uri->scheme )
            {
                $uri->scheme = (string) KRequest::url()->scheme;
            }

            $url = (string) $uri;
        }

        return $match[1] . $match[2] . $url . $match[4];
    }

	public function mergeFields($fields, $body)
	{
		// Merge any fields into the email html body
		if(!$fields instanceof KConfig) $fields = new KConfig((array)$fields);
		preg_match_all( '/\{\{([^\}]*)\}\}/', $body, $matches );
		foreach( $matches[0] as $i => $match )
		{
			$body = preg_replace( '/' . preg_quote( $match ) . '/', $this->fields->get($matches[1][$i].'_'.$this->format, $this->fields->get($matches[1][$i])), $body );
		}

		return $body;
	}
}
