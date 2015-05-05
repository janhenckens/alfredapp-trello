<?php
require_once('workflows.php');
$wf = new Workflows();


$trello_api_key         = '2e0080d27d59f72fe18893b8c19eebc2';
$trello_api_endpoint    = 'https://api.trello.com/1';
$data                   = explode( ";", $argv[1] );
$trello_member_token    = $wf->read('user.ini');


$orig = $data['0'];
$text = urlencode( $orig );
$json = $wf->request( 'https://api.trello.com/1/search?key=2e0080d27d59f72fe18893b8c19eebc2&query=' . $text .'&token=' . $trello_member_token->key);
$json = json_decode( utf8_encode($json) );
$data = $json->boards;

$int= 1;
// $uid, $arg, $title, $sub, $icon, $valid='yes', $auto=null, $type=null

foreach( $data as $results ):
    $wf->result( 'alfredtrello' . $int, $results->id, $results->name, '', 'icon.png' );
    $int++;
endforeach;


echo $wf->toxml();

?>