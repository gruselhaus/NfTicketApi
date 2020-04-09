<?php

class ApiClient
{
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';
    protected $validMethods = [
        self::METHOD_GET,
        self::METHOD_PUT,
        self::METHOD_POST,
        self::METHOD_DELETE,
    ];
    protected $apiUrl;
    protected $cURL;

    public function __construct($apiUrl, $username, $apiKey)
    {
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
        //Initializes the cURL instance
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($this->cURL, CURLOPT_USERAGENT, 'Shopware ApiClient');
        curl_setopt($this->cURL, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($this->cURL, CURLOPT_USERPWD, $username . ':' . $apiKey);
        curl_setopt(
            $this->cURL,
            CURLOPT_HTTPHEADER,
            ['Content-Type: application/json; charset=utf-8']
        );
    }

    public function call($url, $method = self::METHOD_GET, $data = [], $params = [])
    {
        if (!in_array($method, $this->validMethods)) {
            throw new Exception('Invalid HTTP-Methode: ' . $method);
        }
        $queryString = '';
        if (!empty($params)) {
            $queryString = http_build_query($params);
        }
        $url = rtrim($url, '?') . '?';
        $url = $this->apiUrl . $url . $queryString;
        echo $url;
        $dataString = json_encode($data);
        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $dataString);
        $result = curl_exec($this->cURL);
        $httpCode = curl_getinfo($this->cURL, CURLINFO_HTTP_CODE);

        return $result;
    }

    public function get($url, $params = [])
    {
        return $this->call($url, self::METHOD_GET, [], $params);
    }

    public function post($url, $data = [], $params = [])
    {
        return $this->call($url, self::METHOD_POST, $data, $params);
    }

    public function put($url, $data = [], $params = [])
    {
        return $this->call($url, self::METHOD_PUT, $data, $params);
    }

    public function delete($url, $params = [])
    {
        return $this->call($url, self::METHOD_DELETE, [], $params);
    }
}
