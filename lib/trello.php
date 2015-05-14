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
        $_endpoint_url = 'member/' . $this->trello_user_id . '/boards/';
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
                $results[$board->name]['icon'] = "./assets/board.png";
                $int++;
            }
        }
        $w = $this->parse_results($results);
        return $w;

    }

    function cmp($a, $b) {
        if ($a['date'] == $b['date']) {
            return 0;
        }
        return ($a['date'] < $b['date']) ? -1 : 1;
    }

    public function cards($board, $query) {
        $w = new Workflows();
        $data = $w->read( 'boards.json' );
        $results = array();
        foreach ($data as $result ) {
            if(strripos($result->name, $board) !== false) {
                $TrelloClient = new Client( $this->trello_api_key );
                $w = new Workflows();
                $results = array();
                date_default_timezone_set('Europe/Brussels');
                $token = $w->get( 'trello_user_token', 'settings.plist' );
                $_endpoint_url = 'boards/' . $result->id . '/lists?&fields=name&cards=open&card_fields=name&card_fields=url,subscribed,dateLastActivity&';
                $data = $TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $token ) );
                foreach($data as $list) {
                    if(strtolower(str_replace(" ", "", $list['name'])) == strtolower($query)) {
                        foreach($list['cards'] as $card) {
                            if ($card['subscribed'] == true) {
                                $results[$card['name']]['name'] = $card['name'];
                                $results[$card['name']]['id'] = $card['id'];
                                $results[$card['name']]['url'] = $card['url'];
                                $results[$card['name']]['icon'] = "./assets/card.png";
                                $results[$card['name']]['date'] = strtotime($card['dateLastActivity']);
                            }
                        }
                        uasort($results, array($this, 'cmp'));
                        $w = $this->parse_results($results);
                        return $w;
                    }
                }

            }
        }

    }

    public function parse_results($results) {
        $w = new Workflows();
        foreach($results as $result) {
            $int= 1;
            // $uid, $arg, $title, $sub, $icon, $valid='yes', $auto=null, $type=null
            $w->result( 'alfredtrello' . $int, $result['url'], $result['name'], $result['url'], $result['icon'] );
            $int++;
        }
        return $w;
    }
}