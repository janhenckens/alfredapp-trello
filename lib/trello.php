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
        $results = array();
    }

    /**
     * Handles the Trello Search function (anything starting with 'ts')
     */
    public function search($board, $query) {
        $board = $this->get_board($board);
        $cards = $this->get_cards($board->id);
        foreach ($cards as $card ) {
            if(strripos($card['name'], $query) !== false) {
                $this->save_cards($results, $card);
                // Add the list name before the card title
                $results[$card['name']]['name'] = '[' . $board->lists->$card['idList']->name . '] ' . $results[$card['name']]['name'];
            }
        }
        return $this->parse_results($results);
    }

    private function get_cards($board) {
        $_endpoint_url = 'boards/' . $board . '/cards?fields=name,idList,url,subscribed,listID,name,dateLastActivity,url,shortLink';
        $cards = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $this->token ) );
        return $cards;
    }

    private function get_board($command) {
        $data = $this->workflow->read( 'boards.json' );
        foreach ($data as $board ) {
            if(strripos($board->name, $command) !== false) {
                return $board;
            }
        }
    }
    private function get_boards($command) {
        $data = $this->workflow->read( 'boards.json' );
        $int= 1;
        foreach ($data as $board ) {
            if(strripos($board->name, $command) !== false) {
                    $results[$int]['id'] = $board->id;
                    $results[$int]['url'] = $board->url;
                    $results[$int]['name'] = $board->name;
                    $results[$int]['icon'] = "./assets/board.png";
                    $int++;
            }
        }
        return $results;
    }

    public function boards($command, $input=null) {
        if( isset($input) && $input === "me") {
            $board = $this->get_board($command);
            $_endpoint_url = 'boards/' . $board->id . '/cards?fields=name,idList,url,subscribed,name,dateLastActivity,url,shortLink';
            $cards = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $this->token ) );
            unset($results);
            foreach($cards as $card) {
                if($card['subscribed'] === true) {
                        $this->save_cards($results, $card);
                        $results[$card['name']]['name'] = '[' . $board->lists->$card['idList']->name . '] ' . $results[$card['name']]['name'];
                }
            }
            return $this->parse_results($results);
        }
        $results = $this->get_boards($command);
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
        foreach ($data as $result ) {
            if(strripos($result->name, $board) !== false) {
                $_endpoint_url = 'boards/' . $result->id . '/lists?&fields=name&cards=open&card_fields=name&card_fields=url,subscribed,dateLastActivity,url,shortLink&';
                $data = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $this->token ) );
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
        $board = $this->get_board($board);
        $_endpoint_url = 'boards/' . $board->id . '/cards?fields=name,url,shortUrl,dateLastActivity,url,shortLink';
        $data = $this->TrelloClient->get( $_endpoint_url, array( 'key' => $this->trello_api_key ,'token' => $this->token ) );
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

    private function save_cards(&$results, $card) {
        $results[$card['name']]['name'] = $card['name'];
        $results[$card['name']]['id'] = $card['id'];
        $results[$card['name']]['url'] = $card['url'];
        $results[$card['name']]['icon'] = "./assets/card.png";
        $results[$card['name']]['date'] = strtotime($card['dateLastActivity']);
        return $results;
    }

    /**
     * Add the results array to parsable structure before we pass it back to Alfred
     */
    private function parse_results($results) {
        ksort($results, SORT_NATURAL | SORT_FLAG_CASE);
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

    /**
     * Runs the fetch() function to update the locally cached boards from the Trello API
     */
    public function refresh()
    {
        $results = $this->fetch();
    }

    /**
     * Save the users authentication key to settings.plist and run fetch() to pre-fetch the user's boards.
     */
    public function save($input) {
        if(!empty($input) && strlen($input) == 64) {
            $userdata = array('trello_user_token' => $input);
            $this->workflow->set($userdata, 'settings.plist');
            $this->fetch();
            return;
        }
    }

    /**
     * Fetches all boards for the current user (based on the api key) and all lists for each on of the boards.
     */
    private function fetch() {

        $boards = $this->TrelloClient->get( 'member/' . $this->trello_user_id . '/boards', array( 'token' => $this->token ) );
        // Get all boards for the current membmer ('me')
        foreach($boards as $key => $value) {

            $results[$value['name']]['id'] = $value['id'];
            $results[$value['name']]['name'] = $value['name'];
            $results[$value['name']]['url'] = $value['url'];

            // Get all lists the current board.
            // Other data per board can be added to be stored here as well.
            $lists = $this->TrelloClient->get('boards/' . $value['id'] . '?lists=open&list_fields=name&fields=name,desc', array( 'key' => $this->trello_api_key ,'token' => $this->token ) );
            // Loop through all lists and save them to the results.
            foreach($lists['lists'] as $list) {
                $results[$value['name']]['lists'][$list['id']]['id'] = $list['id'];
                $results[$value['name']]['lists'][$list['id']]['name'] = $list['name'];
            }
        }
        // Save the results data to a json file so we can get it from 'cache' later.
        $save = $this->workflow->write($results, 'boards.json');
    }

}