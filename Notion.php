<?php
namespace Notion;
class Notion {
    private $apiKey = null; // holds the API key
    var $current_database = null; // stores the ID of database if set
    const api = "https://api.notion.com/v1/";

    function __construct($apiKey = null, $current_database = null) {
        // a token is required
        if(empty($apiKey)) { throw new Exception('A token is required'); return; }
        $this->token = $apiKey;
        $this->database = $database;
    }

    function get_databases() {
        $endpoint = "databases";
    }

    function get_rows($id = null) {
        $endpoint = "databases";
        // current_database must be set
        if(empty($id) && empty($current_database)) { throw new Exception('database id needed'); return; }
    }

    function http_request() {
        $options = [ 
            'http' => [
                "method" => "GET",
                "header" => "Authorization: Bearer ".$apiKey."\r\n"."Notion-Version: 2021-05-13\r\n"
        ]];
        $context=stream_context_create($options);
        $data=file_get_contents('http://www.someservice.com/api/fetch?key=1234567890',false,$context);
    }
}