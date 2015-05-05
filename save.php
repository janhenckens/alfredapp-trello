<?php
require_once('../workflows.php');
$wf = new Workflows();

$data = explode( ";", $argv[1] );

$key = $data['0'];
$data = array( 'key' => $key );
$save = $wf->write($data, '../user.ini');

echo "saved!";

?>