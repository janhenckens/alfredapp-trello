<?php

class Setup extends App {

    public function __contruct() {
    }

    public function save($input) {
        $w = new Workflows();
        if(!empty($input) && strlen($input) == 64) {
            $userdata = array('trello_user_token' => $input);
            $w->set($userdata, 'settings.plist');
            $this->trello->fetch();
        }
    }


}