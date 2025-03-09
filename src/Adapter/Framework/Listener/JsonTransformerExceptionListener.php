<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Listener;

use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\Project\ProjectAlreadyExistsException;
use App\Domain\Exception\ResourceNotFoundException;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JsonTransformerExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        $data = [
            'class' => $e::class,
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $e->getMessage(),
        ];

        if ($e instanceof ProjectAlreadyExistsException) {
            $data['code'] = Response::HTTP_BAD_REQUEST;
        }

        if ($e instanceof ResourceNotFoundException) {
            $data['code'] = Response::HTTP_NOT_FOUND;
        }

        if ($e instanceof InvalidArgumentException || $e instanceof JsonException) {
            $data['code'] = Response::HTTP_BAD_REQUEST;
        }

        if ($e instanceof AccessDeniedException) {
            $data['code'] = Response::HTTP_FORBIDDEN;
        }

        $response = new JsonResponse($data, $data['code']);

        $event->setResponse($response);
    }
}
