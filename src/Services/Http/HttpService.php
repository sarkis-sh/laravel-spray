<?php

declare(strict_types=1);

namespace src\Services\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use src\Models\Http\Response;

/**
 * Class HttpService
 *
 * Provides HTTP request methods.
 * 
 * @package src\Services\Http
 */
class HttpService
{
    /**
     * Send a PUT request.
     *
     * @param string $url The URL to send the request to.
     * @param mixed $body The body of the request.
     * @param array $headers Additional request headers.
     * @return Response The HTTP response.
     */
    public function doPut(string $url, $body, array $headers = []): Response
    {
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);

        $request = new Request(
            'PUT',
            $url,
            $headers,
            $body
        );

        $response = $client->sendAsync($request)->wait();

        $code = $response->getStatusCode();
        $status = $code == 200 ? 'Success' : 'Error';

        return new Response($status, json_decode(strval($response->getBody()), true), $code);
    }

    /**
     * Send a GET request.
     *
     * @param string $url The URL to send the request to.
     * @param array $headers Additional request headers.
     * @return Response The HTTP response.
     */
    public function doGet(string $url, array $headers = []): Response
    {
        $client = new Client([
            RequestOptions::VERIFY => false,
        ]);

        $request = new Request(
            'GET',
            $url,
            $headers
        );

        $response = $client->sendAsync($request)->wait();

        $code = $response->getStatusCode();
        $status = $code == 200 ? 'Success' : 'Error';

        return new Response($status, json_decode(strval($response->getBody()), true), $code);
    }
}
