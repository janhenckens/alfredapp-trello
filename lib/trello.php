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
        $this->workflow = new Workflows();
        $this->TrelloClient = new Client( $this->trello_api_key );
        $this->token = $this->workflow->get( 'trello_user_token', 'settings.plist' );
        date_default_timezone_set('Europe/Brussels');
    }

    public function search($board, $query) {
        $board_id = $this->get_board_id($board);
        $_endpoint_url = 'boards/' . $board_id . '/cards?fields=name,idList,url,subscribed,name';
        $cards = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $this->token ) );
        foreach ($cards as $card ) {
            if(strripos($card['name'], $query) !== false) {
                $this->save_cards($results, $card);
            }
        }
        return $this->parse_results($results);
    }

    private function get_board_id($command) {
        $data = $this->workflow->read( 'boards.json' );
        $results = array();
        foreach ($data as $board ) {
            if(strripos($board->name, $command) !== false) {
                $board = $board->id;
                return $board;
            }
        }
    }

    public function boards($command, $input=null) {
        $data = $this->workflow->read( 'boards.json' );
        $results = array();
        foreach ($data as $board ) {
            if(strripos($board->name, $command) !== false) {
                if( isset($input) && $input === "me") {                    
                    $token = $this->workflow->get( 'trello_user_token', 'settings.plist' );
                    $_endpoint_url = 'boards/' . $board->id . '/cards?fields=name,idList,url,subscribed,name';
                    $cards = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $token ) );
                    foreach($cards as $card) {
                        if($card['subscribed'] === true) {
                                $this->save_cards($results, $card);
                        }
                    }
                    return $this->parse_results($results);
                } else {
                    $int= 1;
                    $results[$board->name]['id'] = $board->id;
                    $results[$board->name]['url'] = $board->url;
                    $results[$board->name]['name'] = $board->name;
                    $results[$board->name]['icon'] = "./assets/board.png";
                    $int++;
                }
            }
        }
        ksort($results, SORT_NATURAL | SORT_FLAG_CASE);
        return $this->parse_results($results);
    }

    function cmp($a, $b) {
        if ($a['date'] == $b['date']) {
            return 0;
        }
        return ($a['date'] < $b['date']) ? -1 : 1;
    }

    public function cards($board, $query, $optional=null) {
        $data = $this->workflow->read( 'boards.json' );
        $results = array();
        foreach ($data as $result ) {
            if(strripos($result->name, $board) !== false) {
                $results = array();
                $token = $this->workflow->get( 'trello_user_token', 'settings.plist' );
                $_endpoint_url = 'boards/' . $result->id . '/lists?&fields=name&cards=open&card_fields=name&card_fields=url,subscribed,dateLastActivity&';
                $data = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $token ) );
                foreach($data as $list) {
                    if(strtolower(str_replace(" ", "", $list['name'])) == strtolower($query)) {
                        foreach($list['cards'] as $card) {
                            if ($optional == "me") {
                                if($card['subscribed'] === true) {
                                    $this->save_cards($results, $card);
                                }
                            } else {
                                $this->save_cards($results, $card);
                            }
                        }
                        uasort($results, array($this, 'cmp'));
                        return $this->parse_results($results);
                    }
                }

            }
        }

    }

    public function tickets($query) {
        $board = strrpos($query, '-');
        $board = substr($query, 0, $board);
        $data = $this->workflow->read( 'boards.json' );
        foreach ($data as $result ) {
            if (strripos($result->name, $board) !== false) {
                $results = array();
                $token = $this->workflow->get( 'trello_user_token', 'settings.plist' );
                $_endpoint_url = 'boards/' . $result->id . '/cards?fields=name,url,shortUrl';
                // https://api.trello.com/1/boards/4eea4ffc91e31d1746000046/cards?fields=name,idList,url&key=[application_key]&token=[optional_auth_token]
                $data = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $token ) );
                foreach($data as $card) {
                    $number = substr($query, strrpos($query, '-') + 1);
                    $cardid = substr($card['url'], strrpos($card['url'], '/') + 1);
                    $ticket = explode("-", $cardid, 2);
                    if ( $ticket['0'] == $number) {
                        $this->save_cards($results, $card);
                        return $this->parse_results($results);
                    }
                }
            }
        }
    }

    private function save_cards(&$results, $card) {
        $results[$card['name']]['name'] = $card['name'];
        $results[$card['name']]['id'] = $card['id'];
        $results[$card['name']]['url'] = $card['url'];
        $results[$card['name']]['icon'] = "./assets/card.png";
        $results[$card['name']]['date'] = strtotime($card['dateLastActivity']);
        return $results;
    }

    private function parse_results($results) {
        $results = array_filter($results);
        if(empty($results)) {
            $this->workflow->result('alfredtrello' . $int, '', 'No boards found', "Try a different search term...", $result['icon']);
        }
        else {
            foreach ($results as $result) {
                $int = 1;
                // $uid, $arg, $title, $sub, $icon, $valid='yes', $auto=null, $type=null
                $this->workflow->result('alfredtrello' . $int, $result['url'], $result['name'], $result['url'], $result['icon']);
                $int++;
            }
        }
        return $this->workflow;
    }

    public function refresh()
    {
        $results = $this->fetch();
    }

    public function save($input) {
        if(!empty($input) && strlen($input) == 64) {
            $userdata = array('trello_user_token' => $input);
            $this->workflow->set($userdata, 'settings.plist');
            $this->fetch();
            return;
        }
    }

    private function fetch() {
        $token = $this->workflow->get( 'trello_user_token', 'settings.plist' );
        $_endpoint_url = 'member/' . $this->trello_user_id . '/boards/';
        $boards = $this->TrelloClient->get( $_endpoint_url, array( 'token' => $token ) );

        foreach($boards as $key => $value)
        {
            foreach($value as $data => $user_data)
            {
                $boards[$value->name]['id'] = $value->id;
                $boards[$value->name]['name'] = $value->name;
                $boards[$value->name]['url'] = $value->url;
            }
        };
        $save = $this->workflow->write($boards, 'boards.json');
    }

}