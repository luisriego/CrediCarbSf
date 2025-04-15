<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\ShoppingCart;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class CreateDiscountRequestDto implements RequestDto
{
    public string $creatorId;
    public int $amount;
    public string $expiresAt;
    public ?bool $isPercentage = true;
    public ?string $projectId = null;

    public function __construct(Request $request)
    {
        $this->creatorId = $request->request->get('creatorId');
        $this->amount = $request->request->get('amount');
        $this->expiresAt = $request->request->get('expiresAt');
        $this->isPercentage = $request->request->get('isPercentage') ?? true;
        $this->projectId = $request->request->get('projectId');
    }
}
