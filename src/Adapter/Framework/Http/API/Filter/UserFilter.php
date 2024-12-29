<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\API\Filter;

use App\Domain\Model\User;
use InvalidArgumentException;

use function filter_var;
use function is_numeric;
use function sprintf;

use const FILTER_VALIDATE_EMAIL;

class UserFilter extends EntityFilter
{
    public readonly ?string $email;
    public readonly ?string $companyId;

    public function __construct(
        int $page,
        int $limit,
        string $sort,
        string $order,
        ?string $companyId,
        ?string $name,
        ?string $email,
    ) {
        parent::__construct($page, $limit, $sort, $order, $name);
        $this->email = $email;
        $this->companyId = $companyId;
    }

    public function validateCompanyId(): void
    {
        if ($this->companyId !== null && !is_numeric($this->companyId)) {
            throw new InvalidArgumentException(sprintf('Invalid company id format [%s]', $this->companyId));
        }

        if ($this->companyId !== null && (mb_strlen($this->companyId) !== User::ID_LENGTH)) {
            throw new InvalidArgumentException(sprintf('Invalid company id length [%s]', $this->companyId));
        }
    }

    public function validateEmail(): void
    {
        if ($this->email !== null && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('Invalid email format [%s]', $this->email));
        }
    }
}
