<?php

class Setup {

    public function __contruct() {
    }

    public function save($input) {
        $w = new Workflows();
        if(!empty($input) && strlen($input) == 64) {
            var_dump($input);
            $userdata = array('trello_user_token' => $input['1']);
            $w->set($userdata, 'settings.plist');
        }
    }
}