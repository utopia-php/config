<?php

namespace Utopia\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Exception\Parse;
use Utopia\Config\Parser\None;

class NoneTest extends TestCase
{
    protected None $parser;

    protected function setUp(): void
    {
        $this->parser = new None();
    }

    public function testNoneBasicTypes(): void
    {
        $variable = [
            "string" => "hello world",
            "unicode_string" => "ä你こحب🌍",
            "integer" => 42,
            "float" => 3.14159,
            "negative" => -50,
            "boolean_true" => true,
            "boolean_false" => false,
            "null" => null,
        ];

        $data = $this->parser->parse($variable);

        $this->assertSame("hello world", $data["string"]);
        $this->assertSame("ä你こحب🌍", $data["unicode_string"]);
        $this->assertSame(42, $data["integer"]);
        $this->assertSame(3.14159, $data["float"]);
        $this->assertSame(-50, $data["negative"]);
        $this->assertTrue($data["boolean_true"]);
        $this->assertFalse($data["boolean_false"]);
        $this->assertNull($data["null"]);
    }

    public function testNoneParseException(): void
    {
        $this->expectException(Parse::class);

        $data = $this->parser->parse(null);
        $data = $this->parser->parse("hello");
        $data = $this->parser->parse(false);
        $data = $this->parser->parse(12);
    }

    public function testNoneEdgeCases(): void
    {
        $value = [];
        $data = $this->parser->parse($value);
        $this->assertSame($value, $data);
    }
}
