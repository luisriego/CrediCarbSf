<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\RequestTransformer;

use App\Domain\Exception\InvalidArgumentException;
use JsonException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

use function in_array;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class RequestTransformer
{
    private const ALLOWED_CONTENT_TYPE = 'application/json';

    private const METHODS_TO_DECODE = [
        Request::METHOD_POST,
        Request::METHOD_PUT,
        Request::METHOD_PATCH,
    ];

    public function transform(Request $request): void
    {
        if (self::ALLOWED_CONTENT_TYPE !== $request->headers->get('Content-Type')) {
            throw InvalidArgumentException::createFromMessage(sprintf('[%s] is the only Content-Type allowed', self::ALLOWED_CONTENT_TYPE));
        }

        if (in_array($request->getMethod(), self::METHODS_TO_DECODE, true)) {
            try {
                $request->request = new ParameterBag((array) json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR));
            } catch (JsonException $ex) {
                throw InvalidArgumentException::createFromMessage('Invalid JSON payload');
            }
        }
    }
}
