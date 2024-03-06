<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$functions = array_merge(
    require __DIR__ . '/functions/postgis-types.php',
    require __DIR__ . '/functions/geometry-constructors.php',
    require __DIR__ . '/functions/geometry-accessors.php',
    require __DIR__ . '/functions/geometry-editors.php',
    require __DIR__ . '/functions/geometry-outputs.php',
    require __DIR__ . '/functions/spatial-relationships-measurement.php',
    require __DIR__ . '/functions/geometry-processing.php',
    require __DIR__ . '/functions/miscellaneous-functions.php'
);

$functionIndex = [
    [
        'title' => 'PostgreSQL PostGIS Geometry/Geography/Box Types',
        'anchor' => 'PostGIS_Types',
        'functions' => array_keys(require __DIR__ . '/functions/postgis-types.php'),
    ],
    [
        'title' => 'Geometry Constructors',
        'anchor' => 'Geometry_Constructors',
        'functions' => array_keys(require __DIR__ . '/functions/geometry-constructors.php'),
    ],
    [
        'title' => 'Geometry Accessors',
        'anchor' => 'Geometry_Accessors',
        'functions' => array_keys(require __DIR__ . '/functions/geometry-accessors.php'),
    ],
    [
        'title' => 'Geometry Editors',
        'anchor' => 'Geometry_Editors',
        'functions' => array_keys(require __DIR__ . '/functions/geometry-editors.php'),
    ],
    [
        'title' => 'Geometry Outputs',
        'anchor' => 'Geometry_Outputs',
        'functions' => array_keys(require __DIR__ . '/functions/geometry-outputs.php'),
    ],
    [
        'title' => 'Spatial Relationships and Measurements',
        'anchor' => 'Spatial_Relationships_Measurements',
        'functions' => array_keys(require __DIR__ . '/functions/spatial-relationships-measurement.php'),
    ],
    [
        'title' => 'Geometry Processing',
        'anchor' => 'Geometry_Processing',
        'functions' => array_keys(require __DIR__ . '/functions/geometry-processing.php'),
    ],
    [
        'title' => 'Miscellaneous Functions',
        'anchor' => 'Miscellaneous_Functions',
        'functions' => array_keys(require __DIR__ . '/functions/miscellaneous-functions.php'),
    ],
];

$srcPath = __DIR__ . '/../src/Functions';
$testPath = __DIR__ . '/../tests/Functions';
$docsPath = __DIR__ . '/../docs';

function get_function_src_class_code($name, $options)
{
    $totalArguments = $options['total_arguments'] ?? 0;
    $requiredArguments = $options['required_arguments'] ?? 0;
    ob_start(); ?>

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

final class <?php echo $name; ?> extends FunctionNode
{
    protected array $expressions = [];

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
<?php if ($totalArguments > 0) { ?>
    <?php for ($i = 0; $i < $requiredArguments; ++$i) { ?>
        <?php if ($i > 0) { ?>

        $parser->match(Lexer::T_COMMA);
        <?php } ?>

        $this->expressions[] = $parser->ArithmeticFactor();
    <?php } ?>
    <?php for ($i = 0, $j = $totalArguments - $requiredArguments; $i < $j; ++$i) { ?>
        <?php if (0 === $i) { ?>

        $lexer = $parser->getLexer();
        <?php } ?>

        if ($lexer->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->expressions[] = $parser->ArithmeticFactor();
        }
    <?php } ?>
<?php } ?>

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
<?php if ($totalArguments > 0) { ?>
        $arguments = [];

        /** @var Node $expression */
        foreach ($this->expressions as $expression) {
            $arguments[] = $expression->dispatch($sqlWalker);
        }

        return '<?php echo $name; ?>(' . implode(', ', $arguments) . ')';
<?php } else { ?>

        return '<?php echo $name; ?>()';
<?php } ?>
    }
}
<?php

    return ob_get_clean();
}

function get_function_test_class_code($name, $options)
{
    $queries = $options['tests']['queries'] ?? [];
    ob_start(); ?>

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\Entity\PointsEntity;

/**
 * @group orm
 * @group functions
<?php foreach ($options['tests']['groups'] ?? [] as $group) { ?>
 * @group <?php echo $group; ?>

<?php } ?>
 */
final class <?php echo $name; ?>Test extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->_setUpEntitySchema([
            PointsEntity::class,
        ]);

        $em = $this->_getEntityManager();

        $entity = new PointsEntity([
            'text' => 'foo',
            'geometry' => 'POINT(1 1)',
            'point' => 'POINT(1 1)',
            'point2d' => 'SRID=3785;POINT(1 1)',
            'point3dz' => 'SRID=3785;POINT(1 1 1)',
            'point3dm' => 'SRID=3785;POINTM(1 1 1)',
            'point4d' => 'SRID=3785;POINT(1 1 1 1)',
            'point2dNullable' => null,
            'point2dNoSrid' => 'POINT(1 1)',
            'geography' => 'SRID=4326;POINT(1 1)',
            'pointGeography2d' => 'SRID=4326;POINT(1 1)',
            'pointGeography2dSrid' => 'POINT(1 1)',
        ]);

        $em->persist($entity);
        $em->flush();
        $em->clear();
    }
