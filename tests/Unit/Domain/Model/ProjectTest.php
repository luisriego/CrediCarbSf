<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Project;
use App\Domain\Common\ProjectStatus;
use App\Domain\Exception\HttpException;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Model\Company;
use DomainException;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    /** @var Company&\PHPUnit\Framework\MockObject\MockObject $companyMock */
    private $companyMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyMock = $this->createMock(Company::class);
    }

    public function testCreateProject(): void
    {      
        

        $project = Project::create(
            'Test Project',
            'Some description text...',
            '10.0',
            '100',
            '20.0',
            'Carbon',
            $this->companyMock,
        );

        $this->assertSame('Test Project', $project->getName());
        $this->assertSame('Some description text...', $project->getDescription());
        $this->assertSame('10.0', $project->getAreaHa());
        $this->assertSame('100', $project->getQuantity());
        $this->assertSame('20.0', $project->getPrice());
        $this->assertSame('Carbon', $project->getProjectType());
        $this->assertTrue($project->isActive());
        $this->assertEquals(ProjectStatus::PLANNED, $project->getStatus());
    }

    public function testUpdateName(): void
    {
        $project = Project::create('Old Name', 'Some description text...', '5.0', '10', '15.0', 'Carbon', $this->companyMock);
        $project->setName('New Name');

        $this->assertSame('New Name', $project->getName());
    }

    public function testSetStatus(): void
    {
        $project = Project::create('Test Project', 'Some description text...', '5.0', '10', '15.0', 'Other', $this->companyMock);
        $project->setStatus(ProjectStatus::IN_DEVELOPMENT);

        $this->assertEquals(ProjectStatus::IN_DEVELOPMENT, $project->getStatus());
    }

    public function testTrackProgress(): void
    {
        $project = Project::create('Progress Project', 'Some description text...', '5.0', '10', '15.0', 'Other', $this->companyMock);
        $track = $project->trackProgress();

        $this->assertArrayHasKey('currentStatus', $track);
        $this->assertArrayHasKey('milestones', $track);
        $this->assertArrayHasKey('completionPercentage', $track);
    }

    public function testInvalidQuantityThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Project::create('Invalid Quantity', '', '5.0', '0', '15.0', 'Carbon', $this->companyMock);
    }

    public function testDeactivateProject(): void
    {
        $project = Project::create('Deactivate me', 'Some description text...', '10.0', '200', '50.0', 'Carbon', $this->companyMock);

        $project->deactivate(); // Suppose your Project has a deactivate() method
        $this->assertFalse($project->isActive());

        $project->deactivate();
        $this->assertFalse($project->isActive());
    }

    public function testActivateProject(): void
    {
        $project = Project::create('Activate me', 'Some description text...', '10.0', '200', '50.0', 'Carbon', $this->companyMock);

        $project->activate();
        $this->assertTrue($project->isActive());

        $project->activate();
        $this->assertTrue($project->isActive());
    }

    public function testInvalidOwnerThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Project::create('Invalid Owner', '', '5.0', '10', '15.0', 'Carbon', null);
    }
}
