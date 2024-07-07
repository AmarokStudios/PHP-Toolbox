<?php

namespace DBHelix\Integration;

class ApiService {
    public function fetch($url, $params = []) {
        // Implementation for fetching data from an external API
        $queryString = http_build_query($params);
        $response = file_get_contents("$url?$queryString");
        return json_decode($response, true);
    }

    public function post($url, $data = []) {
        // Implementation for posting data to an external API
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ],
        ];
        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        return json_decode($response, true);
    }
}
?>
