<?php

declare(strict_types=1);

namespace Netgen\IbexaFieldTypeEnhancedLinkBundle\Templating\Twig\Extension;

use eZ\Publish\API\Repository\Repository;

class FieldTypeRuntime
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function hasLocation(int $reference): bool
    {
        return $this->repository->sudo(
            fn (): bool => $this->repository->getContentService()->loadContentInfo($reference)->mainLocationId !== null
        );
    }
}