<?php foreach ($queries as $index => $query) { ?>

<?php if (isset($query['groups'])) { ?>
    /**
<?php foreach ($query['groups'] as $group) { ?>
     * @group <?php echo $group; ?>

<?php } ?>
     */
<?php } ?>
    public function testQuery<?php echo $index + 1; ?>(): void
    {
        $query = $this->_getEntityManager()->createQuery(<?php echo var_export(str_replace('{function}', $name, $query['sql']) . ' FROM Jsor\Doctrine\PostGIS\Entity\PointsEntity point'); ?>);

        $result = $query->getSingleResult();

        array_walk_recursive($result, static function (&$data): void {
            if (is_resource($data)) {
                $data = stream_get_contents($data);

                if (false !== ($pos = strpos($data, 'x'))) {
                    $data = substr($data, $pos + 1);
                }
            }
<?php if ('numeric' === ($options['return_type'] ?? null)) { ?>

            $data = (float) $data;
<?php } else { ?>

            if (is_string($data)) {
                $data = trim($data);
            }
<?php } ?>
        });

        $expected = <?php echo var_export($query['result'], true); ?>;

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }
<?php } ?>
}
<?php

    return ob_get_clean();
}

function get_configurator_class_code($functions)
{
    ob_start(); ?>

declare(strict_types=1);

/* This file is auto-generated. Don't edit directly! */

namespace Jsor\Doctrine\PostGIS\Functions;

use Doctrine\ORM\Configuration;

final class Configurator
{
    public static function configure(Configuration $configuration): void
    {
<?php foreach ($functions as $name => $options) { ?>
<?php
    if (isset($options['alias_for'])) {
        $options = array_replace_recursive($functions[$options['alias_for']], $options);
    }
    ?>
        $configuration->addCustom<?php echo ucfirst($options['return_type'] ?? 'String'); ?>Function('<?php echo $name; ?>', <?php echo $name; ?>::class);
<?php } ?>
    }
}
<?php

        return ob_get_clean();
}

function normalize_versioned_groups(array $queries): array
{
    $postGisVersions = ['3.0', '3.1', '3.2', '3.3', '3.4'];

    $queryGroups = [];
    foreach ($queries as $query) {
        if (!isset($query['groups'])) {
            continue;
        }
        foreach ($query['groups'] as $group) {
            $version = str_replace('postgis-', '', $group);
            if (in_array($version, $postGisVersions, true)) {
                $queryGroups[$version] = $version;
            }
        }
    }
    rsort($queryGroups, SORT_NUMERIC);

    $highest = reset($queryGroups);
    if (false === $highest) {
        return $queries;
    }

    $needed = array_map(fn ($v) => "postgis-$v", array_filter($postGisVersions, fn ($v) => $v > $highest));
    foreach ($queries as &$query) {
        if (!isset($query['groups'])) {
            continue;
        }
        foreach ($query['groups'] as $group) {
            if ($group === "postgis-$highest") {
                $query['groups'] = [...$query['groups'], ...$needed];
            }
        }
        $query['groups'][] = 'versioned';
    }

    return $queries;
}

foreach ($functions as $name => $options) {
    $srcFile = $srcPath . '/' . $name . '.php';
    $testFile = $testPath . '/' . $name . 'Test.php';

    if (isset($options['alias_for'])) {
        $options = array_replace_recursive($functions[$options['alias_for']], $options);
    }

    $options['tests']['queries'] = normalize_versioned_groups($options['tests']['queries']);

    file_put_contents($srcFile, "<?php\n\n" . get_function_src_class_code($name, $options));
    file_put_contents($testFile, "<?php\n\n" . get_function_test_class_code($name, $options));
}

file_put_contents(
    $srcPath . '/Configurator.php',
    "<?php\n\n" . get_configurator_class_code($functions)
);

passthru(__DIR__ . '/../vendor/bin/php-cs-fixer --verbose --config=' . __DIR__ . '/../.php-cs-fixer.dist.php fix');

$md = <<<MD
Function Index
==

This is a complete list of all supported functions which can be use with the
[Doctrine Query Language](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html)
(DQL).

For more information about how to setup and use these functions, refer to the
[DQL Functions documentation](../README.md#dql-functions) of this library.


MD;

foreach ($functionIndex as $section) {
    $md .= <<<MD
* [{$section['title']}](#{$section['anchor']})

MD;
}

foreach ($functionIndex as $section) {
    $md .= <<<MD

<a name="{$section['anchor']}"></a>
## [{$section['title']}](https://postgis.net/docs/reference.html#{$section['anchor']})


MD;
    foreach ($section['functions'] as $func) {
        $md .= <<<MD
* [$func](https://postgis.net/docs/$func.html)

MD;
    }
}

file_put_contents($docsPath . '/function-index.md', $md);
