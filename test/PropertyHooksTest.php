<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\{
    Relation,
    Entry,
};
use Smeghead\PhpClassDiagram\Php\PhpReader;

final class PropertyHooksTest extends TestCase
{
    private string $fixtureDir;

    public function setUp(): void
    {
        $this->fixtureDir = sprintf('%s/fixtures', __DIR__);
    }

    public function tearDown(): void
    {
        $this->fixtureDir = '';
    }

    public function testDump_PropertyHooks_Classes(): void
    {
        $directory = sprintf('%s/property-hooks', $this->fixtureDir);
        $options = new Options([
        ]);
        $files = [
            'SimpleHooks.php',
        ];

        $rel = $this->getRelation($directory, $options, $files);

        $expected = <<<EOS
@startuml class-diagram
  class "SimpleHooks" as SimpleHooks {
    +bar : string
    +baz : string {get/set}
    +onlyGet : string {get}
    +onlySet : string {set}
    +ref : string {&get/set}
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    /**
     * @param string[] $files
     */
    private function getRelation(string $directory, Options $options, array $files): Relation
    {
        $entries = [];
        foreach ($files as $f) {
            $filename = sprintf('%s/%s', $directory, $f);
            $classes = PhpReader::parseFile($directory, $filename, $options);
            $d = dirname($f);
            if ($d === '.') {
                $d = '';
            }
            $entries[] = array_map(fn($c) => new Entry($d, $c->getInfo(), $options), $classes);
        }

        return new Relation(array_merge(...$entries), $options);
    }
}
