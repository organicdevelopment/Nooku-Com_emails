<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markhead
 * Date: 06/06/2012
 * Time: 15:49
 * To change this template use File | Settings | File Templates.
 */

class ComEmailsControllerEmail extends ComEmailsControllerDefault
{

    protected function _initialize( KConfig $config )
    {
        parent::_initialize( $config );
        $config->behaviors = array_diff( KConfig::unbox( $config->behaviors ), array( 'executable' ) );
    }

}
