<?php

namespace Utopia\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Exception\Parse;
use Utopia\Config\Parser\Dotenv;

class DotenvTest extends TestCase
{
    protected Dotenv $parser;

    protected function setUp(): void
    {
        $this->parser = new Dotenv();
    }

    protected function tearDown(): void
    {
    }

    public function testDotenvBasicTypes(): void
    {
        $dotenv = <<<DOTENV
          STRING=hello world
          UNICODE_STRING=ä你こحب🌍
          INTEGER=42
          FLOAT=3.14159
          NEGATIVE=-50
          BOOLEAN_TRUE=true
          BOOLEAN_FALSE=false
          NULL_VALUE=null
        DOTENV;

        $data = $this->parser->parse($dotenv);

        $this->assertSame("hello world", $data["STRING"]);
        $this->assertSame("ä你こحب🌍", $data["UNICODE_STRING"]);
        $this->assertSame('42', $data["INTEGER"]);
        $this->assertSame('3.14159', $data["FLOAT"]);
        $this->assertSame('-50', $data["NEGATIVE"]);

        $this->assertTrue($data["BOOLEAN_TRUE"]);
        $this->assertFalse($data["BOOLEAN_FALSE"]);
        $this->assertNull($data["NULL_VALUE"]);
    }

    public function testDotenvParseException(): void
    {
        $this->expectException(Parse::class);

        $this->parser->parse('=b');
        $this->parser->parse(12);
        $this->parser->parse(false);
        $this->parser->parse(null);
    }

    public function testDotenvEdgeCases(): void
    {
        $data = $this->parser->parse("");
        $this->assertCount(0, $data);
        $data = $this->parser->parse("KEY=");
        $this->assertCount(1, $data);
        $this->assertSame("", $data['KEY']);
    }

    public function testDotenvComment(): void
    {
        $data = $this->parser->parse(
            <<<DOTENV
            HOST=127.0.0.1
            PORT=3306 # A comment
            # Another comment, with empty line below intentionally

            PASSWORD=secret
            DOTENV
        );

        $this->assertSame("127.0.0.1", $data["HOST"]);
        $this->assertSame("3306", $data["PORT"]);
        $this->assertSame("secret", $data["PASSWORD"]);
        $this->assertCount(3, \array_keys($data));
        $this->assertArrayNotHasKey("PASSWORD2", $data);
    }

    public function testValueConvertor(): void
    {
        $data = $this->parser->parse(
            <<<DOTENV
            KEY1=1 # Becomes true
            KEY2=on # Becomes true
            KEY3=enabled # Becomes true
            KEY4=Enabled # Becomes true
            KEY5=true # Becomes true
            KEY6=TRUE # Becomes true
            KEY7=yes # Becomes true

            KEY8=0 # Becomes false
            KEY9=off # Becomes false
            KEY10=disabled # Becomes false
            KEY11=Disabled # Becomes false
            KEY12=false # Becomes false
            KEY13=FALSE # Becomes false
            KEY14=no # Becomes false

            KEY15=11  # Preserves value
            KEY16=20  # Preserves value

            KEY17=online # Preserves value
            KEY18=offline # Preserves value

            KEY19=notenabled # Preserves value
            KEY20=notdisabled # Preserves value

            KEY21=yesterday # Preserves value
            KEY22=november # Preserves value

            KEY23=agree # Preserves value
            KEY24=disagree # Preserves value

            DOTENV
        );

        $expectedValues = [
            "KEY1" => true,
            "KEY2" => true,
            "KEY3" => true,
            "KEY4" => true,
            "KEY5" => true,
            "KEY6" => true,
            "KEY7" => true,

            "KEY8" => false,
            "KEY9" => false,
            "KEY10" => false,
            "KEY11" => false,
            "KEY12" => false,
            "KEY13" => false,
            "KEY14" => false,

            "KEY15" => "11",
            "KEY16" => "20",
            "KEY17" => "online",
            "KEY18" => "offline",
            "KEY19" => "notenabled",
            "KEY20" => "notdisabled",
            "KEY21" => "yesterday",
            "KEY22" => "november",
            "KEY23" => "agree",
            "KEY24" => "disagree",
        ];

        $this->assertCount(\count($expectedValues), \array_keys($data));

        foreach ($expectedValues as $key => $value) {
            $this->assertSame($value, $data[$key], "Failed for key: $key");
        }
    }
}
