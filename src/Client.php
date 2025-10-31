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
     * @param \GuzzleHttp\Client|null $httpClient Optional HTTP client for testing
     *
     * @throws ClientException
     */
    public function __construct($login, $password, $httpClient = null)
    {
        if (empty($login)) {
            throw new ClientException('the login parameter is empty');
        }

        if (empty($password)) {
            throw new ClientException('the password parameter is empty');
        }

        $this->apiLogin = $login;
        $this->apiPassword = $password;

        $this->httpClient = $httpClient ?: new \GuzzleHttp\Client();

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
     * @param bool   $useSixDigits
     *
     * @return mixed
     *
     * @throws ClientException
     */
    public function callViaLastDigits($phoneNumber, $poolID, $useSixDigits = false)
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

        if ($useSixDigits) {
            $uri = $this->makeFullURI(sprintf('pool/%s/call/six-digits', $poolID));
        } else {
            $uri = $this->makeFullURI(sprintf('pool/%s/call', $poolID));
        }

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
     * Initiate a new call with the code
     *
     * @param string $phoneNumber
     * @param string $code
     * @param string $lang
     *
     * @return mixed
     *
     * @throws ClientException
     */
    public function callWithCode($phoneNumber, $code, $lang)
    {
        if (empty($phoneNumber)) {
            throw new ClientException('the phoneNumber parameter is empty');
        }

        if (empty($code)) {
            throw new ClientException('the code parameter is empty');
        }

        if (empty($lang)) {
            throw new ClientException('the lang parameter is empty');
        }

        $headers = [
            'Authorization' => sprintf('Bearer %s', $this->jwt),
        ];

        $callData = [
            'phone_number' => $phoneNumber,
            'code' => $code,
            'lang' => $lang,
        ];

        $uri = $this->makeFullURI('code/call');

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
     * Initiate a new call with the code via gateway name
     *
     * @param string $phoneNumber
     * @param string $callbackURL
     * @param string $gatewayName
     * @param string $callFrom
     *
     * @return mixed
     *
     * @throws ClientException
     */
    public function callWithGatewayName($phoneNumber, $callbackURL, $gatewayName, $callFrom)
    {
        if (empty($phoneNumber)) {
            throw new ClientException('the phoneNumber parameter is empty');
        }

        if (empty($gatewayName)) {
            throw new ClientException('the gatewayName parameter is empty');
        }

        if (empty($callFrom)) {
            throw new ClientException('the callFrom parameter is empty');
        }

        $headers = [
            'Authorization' => sprintf('Bearer %s', $this->jwt),
        ];

        $callData = [
            'phone_number' => $phoneNumber,
            'callback_url' => $callbackURL,
            'gateway_name' => $gatewayName,
            'call_from' => $callFrom,
        ];

        $uri = $this->makeFullURI('call/with/gateway-name');

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
     * Get information about a call by its identifier
     *
     * @param string $id
     *
     * @return mixed
     *
     * @throws ClientException
     */
    public function info($id)
    {
        if (empty($id)) {
            throw new ClientException('the id parameter is empty');
        }

        $headers = [
            'Authorization' => sprintf('Bearer %s', $this->jwt),
        ];

        $uri = $this->makeFullURI(sprintf('call/%s', $id));

        try {
            $response = $this->httpClient->request('GET', $uri, ['headers' => $headers]);
            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                throw new ClientException(sprintf('Incorrect status code: %d', $statusCode));
            }

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new ClientException(sprintf('Cannot perform a request to get the call info: %s', $e->getMessage()));
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
