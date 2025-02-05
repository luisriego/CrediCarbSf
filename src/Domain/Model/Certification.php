<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Common\CertificationType;
use App\Domain\Repository\CertificationRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class Certification
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description;

    private ?CertificationType $type;

    private ?CertificationAuthority $authority;
}