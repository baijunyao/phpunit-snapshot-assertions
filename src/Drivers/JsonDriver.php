<?php

namespace Spatie\Snapshots\Drivers;

use PHPUnit\Framework\Assert;
use Spatie\Snapshots\Driver;
use Spatie\Snapshots\Exceptions\CantBeSerialized;

class JsonDriver implements Driver
{
    /** @var int @see https://www.php.net/manual/en/json.constants.php */
    private $flags;

    public function __construct($flags = JSON_PRETTY_PRINT)
    {
        $this->flags = $flags;
    }

    public function serialize($data): string
    {
        if (is_string($data)) {
            $data = json_decode($data);
        }

        if (is_resource($data)) {
            throw new CantBeSerialized('Resources can not be serialized to json');
        }

        return json_encode($data, $this->flags)."\n";
    }

    public function extension(): string
    {
        return 'json';
    }

    public function match($expected, $actual)
    {
        if (is_string($actual)) {
            $actual = json_decode($actual, true, 512, JSON_THROW_ON_ERROR);
        }
        $expected = json_decode($expected, true, 512, JSON_THROW_ON_ERROR);
        Assert::assertJsonStringEqualsJsonString(json_encode($expected), json_encode($actual));
    }
}
