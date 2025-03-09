<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\ShoppingCart;

use App\Adapter\Framework\Http\Dto\RequestDto;
use DateMalformedStringException;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;

class CreateDiscountRequestDto implements RequestDto
{
    public string $creatorId;
    public string $amount;
    public DateTimeImmutable $expiresAt;

    /**
     * @throws DateMalformedStringException
     */
    public function __construct(Request $request)
    {
        $this->creatorId = $request->request->get('creatorId');
        $this->amount = $request->request->get('amount');
        $this->expiresAt = new DateTimeImmutable($request->request->get('expiresAt'));
    }
}
