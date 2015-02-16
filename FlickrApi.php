<?php

class FlickrApi {
    private $apiKey = '';
    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function api($params) {
        $params['api_key'] = $this->apiKey;
        $params['format'] = 'json';
        $params['nojsoncallback'] = 1;

        $encoded_params = array();
        foreach ($params as $k => $v){
            $encoded_params[] = urlencode($k).'='.urlencode($v);
        }

        $url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);
        $rsp = file_get_contents($url);
        $rsp_obj = json_decode($rsp, 1);

        return $rsp_obj;
    }
}