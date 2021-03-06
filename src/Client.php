<?php

namespace Rikkicom\Call2FA;

use GuzzleHttp\Exception\GuzzleException;

class Client
{
    /** @var string The API version */
    private $version = 'v1';

    /** @var string The base URL of the API */
    private $baseURI = 'https://api-call2fa.rikkicom.io';

    private $httpClient;

    /** @var string The customer's login */
    private $apiLogin;

    /** @var string The customer's password */
    private $apiPassword;

    /** @var string The JSON Web Token */
    private $jwt;

    /**
     * Client constructor.
     *
     * @param string $login
     * @param string $password
     *
     * @throws ClientException
     */
    public function __construct($login, $password)
    {
        if (empty($login)) {
            throw new ClientException('the login parameter is empty');
        }

        if (empty($password)) {
            throw new ClientException('the password parameter is empty');
        }

        $this->apiLogin = $login;
        $this->apiPassword = $password;

        $this->httpClient = new \GuzzleHttp\Client();

        $this->receiveJWT();
    }

    /**
     * Initiate a new call
     *
     * @param string $phoneNumber
     * @param string $callbackURL
     *
     * @return mixed
     *
     * @throws ClientException
     */
    public function call($phoneNumber, $callbackURL = '')
    {
        if (empty($phoneNumber)) {
            throw new ClientException('the phoneNumber parameter is empty');
        }

        $headers = [
            'Authorization' => sprintf('Bearer %s', $this->jwt),
        ];

        $callData = [
            'phone_number' => $phoneNumber,
            'callback_url' => $callbackURL,
        ];

        $uri = $this->makeFullURI('call');

        try {
            $response = $this->httpClient->request('POST', $uri, ['json' => $callData, 'headers' => $headers]);
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 201) {
                throw new ClientException(sprintf('Incorrect status code: %d on call step', $statusCode));
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new ClientException(sprintf('Cannot perform a request on call step: %s', $e->getMessage()));
        }
    }

    /**
     * Initiate a new call via the last digits mode
     *
     * @param string $phoneNumber
     * @param string $poolID
     *
     * @return mixed
     *
     * @throws ClientException
     */
    public function callViaLastDigits($phoneNumber, $poolID)
    {
        if (empty($phoneNumber)) {
            throw new ClientException('the phoneNumber parameter is empty');
        }

        if (empty($poolID)) {
            throw new ClientException('the poolID parameter is empty');
        }

        $headers = [
            'Authorization' => sprintf('Bearer %s', $this->jwt),
        ];

        $callData = [
            'phone_number' => $phoneNumber,
        ];

        $uri = $this->makeFullURI(sprintf('pool/%s/call', $poolID));

        try {
            $response = $this->httpClient->request('POST', $uri, ['json' => $callData, 'headers' => $headers]);
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 201) {
                throw new ClientException(sprintf('Incorrect status code: %d on call step', $statusCode));
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new ClientException(sprintf('Cannot perform a request on call step: %s', $e->getMessage()));
        }
    }

    /**
     * Receive the JSON Web Token from the API
     *
     * @throws ClientException
     */
    private function receiveJWT()
    {
        $authData = [
            'login' => $this->apiLogin,
            'password' => $this->apiPassword,
        ];

        $uri = $this->makeFullURI('auth');

        try {
            $response = $this->httpClient->request('POST', $uri, ['json' => $authData]);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $responseContent = $response->getBody()->getContents();
                $jsonResponse = json_decode($responseContent, true);

                $this->jwt = $jsonResponse['jwt'];
            } else {
                throw new ClientException(sprintf('Incorrect status code: %d on authorization step', $statusCode));
            }
        } catch (GuzzleException $e) {
            throw new ClientException(sprintf('Cannot perform a request on authorization step: %s', $e->getMessage()));
        }
    }

    /**
     * Create a full URI to the specified API method
     *
     * @param string $method
     *
     * @return string
     */
    private function makeFullURI($method)
    {
        return sprintf('%s/%s/%s/', $this->baseURI, $this->version, $method);
    }

    /**
     * Return the API version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set a different API version
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
