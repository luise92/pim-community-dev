<?php

namespace Akeneo\Pim\Enrichment\Component\Product\Connector\Writer\File;

use Akeneo\Pim\Enrichment\Bundle\Storage\Sql\Connector\Writer\File\Flat\GenerateHeadersFromFamilyCodes;
use Akeneo\Pim\Structure\Component\Repository\AttributeRepositoryInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters;
use Akeneo\Tool\Component\Buffer\BufferFactory;
use Akeneo\Tool\Component\Connector\ArrayConverter\ArrayConverterInterface;
use Akeneo\Tool\Component\Connector\Writer\File\AbstractItemMediaWriter;
use Akeneo\Tool\Component\Connector\Writer\File\FileExporterPathGeneratorInterface;
use Akeneo\Tool\Component\Connector\Writer\File\FlatItemBufferFlusher;

/**
 * @author    Benoit Jacquemont <benoit@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractEntityWithFamilyFlatWriter extends AbstractItemMediaWriter
{
    /** @var array */
    protected $familyCodes;

    /** @var GenerateHeadersFromFamilyCodes */
    protected $generateHeaders;

    public function __construct(
        ArrayConverterInterface $arrayConverter,
        BufferFactory $bufferFactory,
        FlatItemBufferFlusher $flusher,
        AttributeRepositoryInterface $attributeRepository,
        FileExporterPathGeneratorInterface $fileExporterPath,
        GenerateHeadersFromFamilyCodes $generateHeaders,
        array $mediaAttributeTypes,
        string $jobParamFilePath = self::DEFAULT_FILE_PATH
    ) {
        parent::__construct(
            $arrayConverter,
            $bufferFactory,
            $flusher,
            $attributeRepository,
            $fileExporterPath,
            $mediaAttributeTypes,
            $jobParamFilePath
        );

        $this->generateHeaders = $generateHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize()
    {
        $this->familyCodes = [];

        parent::initialize();
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $items)
    {
        foreach ($items as $item) {
            if (isset($item['family']) && !in_array($item['family'], $this->familyCodes)) {
                $this->familyCodes[] = $item['family'];
            }
        }

        parent::write($items);
    }


    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $parameters = $this->stepExecution->getJobParameters();

        if (null !== $parameters->get('withHeader') && $parameters->get('withHeader')) {
            $headersFromFamilies = $this->getHeadersFromFamilyCodes($this->familyCodes, $parameters);
            $this->flatRowBuffer->addToHeaders($headersFromFamilies);
        }

        parent::flush();
    }

    /**
     * Return additional headers, based on the requested attributes if any,
     * and from the families definition
     */
    protected function getHeadersFromFamilyCodes(?array $familyCodes, JobParameters $parameters): array
    {
        $filters = $parameters->get('filters');

        $localeCodes = $filters['structure']['locales'];
        $channelCode = $filters['structure']['scope'];

        $attributeCodes = null;

        if (isset($filters['structure']['attributes'])
            && !empty($filters['structure']['attributes'])) {
            $attributeCodes = $filters['structure']['attributes'];
        }

        return ($this->generateHeaders)(
            $familyCodes,
            $attributeCodes,
            $localeCodes,
            $channelCode
        );
    }
}
