<?php

namespace AppBundle\Utils\Google;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ReCaptcha
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @var string
     */
    private $secret;

    /**
     * ReCaptcha constructor.
     *
     * @param string $secret
     * @param \GuzzleHttp\Client|null $client
     */
    public function __construct($secret, Client $client = null)
    {
        $this->secret = $secret;
        $this->client = null !== $client ? $client : new Client();
    }

    public function isHuman($reCaptchaResponse)
    {
        try {
            $response = $this->client->post('https://www.google.com/recaptcha/api/siteverify', [
                'query' => [
                    'secret' => $this->secret,
                    'response' => $reCaptchaResponse
                ]
            ]);

            $body = json_decode($response->getBody(), true);

            return isset($body['success']) && $body['success'];
        } catch (RequestException $exception) {
            return false;
        }
    }
}
