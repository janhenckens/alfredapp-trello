<?php

class App {

    protected $trello_api_key = "2e0080d27d59f72fe18893b8c19eebc2";
    protected $trello_user_id = 'me';
    public $commands = array('setup', 'save', 'refresh', 'sync', 'search');
    public $command = null;

    public function __construct()
    {
        $this->trello = new Trello ( $this );
    }

    public function request($query) {
        $this->checkCommand($query);

        $this->validateCommand();
        $results = $this->routeRequest();

        echo $results;
    }

    public function checkCommand($request) {
        list($this->command) = explode( ' ', (empty( $request[1] )) ? null : str_replace( ':', '', $request[1] ));

        array_splice($request, 0, 1);

        if (!empty( $request ))
        {
            $request = explode(' ',trim($request['0']));
            $this->query = $request['0'];
            $this->input = $request['1'];
            $this->optional = $request['2'];
        }
    }

    public function validateCommand( $command=null )
    {
        $command = $this->query;
        if (empty( $command ))
        {
            $command = $this->command;
        }

        // Still if no command sent, show the default option menu
        if (empty( $command ))
        {

        }

        // If command is set and not valid, throw an error
        if (!in_array( $command, $this->commands ))
        {
            return false;
        }
    }

    public function routeRequest()
    {
        // Route command to appropriate method
        if( method_exists( $this->trello, $this->command ) ) {
            $results = call_user_func_array( array( $this->trello, $this->command), array( $this->input ) );
        }
        // Board + column withouth "me"
        elseif( (isset($this->query) && isset($this->input) && "me" !== $this->input && "me" !== $this->optional ) ) {
            $results = $this->trello->cards($this->query, $this->input);
        }
        // Board + column + me
        elseif( isset($this->query) && isset($this->input) && isset($this->optional) && $this->optional === "me" ){
            $results = $this->trello->cards($this->query, $this->input, $this->optional);
        }
        // Tickets
        elseif( isset($this->query) && strpos($this->query, '-') ) {
            $results = $this->trello->tickets($this->query);
        }
        // Board + me === 'all my cards on this board'
        elseif( isset($this->query) && isset($this->input) && $this->input === "me" ) {
            $results = $this->trello->boards($this->command, $this->input);
        } 
        // Board without "me" === basic board query
        elseif ( isset($this->query) && !isset($this->input) ) {
            $results = $this->trello->boards($this->command);
        } 
        // If command not found or pattern not matched
        else {
            $this->workflow = new Workflows();
            $this->workflow->result('alfredtrello' . $int, '', 'Command not found', "", $result['icon']);
            return $this->workflow->toxml();
        }
        return $results->toxml();
    }

    public function search() {
        $results = $this->trello->search( $input );
        return $results;
    }

}