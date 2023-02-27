<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use Smeghead\PhpClassDiagram\Config\Options;
use Smeghead\PhpClassDiagram\DiagramElement\ {
    Relation,
    Entry,
    Package,
};
require_once(__DIR__ . '/dummy/PhpClassDummy.php');

final class PackageTest extends TestCase {
    private $fixtureDir;

    private string $product_expression = <<<EOJ
{
    "type": {
        "name": "Product",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [
        {
            "name": "name",
            "type": {
                "name": "Name",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        },
        {
            "name": "price",
            "type": {
                "name": "Price",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;
    private string $price_expression = <<<EOJ
{
    "uses": [
        {
            "name": "PhpParser",
            "meta": "Stmt_Class",
            "namespace": ["PhpParse"]
        }
    ],
    "type": {
        "name": "Price",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [
        {
            "name": "price",
            "type": {
                "name": "int",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;
    private string $name_expression = <<<EOJ
{
    "type": {
        "name": "Name",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [
        {
            "name": "name",
            "type": {
                "name": "string",
                "namespace": []
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;
    private string $interface_expression = <<<EOJ
{
    "type": {
        "name":"Interface_",
        "meta":"Stmt_Interface",
        "namespace":[]
    },
    "properties":[
        {
            "name":"name",
            "type":{
                "name":"string",
                "namespace":[]
            },
            "modifier":{"private":true}
        }
    ],
    "methods":[
        {
            "name":"method1",
            "type":{
                "name":"Product",
                "namespace":[]
            },
            "params":[
                {
                    "name":"param1",
                    "type":{"name":"string"}
                }
            ],
            "modifier":{"private":true}
        }
    ]
}
EOJ;
    private string $implement_expression = <<<EOJ
{
    "type":{
        "name":"Implement_",
        "meta":"Stmt_Class",
        "namespace":[]
    },
    "properties":[
        {
            "name":"name",
            "type":{"name":"string","namespace":[]},
            "modifier":{"private":true}
        }
    ],
    "methods":[
        {
            "name":"method1",
            "type":{
                "name":"Product",
                "namespace":[]
            },
            "params":[
                {
                    "name":"param1",
                    "type":{"name":"string"}
                }
            ],
            "modifier":{"private":true}
        }
    ],
    "extends":[
        {
            "name":"Interface_",
            "meta":"Stmt_Interface",
            "namespace":[]
        }
    ]
}
EOJ;
    private string $product_method_expression = <<<EOJ
{
    "type": {
        "name": "Product",
        "meta": "Stmt_Class",
        "namespace": []
    },
    "properties": [],
    "methods":[
        {
            "name":"getName",
            "type":{
                "name":"Name",
                "namespace":[]
            },
            "params":[
            ],
            "modifier":{"public":true}
        },
        {
            "name":"getPrice",
            "type":{
                "name":"Price",
                "namespace":[]
            },
            "params":[
            ],
            "modifier":{"private":true}
        }
    ]
}
EOJ;
    private string $packaged_product_expression = <<<EOJ
{
    "uses": [{
        "name": "Name",
        "meta": "Stmt_Class",
        "namespace": ["hoge", "fuga", "product", "utility"]
    }],
    "type": {
        "name": "Product",
        "meta": "Stmt_Class",
        "namespace": ["hoge", "fuga", "product"]
    },
    "properties": [
        {
            "name": "name",
            "type": {
                "name": "Name",
                "namespace": ["hoge", "fuga", "product", "utility"]
            },
            "modifier": {
                "private": true
            }
        },
        {
            "name": "price",
            "type": {
                "name": "Price",
                "namespace": ["hoge", "fuga", "product"]
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;
    private string $packaged_name_expression = <<<EOJ
{
    "uses": [{
        "name": "Product",
        "meta": "Stmt_Class",
        "namespace": ["hoge", "fuga", "product"]
    }],
    "type": {
        "name": "Name",
        "meta": "Stmt_Class",
        "namespace": ["hoge", "fuga", "product", "utility"]
    },
    "properties": [
        {
            "name": "name",
            "type": {
                "name": "string",
                "namespace": ["hoge", "fuga", "product"]
            },
            "modifier": {
                "private": true
            }
        }
    ],
    "methods":[]
}
EOJ;

    public function setUp(): void {
    }

    public function testInitialize(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $namespace = $rel->getPackage();

        $this->assertInstanceOf(Package::class, $namespace, 'namespace instance');
        $this->assertSame('ROOT', $namespace->name, 'ROOT namespace name');

        $product = $namespace->children[0];
        $this->assertSame('product', $product->name, 'product namespace name');

        $this->assertSame('Product', $product->entries[0]->getClass()->getClassType()->getName(), 'product class name');
        $this->assertSame('Price', $product->entries[1]->getClass()->getClassType()->getName(), 'price class name');
        $this->assertSame('Name', $product->entries[2]->getClass()->getClassType()->getName(), 'name class name');

    }

    public function testDump(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

        $expected =<<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product
    class "Price" as product_Price
    class "Name" as product_Name
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump2(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product/utility', new PhpClassDummy('product/utility', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product
    class "Price" as product_Price
    package utility as product.utility {
      class "Name" as product_Name
    }
  }
  product_Product ..> product_Name
  product_Product ..> product_Price
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump3(): void {
        $options = new Options([]);
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }

    public function testDump4(): void {
        $options = new Options([
            'enable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_ {
      -name : string
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump5(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'enable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_ {
      -method1(param1)
    }
  }
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump6(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'enable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Interface_.php', $this->interface_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Implement_.php', $this->implement_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml class-diagram
  package product as product {
    interface "Interface_" as product_Interface_ {
      -method1(param1)
    }
    class "Implement_" as product_Implement_ {
      -method1(param1)
    }
  }
  product_Interface_ <|-- product_Implement_
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDump7(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_method_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

        $expected =<<<EOS
@startuml class-diagram
  package product as product {
    class "Product" as product_Product
    class "Price" as product_Price
    class "Name" as product_Name
  }
  product_Product ..> product_Name
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dump()), 'output PlantUML script.');
    }
    public function testDumpPackage1(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->product_method_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Price.php', $this->price_expression), $options),
            new Entry('product', new PhpClassDummy('product', 'product/Name.php', $this->name_expression), $options),
        ];
        $rel = new Relation($entries, $options);

        $expected =<<<EOS
@startuml package-related-diagram
  package ROOT as ROOT {
    package product {
    }
  }
  package PhpParse as PhpParse #DDDDDD {
  }
  product --> PhpParse
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dumpPackages()), 'output PlantUML script.');
    }
    public function testDumpPackage_bothSideArrows(): void {
        $options = new Options([
            'disable-class-properties' => true,
            'disable-class-methods' => true,
        ]);
        $entries = [
            new Entry('product', new PhpClassDummy('product', 'product/Product.php', $this->packaged_product_expression), $options),
            new Entry('product/utility', new PhpClassDummy('product/utility', 'product/utility/Name.php', $this->packaged_name_expression), $options),
        ];
        $rel = new Relation($entries, $options);
        $expected =<<<EOS
@startuml package-related-diagram
  package hoge.fuga as fuga {
    package product {
      package utility {
      }
    }
  }
  product <-[#red,plain,thickness=4]-> utility
@enduml
EOS;
        $this->assertSame($expected, implode(PHP_EOL, $rel->dumpPackages()), 'output PlantUML script.');
    }
}
