<?php

defined( 'KOOWA' ) or die( 'Restricted Access' );

echo preg_replace( '/\{\{content\}\}/', $body, $layout );