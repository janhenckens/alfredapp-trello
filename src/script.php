<?php
require_once('workflows.php');
$wf = new Workflows();


$trello_api_key         = '2e0080d27d59f72fe18893b8c19eebc2';
$trello_api_endpoint    = 'https://api.trello.com/1';
$data                   = explode( ";", $argv[1] );
$trello_member_token    = $wf->read('user.ini');

$orig = $data['0'];
$text = urlencode( $orig );

$myboards = $wf->read('boards.json');
$myboards = (array) $myboards;

foreach ($myboards as $board ) {
    if(strripos($board->name, $text) !== false) {
        $int= 1;
        // $uid, $arg, $title, $sub, $icon, $valid='yes', $auto=null, $type=null
        $wf->result( 'alfredtrello' . $int, $board->url, $board->name, '', 'board.png' );
        $int++;
    }
}

echo $wf->toxml();

?>