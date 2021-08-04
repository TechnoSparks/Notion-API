<?php
namespace Notion;
class Notion {
    private $apiKey = null; // holds the API key
    var $current_database = null; // stores the ID of database if set
    const NOTION_API = "https://api.notion.com/v1/";
    const NOTION_VER = "2021-05-13";

    function __construct($apiKey = null, $database = null) {
        // a token is required
        if(empty($apiKey)) throw new \Exception('A token is required');
        $this->token = $apiKey;
        $this->current_database = $database;
    }

    function get_databases() {
        $endpoint = "databases";
    }

    function get_rows($id = null, $filter = null, $sorts = null, $start_cursor = null, $num_rows = 100) {
        // # CONSTRAINTS 
        // current_database must be set
        if(empty($id) && empty($this->current_database)) throw new \Exception('database id needed');
        $id       = (empty($id)) ? $this->current_database : $id;
        $endpoint = "databases/$id/query";
        // some parameters must be an array
        if(!empty($filter) && !is_array($filter)) throw new \Exception('get_rows: argument for `filter` must be an array');
        if(!empty($sorts)  && !is_array($sorts))  throw new \Exception('get_rows: argument for `sorts` must be an array');

        // # LOGIC 
        // construct payload array
        $payload = [];
        if(!empty($filter))       $payload[] = $filter;
        if(!empty($sorts))        $payload[] = $sorts;
        if(!empty($start_cursor)) $payload[] = $start_cursor;
        if(!empty($num_rows))     $payload[] = $num_rows;
        // commit
        return $this->http_c($endpoint, "post", $payload);
    }

    function http_c($endpoint = null, $method = "get", $payload = null, $convertJSON = true) {
        // # CONSTRAINTS 
        // sanisation for HTTP method
        $method = strtolower($method);
        // endpoint is required
        if(empty($endpoint))                         throw new \Exception('http_c: an endpoint is required');
        // some parameters must be an array
        if(!empty($payload)  && !is_array($payload)) throw new \Exception('http_c: argument for `payload` must be an array');

        // # LOGIC 
        if(!empty($payload) && $convertJSON) $payload = json_encode($payload); 
        $url = $this->NOTION_API.$endpoint;
        $curl = curl_init();
        if($method == "post"){
            curl_setopt($curl, CURLOPT_POST, 1);
            if($payload) curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }
        // curl options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer ".$this->apiKey,
            "Notion-Version: ".$this->NOTION_VER,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // execute curl session
        $result = curl_exec($curl);
        if(!$result){ throw new \Exception('Connection error'); }
        curl_close($curl);
        if($convertJSON) $result = json_decode($result);
        return $result;
    }
}