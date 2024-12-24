<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\Dto\Condo\ActivateCondoRequestDto;
use App\Adapter\Framework\Security\Voter\CondoVoter;
use App\Application\UseCase\Condo\ActivateCondo\ActivateCondo;
use App\Application\UseCase\Condo\ActivateCondo\Dto\ActivateCondoInputDto;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivateCondoController extends AbstractController
{
    public function __construct(
        private readonly ActivateCondo $useCase,
    ) {
    }

    #[Route('/condo/activate', name: 'activate_condo', methods: ['PUT'])]
    public function __invoke(ActivateCondoRequestDto $request): Response
    {
        $inputDto = ActivateCondoInputDto::create($request->id);

        /** @var User $user */
        $user = $this->getUser();

        $this->denyAccessUnlessGranted(CondoVoter::ACTIVATE_CONDO, $user->getId());

        $responseDto = $this->useCase->handle($inputDto);

        return $this->json($responseDto->condoData);
    }
}
