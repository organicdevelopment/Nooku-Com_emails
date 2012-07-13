<?php

defined('KOOWA') or die('Restricted Access');

class ComEmailsDatabaseTableEmails extends KDatabaseTableDefault {

    protected function _initialize( KConfig $config )
    {
        // Set some column filters
        $config->append(
            array(
                'filters' => array(
                    'html'   => array( 'html', 'tidy' )
                )
            )
        );

        parent::_initialize( $config );
    }

}
