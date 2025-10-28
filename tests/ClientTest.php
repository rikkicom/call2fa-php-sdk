<?php

namespace Rikkicom\Call2FA\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;
use PHPUnit\Framework\TestCase;
use Rikkicom\Call2FA\Client;
use Rikkicom\Call2FA\ClientException;

class ClientTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenLoginIsEmpty(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the login parameter is empty');

        new Client('', 'password');
    }

    public function testConstructorThrowsExceptionWhenPasswordIsEmpty(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the password parameter is empty');

        new Client('login', '');
    }

    public function testGetVersion(): void
    {
        $client = $this->createMockedClient();

        $this->assertSame('v1', $client->getVersion());
    }

    public function testSetVersion(): void
    {
        $client = $this->createMockedClient();

        $client->setVersion('v2');

        $this->assertSame('v2', $client->getVersion());
    }

    public function testCallThrowsExceptionWhenPhoneNumberIsEmpty(): void
    {
        $client = $this->createMockedClient();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the phoneNumber parameter is empty');

        $client->call('', 'http://callback.url');
    }

    public function testCallWithValidParameters(): void
    {
        // Mock responses
        $mock = new MockHandler([
            // Auth response
            new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            // Call response
            new Response(201, [], json_encode([
                'call_id' => '12345',
                'status' => 'initiated'
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = $this->createMockedClient($handlerStack);

        $result = $client->call('+380631010121', 'http://callback.url');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('call_id', $result);
        $this->assertSame('12345', $result['call_id']);
    }

    public function testCallThrowsExceptionOnIncorrectStatusCode(): void
    {
        // Mock responses
        $mock = new MockHandler([
            // Auth response
            new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            // Call response with wrong status code
            new Response(400, [], json_encode(['error' => 'Bad request'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = $this->createMockedClient($handlerStack);

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Cannot perform a request on call step:');

        $client->call('+380631010121', 'http://callback.url');
    }

    public function testCallViaLastDigitsThrowsExceptionWhenPhoneNumberIsEmpty(): void
    {
        $client = $this->createMockedClient();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the phoneNumber parameter is empty');

        $client->callViaLastDigits('', 'pool-id-123');
    }

    public function testCallViaLastDigitsThrowsExceptionWhenPoolIDIsEmpty(): void
    {
        $client = $this->createMockedClient();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the poolID parameter is empty');

        $client->callViaLastDigits('+380631010121', '');
    }

    public function testCallViaLastDigitsWithValidParameters(): void
    {
        // Mock responses
        $mock = new MockHandler([
            // Auth response
            new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            // Call response
            new Response(201, [], json_encode([
                'call_id' => '12345',
                'last_digits' => '1234'
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = $this->createMockedClient($handlerStack);

        $result = $client->callViaLastDigits('+380631010121', 'pool-id-123', false);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('call_id', $result);
    }

    public function testCallViaLastDigitsWithSixDigits(): void
    {
        // Mock responses
        $mock = new MockHandler([
            // Auth response
            new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            // Call response
            new Response(201, [], json_encode([
                'call_id' => '12345',
                'last_digits' => '123456'
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = $this->createMockedClient($handlerStack);

        $result = $client->callViaLastDigits('+380631010121', 'pool-id-123', true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('last_digits', $result);
    }

    public function testCallWithCodeThrowsExceptionWhenPhoneNumberIsEmpty(): void
    {
        $client = $this->createMockedClient();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the phoneNumber parameter is empty');

        $client->callWithCode('', '1234', 'en');
    }

    public function testCallWithCodeThrowsExceptionWhenCodeIsEmpty(): void
    {
        $client = $this->createMockedClient();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the code parameter is empty');

        $client->callWithCode('+380631010121', '', 'en');
    }

    public function testCallWithCodeThrowsExceptionWhenLangIsEmpty(): void
    {
        $client = $this->createMockedClient();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the lang parameter is empty');

        $client->callWithCode('+380631010121', '1234', '');
    }

    public function testCallWithCodeWithValidParameters(): void
    {
        // Mock responses
        $mock = new MockHandler([
            // Auth response
            new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            // Call response
            new Response(201, [], json_encode([
                'call_id' => '12345',
                'code' => '1234'
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = $this->createMockedClient($handlerStack);

        $result = $client->callWithCode('+380631010121', '1234', 'en');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('call_id', $result);
    }

    public function testInfoThrowsExceptionWhenIdIsEmpty(): void
    {
        $client = $this->createMockedClient();

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('the id parameter is empty');

        $client->info('');
    }

    public function testInfoWithValidId(): void
    {
        // Mock responses
        $mock = new MockHandler([
            // Auth response
            new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            // Info response
            new Response(200, [], json_encode([
                'call_id' => '12345',
                'status' => 'completed',
                'phone_number' => '+380631010121'
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = $this->createMockedClient($handlerStack);

        $result = $client->info('12345');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('call_id', $result);
        $this->assertArrayHasKey('status', $result);
    }

    public function testInfoThrowsExceptionOnIncorrectStatusCode(): void
    {
        // Mock responses
        $mock = new MockHandler([
            // Auth response
            new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            // Info response with wrong status code
            new Response(404, [], json_encode(['error' => 'Not found'])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = $this->createMockedClient($handlerStack);

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Cannot perform a request to get the call info:');

        $client->info('12345');
    }

    public function testConstructorThrowsExceptionOnAuthFailure(): void
    {
        // Mock auth response with error
        $mock = new MockHandler([
            new Response(401, [], json_encode(['error' => 'Unauthorized'])),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Cannot perform a request on authorization step:');

        $this->createMockedClient($handlerStack);
    }

    /**
     * Create a client with mocked HTTP responses
     */
    private function createMockedClient(?HandlerStack $handlerStack = null): Client
    {
        if ($handlerStack === null) {
            // Default mock for auth
            $mock = new MockHandler([
                new Response(200, [], json_encode(['jwt' => 'test-jwt-token'])),
            ]);
            $handlerStack = HandlerStack::create($mock);
        }

        $mockGuzzleClient = new GuzzleClient(['handler' => $handlerStack]);
        
        // Create client with injected mock client
        return new Client('test-login', 'test-password', $mockGuzzleClient);
    }
}
