<?php

declare(strict_types=1);

namespace Tests\Unit;

use Fifthsage\KCT\SafetyNumber;
use Fifthsage\KCT\Socket;
use PHPUnit\Framework\TestCase;

class KCTPacketTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    public function testSend()
    {
        $socket = new Socket('127.0.0.1', 1337);

        $packet = new SafetyNumber('test');

        $results = $socket->send([
          $packet->login()->getPacket(),
          $packet->register('050848400000', '01000000000')->getPacket(),
        ]);

        foreach ($results as $key => $result) {
            $this->assertEquals('00', SafetyNumber::getResultCode($result));
        }
    }
}
