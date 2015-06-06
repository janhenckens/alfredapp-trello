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

    public function refresh() {
        $this->fetch();
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
        ksort($results, SORT_NATURAL | SORT_FLAG_CASE);
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

    public function tickets($query) {
        $board = substr($query, 0, 6);
        $w = new Workflows();
        $data = $w->read( 'boards.json' );
        $token = $w->get( 'trello_user_token', 'settings.plist' );
        foreach ($data as $result ) {
            if (strripos($result->name, $board) !== false) {
                $TrelloClient = new Client( $this->trello_api_key );
                $w = new Workflows();
                $results = array();
                $token = $w->get( 'trello_user_token', 'settings.plist' );
                $_endpoint_url = 'boards/' . $result->id . '/cards?fields=name,url,shortUrl';
                // https://api.trello.com/1/boards/4eea4ffc91e31d1746000046/cards?fields=name,idList,url&key=[application_key]&token=[optional_auth_token]
                $data = $TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $token ) );
                foreach($data as $card) {
                    $number = substr($query, strrpos($query, '-') + 1);
                    $id = substr($card['url'], strrpos($card['url'], '/') + 1);
                    $ticket = explode("-", id, 2);
                    if ( $ticket['0'] = $number) {
                        $results[$card['name']]['name'] = $card['name'];
                        $results[$card['name']]['id'] = $card['id'];
                        $results[$card['name']]['url'] = $card['url'];
                        $results[$card['name']]['icon'] = "./assets/card.png";
                        $w = $this->parse_results($results);
                        return $w;
                    }
                }
            }
        }
    }

    public function parse_results($results) {
        $results = array_filter($results);
        $w = new Workflows();
        if(empty($results)) {
            $w->result('alfredtrello' . $int, '', 'No boards found', "Try a different search term...", $result['icon']);
        }
        else {
            foreach ($results as $result) {
                $int = 1;
                // $uid, $arg, $title, $sub, $icon, $valid='yes', $auto=null, $type=null
                $w->result('alfredtrello' . $int, $result['url'], $result['name'], $result['url'], $result['icon']);
                $int++;
            }
        }
        return $w;
    }
}