<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\ShoppingCart;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class AddItemToShoppingCartRequestDto implements RequestDto
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public ?string $ownerId;

    #[Assert\NotBlank]
    #[Assert\Uuid]
    public ?string $projectId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $quantity;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public string $price;

    public function __construct(Request $request)
    {
        $this->ownerId = $request->get('ownerId');
        $this->projectId = $request->get('projectId');
        $this->quantity = $request->get('quantity');
        $this->price = $request->get('price');
    }
}
