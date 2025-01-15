<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

use App\Domain\ValueObjects\Uuid;
use App\Tests\Functional\FunctionalTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TrackProgressControllerTest extends FunctionalTestBase
{
    private const ENDPOINT = '/api/project/%s/progress';

    protected string $projectId;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testTrackProgressSuccessfully(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT, $this->projectId),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
        self::assertArrayHasKey('currentStatus', $responseData);
        self::assertArrayHasKey('milestones', $responseData);
        self::assertArrayHasKey('completionPercentage', $responseData);
        self::assertEquals(0, $responseData['completionPercentage']);
        self::assertTrue($responseData['milestones']['planning']);
    }

    public function testTrackProgressProjectNotFound(): void
    {
        $id = Uuid::random()->value();

        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT, $id),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertArrayHasKey('message', $responseData);
        self::assertEquals(sprintf('Resource of type [App\Domain\Model\Project] with ID [%s] not found', $id), $responseData['message']);
    }

    public function testTrackProgressInvalidUid(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT, '49f09d49-416b-44d7-a983-847fc65300'),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertArrayHasKey('message', $responseData);
        self::assertEquals('Invalid UID format', $responseData['message']);
    }

    public function testTrackProgressEmptyUid(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT, ''),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testTrackProgressNullUid(): void
    {
        self::$authenticatedClient->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT, ''),
        );

        $response = self::$authenticatedClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());    
    }

    public function testTrackProgressUnauthorized(): void
    {
        self::$baseClient->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT, $this->projectId),
        );

        $response = self::$baseClient->getResponse();
        $responseData = json_decode($response->getContent(), true);

        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertArrayHasKey('message', $responseData);
        self::assertEquals('Access Denied.', $responseData['message']);
    }
}