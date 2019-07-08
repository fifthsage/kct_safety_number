<?php

declare(strict_types=1);

namespace Fifthsage\KCT;

class Socket
{
    private $destination = null;
    private $port = null;
    private $socket = null;

    public function __construct(?string $destination = null, ?int $port = null)
    {
        $this->destination = $destination;
        $this->port = $port;
    }

    public function setSocket($socket)
    {
        $this->socket = $socket;
    }

    public function setDestination(string $destination)
    {
        $this->destination = $destination;
    }

    public function setPort(int $port)
    {
        $this->port = $port;
    }

    public function send($packets)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_connect($this->socket, $this->destination, $this->port);

        $results = [];
        foreach ($packets as $key => $packet) {
            socket_write($this->socket, $packet, \strlen($packet));

            $result = socket_read($this->socket, 1024);

            $results[$key] = $result;
        }

        socket_close($this->socket);

        return $results;
    }
}
