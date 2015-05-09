<?php
/**
 * Created by PhpStorm.
 * User: jhenckens
 * Date: 08/05/15
 * Time: 21:31
 */
require_once ( 'lib/bootstrap.php' );

$app = new App();
$app->request($argv);