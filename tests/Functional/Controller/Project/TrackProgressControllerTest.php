<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Project;

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
        // TODO: Implement
    }

    public function testTrackProgressUnauthorized(): void
    {
        // Todo: Implement
    }
}