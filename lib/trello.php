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

    public function fetch() {
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

    public function boards($command) {
        $w = new Workflows();
        $data = $w->read( 'boards.json' );
        $results = array();
        foreach ($data as $board ) {

            if(strripos($board->name, $command) !== false) {
                $int= 1;
                $results[$board->name]['id'] = $board->id;
                $results[$board->name]['url'] = $board->url;
                $results[$board->name]['name'] = $board->name;
                $int++;
            }
        }
        $w = $this->parse_results($results);
        return $w;

    }

    public function cards($board, $list) {
        

    }

    public function parse_results($results) {

        $w = new Workflows();
        foreach($results as $result) {
            $int= 1;
            // $uid, $arg, $title, $sub, $icon, $valid='yes', $auto=null, $type=null
            $w->result( 'alfredtrello' . $int, $result['url'], $result['name'], $result['url'], './assets/board.png' );
            $int++;
        }
        return $w;
    }
}