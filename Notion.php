<?php
namespace Notion;
class Notion {
    private $api_key = null; // holds the API key
    var $current_database = null; // stores the ID of database if set
    const NOTION_API = "https://api.notion.com/v1/";
    const NOTION_VER = "2021-05-13";

    function __construct($api_key = null, $database = null) {
        // a token is required
        if(empty($api_key)) $this->throwE('A token is required');
        $this->token            = $api_key;
        $this->current_database = $database;
    }

    function get_databases($start_cursor = null, $num_databases = 100) {
        // # CONSTRAINTS ====================
        $endpoint = "databases";

        // num_databases must be int
        if(is_numeric($num_databases)) $num_databases = intval($num_databases); // de-string
            else $this->throwE('get_databases: argument for `num_databases` must be an int');
        $num_databases = ($num_databases <= 0 && $num_databases > 100) ? 100 : $num_databases; // <= will turn it to default 100

        // # LOGIC ====================

        // construct payload array
        $payload = [];
        if(!empty($start_cursor))  $payload[] = $start_cursor;
        if(!empty($num_databases)) $payload[] = $num_databases;

        // commit
        return $this->http_c($endpoint, "get", $payload);
    }

    function get_pages($id = null, $filter = null, $sorts = null, $start_cursor = null, $num_pages = 100) {
        // # CONSTRAINTS ====================

        // current_database must be set
        if(empty($id) && empty($this->current_database)) $this->throwE('database id needed');
        $id       = (empty($id)) ? $this->current_database : $id;
        $endpoint = "databases/$id/query";

        // some parameters must be an array
        if(!empty($filter) && !is_array($filter)) $this->throwE('get_pages: argument for `filter` must be an array');
        if(!empty($sorts)  && !is_array($sorts))  $this->throwE('get_pages: argument for `sorts` must be an array');

        // num_pages must be int
        if(is_numeric($num_pages)) $num_pages = intval($num_pages); // de-string
            else $this->throwE('get_pages: argument for `num_pages` must be an int');
        $num_pages = ($num_pages <= 0 && $num_pages > 100) ? 100 : $num_pages; // <= will turn it to default 100

        // # LOGIC ====================

        // construct payload array
        $payload = [];
        if(!empty($filter))       $payload[] = $filter;
        if(!empty($sorts))        $payload[] = $sorts;
        if(!empty($start_cursor)) $payload[] = $start_cursor;
        if(!empty($num_pages))    $payload[] = $num_pages;

        // commit
        return $this->http_c($endpoint, "post", $payload);
    }

    function get_page_blocks($toHTML = false) {
        // TBI
        // # CONSTRAINTS ====================

        // # LOGIC ====================
    }

    private function http_c($endpoint = null, $method = "get", $payload = null, $convertJSON = true) {
        // # CONSTRAINTS ====================

        // sanisation for HTTP method
        $method = strtolower($method);

        // endpoint is required
        if(empty($endpoint))                         $this->throwE('http_c: an endpoint is required');

        // some parameters must be an array
        if(!empty($payload)  && !is_array($payload)) $this->throwE('http_c: argument for `payload` must be an array');

        // # LOGIC ====================

        if(!empty($payload) && $convertJSON) $payload = json_encode($payload);
        $url  = $this->NOTION_API.$endpoint;
        $curl = curl_init();
        if($method == "post"){
            curl_setopt($curl, CURLOPT_POST, 1);
            if($payload) curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }

        // curl options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer ".$this->api_key,
            "Notion-Version: ".$this->NOTION_VER,
            'Content-Type: application/json'
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // execute curl session
        $result = curl_exec($curl);
        if(!$result){ $this->throwE('Connection error'); }
        curl_close($curl);
        if($convertJSON) $result = json_decode($result);
        return $result;
    }

    private function throwE($errorText) {
        throw new \Exception($errorText);
    }
}