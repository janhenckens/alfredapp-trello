<?php
/**
 * Created by PhpStorm.
 * User: jhenckens
 * Date: 09/05/15
 * Time: 12:06
 */
use Trello\Client;

class Trello extends App {

    public function __construct() {

    }

    public function boards() {
        $TrelloClient = new Client( $this->trello_api_key );
        $w = new Workflows();
        $token = $w->get( 'trello_user_token', 'settings.plist' );
        $trello_user_id = 'me';
        $_endpoint_url = 'member/' . $trello_user_id . '/boards/';
        $boards = $TrelloClient->get( $_endpoint_url, array( 'token' => $token ) );

        foreach($boards as $key => $value)
        {
            foreach($value as $data => $user_data)
            {
                $boards[$value->name]['id'] = $value->id;
                $boards[$value->name]['name'] = $value->name;
                $boards[$value->name]['url'] = $value->url;
            }
        };
        $save = $w->write($boards, 'boards.json');
    }
}