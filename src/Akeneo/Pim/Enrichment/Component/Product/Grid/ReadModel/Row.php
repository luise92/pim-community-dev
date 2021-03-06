<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Component\Product\Grid\ReadModel;

use Akeneo\Pim\Enrichment\Component\Product\Model\ValueCollection;
use Akeneo\Pim\Enrichment\Component\Product\Model\ValueCollectionInterface;
use Akeneo\Pim\Enrichment\Component\Product\Value\MediaValue;

/**
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
final class Row
{
    private const PRODUCT_TYPE = 'product';
    private const PRODUCT_MODEL_TYPE = 'product_model';

    /** @var string */
    private $identifier;

    /** @var string */
    private $familyCode;

    /** @var string[] */
    private $groupCodes;

    /** @var boolean */
    private $enabled;

    /** @var \DateTimeInterface */
    private $created;

    /** @var \DateTimeInterface */
    private $updated;

    /** @var string */
    private $label;

    /** @var null|MediaValue */
    private $image;

    /** @var null|int */
    private $completeness;

    /** @var string */
    private $documentType;

    /** @var integer */
    private $technicalId;

    /** @var string */
    private $searchId;

    /** @var bool */
    private $checked;

    /** @var array */
    private $childrenCompleteness;

    /** @var null|string */
    private $parent;

    /** @var ValueCollectionInterface */
    private $values;

    /**
     * @param string                   $identifier
     * @param null|string              $family
     * @param string[]                 $groupCodes
     * @param bool|null                $enabled
     * @param \DateTimeInterface       $created
     * @param \DateTimeInterface       $updated
     * @param string                   $label
     * @param null|MediaValue          $image
     * @param null|int                 $completeness
     * @param string                   $documentType
     * @param int                      $technicalId
     * @param string                   $searchId
     * @param bool                     $checked
     * @param array                    $childrenCompleteness
     * @param null|string              $parentCode
     * @param ValueCollectionInterface $values
     */
    private function __construct(
        string $identifier,
        ?string $family,
        array $groupCodes,
        ?bool $enabled,
        \DateTimeInterface $created,
        \DateTimeInterface $updated,
        ?string $label,
        ?MediaValue $image,
        ?int $completeness,
        string $documentType,
        int $technicalId,
        string $searchId,
        ?bool $checked,
        array $childrenCompleteness,
        ?string $parentCode,
        ValueCollectionInterface $values
    ) {
        $this->identifier = $identifier;
        $this->familyCode = $family;
        $this->groupCodes = $groupCodes;
        $this->enabled = $enabled;
        $this->created = $created;
        $this->updated = $updated;
        $this->label = $label;
        $this->image = $image;
        $this->completeness = $completeness;
        $this->documentType = $documentType;
        $this->technicalId = $technicalId;
        $this->searchId = $searchId;
        $this->checked = $checked;
        $this->childrenCompleteness = $childrenCompleteness;
        $this->parent = $parentCode;
        $this->values = $values;
    }

    public static function fromProduct(
        string $identifier,
        ?string $familyCode,
        array $groupCodes,
        ?bool $enabled,
        \DateTimeInterface $created,
        \DateTimeInterface $updated,
        string $label,
        ?MediaValue $image,
        ?int $completeness,
        int $technicalId,
        ?string $parentCode,
        ValueCollection $values
    ):self {
        return new self(
            $identifier,
            $familyCode,
            $groupCodes,
            $enabled,
            $created,
            $updated,
            $label,
            $image,
            $completeness,
            self::PRODUCT_TYPE,
            $technicalId,
            sprintf('%s_%s', self::PRODUCT_TYPE, $technicalId),
            true,
            [],
            $parentCode,
            $values
        );
    }

    public static function fromProductModel(
        string $code,
        string $familyCode,
        \DateTimeInterface $created,
        \DateTimeInterface $updated,
        string $label,
        ?MediaValue $image,
        int $technicalId,
        array $childrenCompleteness,
        ?string $parent,
        ValueCollection $values
    ):self {
        return new self(
            $code,
            $familyCode,
            [],
            false,
            $created,
            $updated,
            $label,
            $image,
            null,
            self::PRODUCT_MODEL_TYPE,
            $technicalId,
            sprintf('%s_%s', self::PRODUCT_MODEL_TYPE, $technicalId),
            true,
            $childrenCompleteness,
            $parent,
            $values
        );
    }

    /**
     * @return string
     */
    public function identifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return null|string
     */
    public function familyCode(): ?string
    {
        return $this->familyCode;
    }

    /**
     * @return string[]
     */
    public function groupCodes(): array
    {
        return $this->groupCodes;
    }

    /**
     * @return bool|null
     */
    public function enabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @return \DateTimeInterface
     */
    public function created(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return \DateTimeInterface
     */
    public function updated(): \DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @return null|MediaValue
     */
    public function image(): ?MediaValue
    {
        return $this->image;
    }

    /**
     * @return null|int
     */
    public function completeness(): ?int
    {
        return $this->completeness;
    }

    /**
     * @return string
     */
    public function documentType(): string
    {
        return $this->documentType;
    }

    /**
     * @return int
     */
    public function technicalId(): int
    {
        return $this->technicalId;
    }

    /**
     * @return string
     */
    public function searchId(): string
    {
        return $this->searchId;
    }

    /**
     * @return bool
     */
    public function checked(): bool
    {
        return $this->checked;
    }

    /**
     * @return array
     */
    public function childrenCompleteness(): array
    {
        return $this->childrenCompleteness;
    }

    /**
     * @return null|string
     */
    public function parentCode(): ?string
    {
        return $this->parent;
    }

    /**
     * @return ValueCollectionInterface
     */
    public function values(): ValueCollectionInterface
    {
        return $this->values;
    }
}
