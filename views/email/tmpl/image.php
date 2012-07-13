<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markhead
 * Date: 06/06/2012
 * Time: 16:05
 * To change this template use File | Settings | File Templates.
 */

$email->read = 1;
$email->save();

header( 'Content-Type: image/gif' );
echo file_get_contents( dirname( __FILE__ ) . '/../../../assets/images/blank.gif' );
exit;
