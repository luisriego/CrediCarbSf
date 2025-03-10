<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Project;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class GetProjectsByCompanyRequestDto implements RequestDto
{
    public int $page;
    public int $limit;
    public ?string $company;
    public string $sort;
    public string $order;
    public ?string $name;

    public function __construct(Request $request)
    {
        $this->page = $request->query->getInt('page');
        $this->limit = $request->query->getInt('limit');
        $this->company = $request->query->get('company');
        $this->sort = $request->query->getAlpha('sort');
        $this->order = $request->query->getAlpha('order');
        $this->name = $request->query->get('name');
    }
}
