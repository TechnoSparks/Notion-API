<?php
namespace Notion;
class Notion {
    var $token = null;
    var $current_database = null;
    const api = "https://api.notion.com/v1/";

    function __construct($token = null, $current_database = null) {
        // a token is required
        if(is_null($token) || empty($token) || !isset($token)) { throw new Exception('A token is required'); return; }
        $this->token = $token;
        $this->database = $database;
    }

    function get_databases() {
        // tbd
    }

    function get_rows($id = null) {
        // current_database must be set
        if(is_null($current_database) || empty($current_database) || !isset($current_database)) { throw new Exception('current_database is not set'); return; }

    }
}