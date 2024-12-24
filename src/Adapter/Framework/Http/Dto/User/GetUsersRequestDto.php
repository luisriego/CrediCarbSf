<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\User;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

final class GetUsersRequestDto implements RequestDto
{
    public readonly int $page;
    public readonly int $limit;
    public readonly ?string $condoId;
    public readonly string $sort;
    public readonly string $order;
    public readonly ?string $name;

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
