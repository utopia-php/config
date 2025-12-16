<?php

namespace Utopia\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Parser\JSON;
use Utopia\Config\Exception\Parse;

class JSONTest extends TestCase
{
    protected JSON $parser;

    protected function setUp(): void
    {
        $this->parser = new JSON();
    }

    public function testJSONBasicTypes(): void
    {
        $json = <<<JSON
            {
              "string": "hello world",
              "unicode_string": "ä你こحب🌍",
              "integer": 42,
              "float": 3.14159,
              "negative": -50,
              "boolean_true": true,
              "boolean_false": false,
              "null_value": null
            }
        JSON;

        $data = $this->parser->parse($json);

        $this->assertSame("hello world", $data["string"]);
        $this->assertSame("ä你こحب🌍", $data["unicode_string"]);
        $this->assertSame(42, $data["integer"]);
        $this->assertSame(3.14159, $data["float"]);
        $this->assertSame(-50, $data["negative"]);
        $this->assertTrue($data["boolean_true"]);
        $this->assertFalse($data["boolean_false"]);
        $this->assertNull($data["null_value"]);
    }

    public function testJSONArray(): void
    {
        $json = <<<JSON
            {
              "simple_array": [1, 2, 3, 4, 5],
              "mixed_array": ["string", 42, true, null, 3.14],
              "nested_array": [[1, 2, 3], ["a", "b", "c", "d"], [true, false]],
              "empty_array": []
            }
        JSON;

        $data = $this->parser->parse($json);

        $this->assertCount(5, $data["simple_array"]);
        $this->assertSame(1, $data["simple_array"][0]);
        $this->assertSame(5, $data["simple_array"][4]);

        $this->assertCount(5, $data["mixed_array"]);
        $this->assertSame("string", $data["mixed_array"][0]);
        $this->assertSame(42, $data["mixed_array"][1]);
        $this->assertSame(true, $data["mixed_array"][2]);
        $this->assertSame(null, $data["mixed_array"][3]);
        $this->assertSame(3.14, $data["mixed_array"][4]);

        $this->assertCount(3, $data["nested_array"]);

        $this->assertCount(3, $data["nested_array"][0]);
        $this->assertCount(4, $data["nested_array"][1]);
        $this->assertCount(2, $data["nested_array"][2]);

        $this->assertSame(2, $data["nested_array"][0][1]);
        $this->assertSame("b", $data["nested_array"][1][1]);
        $this->assertSame(false, $data["nested_array"][2][1]);

        $this->assertIsArray($data["empty_array"]);
        $this->assertCount(0, $data["empty_array"]);
    }

    public function testJSONObject(): void
    {
        $json = <<<JSON
            {
              "simple_object": {
                "name": "John Doe",
                "age": 30,
                "active": true
              },
              "nested_object": {
                "user": {
                  "profile": {
                    "name": "Jane",
                    "settings": {
                      "theme": "dark"
                    }
                  }
                }
              },
              "empty_object": {}
            }
        JSON;

        $data = $this->parser->parse($json);

        $this->assertSame("John Doe", $data["simple_object"]["name"]);
        $this->assertSame(30, $data["simple_object"]["age"]);
        $this->assertSame(true, $data["simple_object"]["active"]);

        $this->assertArrayHasKey("user", $data["nested_object"]);
        $this->assertArrayHasKey("profile", $data["nested_object"]["user"]);
        $this->assertArrayHasKey(
            "settings",
            $data["nested_object"]["user"]["profile"],
        );

        $this->assertSame(
            "Jane",
            $data["nested_object"]["user"]["profile"]["name"],
        );
        $this->assertSame(
            "dark",
            $data["nested_object"]["user"]["profile"]["settings"]["theme"],
        );

        $this->assertIsArray($data["empty_object"]);
        $this->assertCount(0, $data["empty_object"]);
    }

    public function testJSONParseExceptionInvalid(): void
    {
        $this->expectException(Parse::class);
        $this->parser->parse('{"invalid": json}');
    }

    public function testJSONParseExceptionNumber(): void
    {
        $this->expectException(Parse::class);
        $this->parser->parse(12);
    }

    public function testJSONParseExceptionBoolean(): void
    {
        $this->expectException(Parse::class);
        $this->parser->parse(false);
    }

    public function testJSONParseExceptionNull(): void
    {
        $this->expectException(Parse::class);
        $this->parser->parse(null);
    }

    public function testJSONEdgeCases(): void
    {
        $data = $this->parser->parse("");
        $this->assertCount(0, $data);
        $data = $this->parser->parse("{}");
        $this->assertCount(0, $data);
        $data = $this->parser->parse("[]");
        $this->assertCount(0, $data);
    }
}
