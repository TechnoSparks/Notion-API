<?php
namespace Notion;
class Notion {
    private $apiKey = null; // holds the API key
    var $current_database = null; // stores the ID of database if set
    const NOTION_API = "https://api.notion.com/v1/";
    const NOTION_VER = "2021-05-13";

    function __construct($apiKey = null, $database = null) {
        // a token is required
        if(empty($apiKey)) { throw new \Exception('A token is required'); }
        $this->token = $apiKey;
        $this->current_database = $database;
    }

    function get_databases() {
        $endpoint = "databases";
    }

    function get_rows($id = null) {
        $endpoint = "databases";
        // current_database must be set
        if(empty($id) && empty($this->current_database)) { throw new \Exception('database id needed'); }
    }

    function http_request() {
        $options = [ 
            'http' => [
                "method" => "GET",
                "header" => "Authorization: Bearer ".$this->apiKey."\r\n"."Notion-Version: $this->NOTION_VER\r\n"
        ]];
        $context=stream_context_create($options);
        $data=file_get_contents('http://www.someservice.com/api/fetch?key=1234567890',false,$context);
    }
}