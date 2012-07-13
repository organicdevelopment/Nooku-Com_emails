<?php

defined( 'KOOWA' ) or die( 'Restricted Access' );

class ComEmailsDispatcher extends ComDefaultDispatcher
{

    protected function _initialize( KConfig $config )
    {
        // Set the default view
        $config->append( array(
            'controller' => 'email'
        ) );

        parent::_initialize( $config );
    }

}
