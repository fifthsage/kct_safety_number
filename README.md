# kct_safety_number

KCT 안심번호 packet 통신

## usage

```php
use Fifthsage\KCT\SafetyNumber;
use Fifthsage\KCT\Socket;

$packet = new SafetyNumber('company id');

$socket = new Socket('127.0.0.1', 1337);

$results = $socket->send([
  'LOGIN' => $packet->login()->getPacket(),
  'REGISTER' => $packet->register('050848400000', '01000000000')->getPacket(),
]);

echo SafetyNumber::getResultCode($results['LOGIN']).PHP_EOL;
echo SafetyNumber::getResultCode($results['REGISTER']).PHP_EOL;

```
