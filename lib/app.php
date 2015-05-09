<?php

class App extends Base {

    protected $trello_api_key = "2e0080d27d59f72fe18893b8c19eebc2";
    public $commands = array('setup', 'save', 'reset', 'sync');
    public $command = null;

    public function __construct()
    {
        $this->Setup  = new Setup( $this );
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
            $request[0] = trim(str_replace( ':' . $this->command, '', $request[0] ));

            if (empty( $request[0] ))
            {
                array_shift( $request );
            }

            foreach ($request as $_request)
            {
                $this->input[] = trim( $_request );
                $this->query .= trim( $_request ) . ' ';
            }
            $this->query = trim( $this->query );
        }
    }

    public function validateCommand( $command=null )
    {
        if (empty( $command ))
        {
            $command = $this->command;
        }

        // Still if no command sent, show the default option menu
        if (empty( $command ))
        {
            $this->showMainMenu();
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
        if (method_exists( $this, $this->command ))
        {
            $results = call_user_func_array( array( $this, $this->command), array( $this->input ) );
        }
        else
        {
            echo "\r\n";
            echo "No method for that command...";
            echo "\r\n";
            die;
        }
        return $results;
    }

    public function setup( $input=null )
    {
        $results = $this->Setup->make( $input );
        return $results;
    }
}