<?php

namespace Rikkicom\Call2FA\Tests;

use PHPUnit\Framework\TestCase;
use Rikkicom\Call2FA\ClientException;

class ClientExceptionTest extends TestCase
{
    public function testClientExceptionCanBeThrown(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('Test exception');

        throw new ClientException('Test exception');
    }

    public function testClientExceptionExtendsException(): void
    {
        $exception = new ClientException('Test message');

        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testClientExceptionMessage(): void
    {
        $message = 'Custom error message';
        $exception = new ClientException($message);

        $this->assertSame($message, $exception->getMessage());
    }
}
