<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\User;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

final readonly class GetUsersRequestDto implements RequestDto
{
    public int $page;
    public int $limit;
    public ?string $condoId;
    public string $sort;
    public string $order;
    public ?string $name;

    public function __construct(Request $request)
    {
        $this->page = $request->query->getInt('page');
        $this->limit = $request->query->getInt('limit');
        $this->condoId = $request->query->get('condoId');
        $this->sort = $request->query->getAlpha('sort');
        $this->order = $request->query->getAlpha('order');
        $this->name = $request->query->get('name');
    }
}
