<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLinkBundle\Form\Field;

use Ibexa\Contracts\Core\Repository\FieldType;
use Netgen\IbexaFieldTypeEnhancedLink\FieldType\Type;
use Netgen\IbexaFieldTypeEnhancedLink\FieldType\Value;
use Symfony\Component\Form\DataTransformerInterface;

use function array_key_exists;
use function is_array;

class FieldValueTransformer implements DataTransformerInterface
{
    private FieldType $fieldType;

    public function __construct(FieldType $fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function transform($value): ?array
    {
        if (!$value instanceof Value) {
            return null;
        }

        if ($value->isTypeExternal()) {
            return [
                'url' => $value->reference,
                'label_external' => $value->label,
                'target_external' => $value->target,
                'link_type' => Type::LINK_TYPE_EXTERNAL,
            ];
        }

        if ($value->isTypeInternal()) {
            return [
                'suffix' => $value->suffix,
                'label_internal' => $value->label,
                'target_internal' => $value->target,
                'id' => $value->reference,
                'link_type' => Type::LINK_TYPE_INTERNAL,
            ];
        }

        return null;
    }

    public function reverseTransform($value): ?Value
    {
        dump($value);
        throw new \RuntimeException('asdf');

        if (is_array($value) && array_key_exists('link_type', $value)) {
            if ($value['link_type'] === Type::LINK_TYPE_INTERNAL) {
                if (isset($value['id'], $value['target_internal'])) {
                    return new Value(
                        $value['id'],
                        $value['label_internal'],
                        $value['target_internal'],
                        $value['suffix'] ?? null,
                    );
                }
            }

            if ($value['link_type'] === Type::LINK_TYPE_EXTERNAL) {
                if (isset($value['url'], $value['target_internal'])) {
                    return new Value(
                        $value['url'],
                        $value['label_external'],
                        $value['target_external'],
                    );
                }
            }
        }

        return $this->fieldType->getEmptyValue();
    }
}