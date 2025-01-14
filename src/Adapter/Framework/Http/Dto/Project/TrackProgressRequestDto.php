<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Project;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

final class TrackProgressRequestDto implements RequestDto
{
    public string $projectId;
    
    public function __construct(Request $request) {
        $this->projectId = $request->get('projectId');
    }
}