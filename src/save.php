<?php
require_once('workflows.php');
$wf = new Workflows();

$data = explode( ";", $argv[1] );

$key = $data['0'];
$data = array( 'key' => $key );
$save = $wf->write($data, 'user.ini');

$trello_api_key         = '2e0080d27d59f72fe18893b8c19eebc2';
$trello_member_token    = $wf->read('user.ini');

$text = urlencode( $orig );
$json = $wf->request( 'https://api.trello.com/1/members/me/boards?key=' . $trello_api_key .'&token=' . $trello_member_token->key);
$data = json_decode(utf8_encode($json));
$boards = array();

foreach($data as $key => $value)
    {
    foreach($value as $data => $user_data)
        {
            $boards[$value->name]['id'] = $value->id;
            $boards[$value->name]['name'] = $value->name;
            $boards[$value->name]['url'] = $value->url;
        }
    };

$save = $wf->write($boards, 'boards.json');

echo 'Saved!';

?>