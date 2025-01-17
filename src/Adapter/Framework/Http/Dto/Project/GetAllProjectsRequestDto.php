<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Project;

use Symfony\Component\HttpFoundation\Request;

final class GetAllProjectsRequestDto
{
    public function __construct()
    {
    }

    public static function createFromRequest(Request $request): self
    {
        return new self();
    }
}