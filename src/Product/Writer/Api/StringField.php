<?php declare(strict_types=1);

namespace Shopware\Product\Writer\Api;

use Shopware\Framework\Validation\ConstraintBuilder;

class StringField extends Field
{
    public function __construct(string $name, string $storageName, ConstraintBuilder $constraintBuilder)
    {
        parent::__construct(
            $name,
            $storageName,
            $constraintBuilder->isNotBlank()->isString()->getConstraints(),
            $constraintBuilder->isString()->isShorterThen(255)->getConstraints()
        );
    }

    public function getFilters()
    {
        return [
            new HtmlFilter(),
        ];
    }
}