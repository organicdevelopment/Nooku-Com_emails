<?php

defined( 'KOOWA' ) or die( 'Restricted Access' );

// Initialise com_base
KService::get( 'com://site/base.initialize' );

// Dispatch using a default view if not set
echo KService::get( 'com://site/emails.dispatcher' )
    ->dispatch();