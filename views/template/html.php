<?php

defined( 'KOOWA' ) or die( 'Restricted Access' );

class ComEmailsViewTemplateHtml extends ComEmailsViewTemplateView
{

    public function display()
    {
        $html = parent::display();

        $html = preg_replace( '/\{\{content\}\}/', $this->body, $html );
        $styles = $this->css;

		//Emogrify
        $html = $this->getService( 'lib://site/emogrifier.helper',
            array(
                'css'   => $styles,
                'html'  => $html
            )
        )->emogrify();

        $this->output = $html;

        return $html;
    }

}
