<?php


namespace Jsor\Doctrine\PostGIS\Types\Base;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Implementation of PostgreSql TEXT[] data type.
 * This type is used internally by postgis.
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 * @author Maxime Veber <nek.dev+github@gmail.com>
 *
 * Originally from `martin-georgiev/postgresql-for-doctrine`, if you're using this library as well, prefer its namespace.
 */
class TextArray extends Type
{
    /**
     * @var string
     */
    protected const TYPE_NAME = 'text[]';


    public function getName(): string
    {
        if (null === self::TYPE_NAME) {
            throw new \LogicException(\sprintf('Doctrine type defined in class "%s" has no meaningful value for TYPE_NAME constant', self::class));
        }

        return self::TYPE_NAME;
    }

    /**
     * Converts a value from its PHP representation to its database representation of the type.
     *
     * @param array|null $value the value to convert
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return $this->transformToPostgresTextArray($value);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return false;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        if (null === self::TYPE_NAME) {
            throw new \LogicException(\sprintf('Doctrine type defined in class "%s" has no meaningful value for TYPE_NAME constant', self::class));
        }

        return $platform->getDoctrineTypeMapping(self::TYPE_NAME);
    }

    protected function transformToPostgresTextArray(array $phpTextArray): string
    {
        if (!\is_array($phpTextArray)) {
            throw new \InvalidArgumentException(\sprintf('Value %s is not an array', \var_export($phpTextArray, true)));
        }
        if (empty($phpTextArray)) {
            return '{}';
        }

        return self::transformPHPArrayToPostgresTextArray($phpTextArray);
    }

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param string|null $value the value to convert
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?array
    {
        if ($value === null) {
            return null;
        }

        return $this->transformFromPostgresTextArray($value);
    }

    protected function transformFromPostgresTextArray(string $postgresValue): array
    {
        if ($postgresValue === '{}') {
            return [];
        }

        return self::transformPostgresTextArrayToPHPArray($postgresValue);
    }

    /**
     * From https://github.com/martin-georgiev/postgresql-for-doctrine/blob/7e04b0024bae8072b92deabc236d24c7367a9fec/src/MartinGeorgiev/Utils/DataStructure.php#L14
     */
    public static function transformPostgresTextArrayToPHPArray(string $postgresArray): array
    {
        $transform = static function (string $textArrayToTransform): array {
            $indicatesMultipleDimensions = \mb_strpos($textArrayToTransform, '},{') !== false
                || \mb_strpos($textArrayToTransform, '{{') === 0;
            if ($indicatesMultipleDimensions) {
                throw new \InvalidArgumentException('Only single-dimensioned arrays are supported');
            }

            $phpArray = \str_getcsv(\trim($textArrayToTransform, '{}'));
            foreach ($phpArray as $i => $text) {
                if ($text === null) {
                    unset($phpArray[$i]);

                    break;
                }

                $isInteger = \is_numeric($text) && ''.(int) $text === $text;
                if ($isInteger) {
                    $phpArray[$i] = (int) $text;

                    continue;
                }

                $isFloat = \is_numeric($text) && ''.(float) $text === $text;
                if ($isFloat) {
                    $phpArray[$i] = (float) $text;

                    continue;
                }

                $phpArray[$i] = \str_replace('\"', '"', $text);
            }

            return $phpArray;
        };

        return $transform($postgresArray);
    }

    /**
     * From https://github.com/martin-georgiev/postgresql-for-doctrine/blob/7e04b0024bae8072b92deabc236d24c7367a9fec/src/MartinGeorgiev/Utils/DataStructure.php#L14
     */
    public static function transformPHPArrayToPostgresTextArray(array $phpArray): string
    {
        $transform = static function (array $phpArrayToTransform): string {
            $result = [];
            foreach ($phpArrayToTransform as $text) {
                if (\is_array($text)) {
                    throw new \InvalidArgumentException('Only single-dimensioned arrays are supported');
                }

                if (\is_numeric($text) || \ctype_digit($text)) {
                    $escapedText = $text;
                } else {
                    $escapedText = '"'.\str_replace('"', '\"', $text).'"';
                }
                $result[] = $escapedText;
            }

            return '{'.\implode(',', $result).'}';
        };

        return $transform($phpArray);
    }
}
