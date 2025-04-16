<?php
class response {
    public function __construct() {

    }

    private function getHeader($code = 200) {
        $headers = array(
            200 => 'HTTP/1.1 200 OK',
            201 => 'HTTP/1.1 201 Created',
            204 => 'HTTP/1.1 204 No Content',
            400 => 'HTTP/1.1 400 Bad Request',
            401 => 'HTTP/1.1 401 Unauthorized',
            403 => 'HTTP/1.1 403 Forbidden',
            404 => 'HTTP/1.1 404 Not Found',
            405 => 'HTTP/1.1 405 Method Not Allowed',
            500 => 'HTTP/1.1 500 Internal Server Error',
            503 => 'HTTP/1.1 503 Service Unavailable'
        );

        if (isset($headers[$code])) {
            return($headers[$code]);
        } else {
            return($headers[200]);
        }
    }

    public function error($message, $responseCode = 400) {
        $header = $this->getHeader($responseCode);
        header($header);

        $response = json_encode(["error" => $message]);
        echo $response;
        exit();

    }

    public function message($message, $responseCode = 200) {
        $header = $this->getHeader($responseCode);
        header($header);

        $response = json_encode(["msg"=> $message]);
        echo $response;
        exit();
    }

    public Function setHeader($code) {
        $header = $this->getHeader($code);
        header($header);
    }
}