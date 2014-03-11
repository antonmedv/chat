<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Elfet\Chat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Server implements MessageComponentInterface
{
    private $clients;
    private $messages = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $user = $conn->Session->get('user');
        $conn->user = $user;
        $this->clients->attach($conn);
        $conn->send(json_encode($this->messages));
    }

    public function onMessage(ConnectionInterface $from, $message)
    {
        $message = trim($message);

        if (empty($message)) {
            return;
        }

        $message = [
            'text' => $message,
            'user' => $from->user,
        ];

        $this->messages[] = $message;

        foreach ($this->clients as $client) {
            $client->send(json_encode([$message]));
        }

        if(count($this->messages) > 10) {
            array_shift($this->messages);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->close();
    }
}