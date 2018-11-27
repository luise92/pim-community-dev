<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Bundle\Storage\Sql\Connector\Writer\File\Flat;

use Doctrine\DBAL\Connection;

/**
 * @author    Benoit Jacquemont <benoit@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GenerateHeadersFromFamilyCodes
{
    /** @var Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Generate all possible headers from the provided attribute codes if not empty
     * or from family codes
     */
    public function __invoke(
        ?array $familyCodes,
        ?array $attributeCodes,
        array $localeCodes,
        string $channelCode
    ) {
        $attributesData = [];
        $rawAttributesData = [];

        if (null !== $attributeCodes) {
            $attributesDataSql = <<<SQL
                SELECT a.code,
                       a.is_localizable,
                       a.is_scopable,
                       GROUP_CONCAT(l.code) AS locale_specific
                FROM pim_catalog_attribute
                  JOIN pim_catalog_attribute a
                  LEFT JOIN pim_catalog_attribute_locale al ON al.attribute_id = a.id
                  LEFT JOIN pim_catalog_locale l on l.id = al.locale_id
                WHERE a.code IN (:attributeCodes)
                GROUP BY a.id;
SQL;

            $rawAttributesData = $this->connection->executeQuery(
                $attributesDataSql,
                ['attributeCodes' => $attributeCodes],
                ['attributeCodes' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
            )->fetchAll();

        } elseif (null !== $familyCodes) {
            $attributesDataSql = <<<SQL
                SELECT a.code,
                       a.is_localizable,
                       a.is_scopable,
                       GROUP_CONCAT(l.code) AS locale_specific
                FROM pim_catalog_family f
                  JOIN pim_catalog_family_attribute fa ON fa.family_id = f.id
                  JOIN pim_catalog_attribute a ON a.id = fa.attribute_id
                  LEFT JOIN pim_catalog_attribute_locale al ON al.attribute_id = a.id
                  LEFT JOIN pim_catalog_locale l on l.id = al.locale_id
                WHERE f.code IN (:familyCodes)
                GROUP BY a.id;
SQL;

            $rawAttributesData = $this->connection->executeQuery(
                $attributesDataSql,
                ['familyCodes' => $familyCodes],
                ['familyCodes' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
            )->fetchAll();
        }

        foreach($rawAttributesData as $rawAttributeData) {
            $attributesData[] = [
                "code" => $rawAttributeData["code"],
                "is_localizable" => ("1" === $rawAttributeData["is_localizable"]),
                "is_scopable" => ("1" === $rawAttributeData["is_scopable"])
            ];
        }

        return $this->generateHeaderStrings($attributesData, $channelCode, $localeCodes);
    }

    /**
     * Generate headers string from attribute information
     */
    protected function generateHeaderStrings(array $attributesData, string $channelCode, array $localeCodes): array
    {
        $headers = [];

        foreach($attributesData as $attributeData) {
            if (true === $attributeData['is_localizable'] && true === $attributeData['is_scopable']) {
                foreach($localeCodes as $localeCode) {
                    $headers[] = sprintf('%s-%s-%s', $attributeData['code'], $localeCode, $channelCode);
                }
            } elseif (true === $attributeData['is_localizable']) {
                foreach($localeCodes as $localeCode) {
                    $headers[] = sprintf('%s-%s', $attributeData['code'], $localeCode);
                }
            } elseif (true === $attributeData['is_scopable']) {
                $headers[] = sprintf('%s-%s', $attributeData['code'], $channelCode);
            } elseif (!empty($attributeData['specific_to_locales'])) {
                    if (in_array($attributeData['specific_to_locales'], $localeCode)) {
                        $headers[] = $attributeData['code'];
                    }
            } else {
                $headers[] = $attributeData['code'];
            }
        }

        return $headers;
    }
}
