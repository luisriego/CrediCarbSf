<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Mother;

use App\Domain\Common\ProjectStatus;
use App\Domain\Model\Company;
use App\Domain\Model\Project;
use DateTime;

final class ProjectMother
{
    private const DEFAULT_NAME = 'Test Project';
    private const DEFAULT_DESCRIPTION = 'This is a test project description with sufficient length to pass validation.';
    private const DEFAULT_AREA_HA = 10;
    private const DEFAULT_QUANTITY_IN_KG = 5000;
    private const DEFAULT_PRICE_IN_CENTS = 500000; // 5000.00
    private const DEFAULT_PROJECT_TYPE = 'Agriculture';

    public static function create(
        ?string $name = null,
        ?string $description = null,
        ?int $areaHa = null,
        ?int $quantityInKg = null,
        ?int $priceInCents = null,
        ?string $projectType = null,
        ?Company $owner = null
    ): Project {
        if ($owner === null) {
            $owner = CompanyMother::create();
        }

        return new Project(
            $name ?? self::DEFAULT_NAME,
            $description ?? self::DEFAULT_DESCRIPTION,
            $areaHa ?? self::DEFAULT_AREA_HA,
            $quantityInKg ?? self::DEFAULT_QUANTITY_IN_KG,
            $priceInCents ?? self::DEFAULT_PRICE_IN_CENTS,
            $projectType ?? self::DEFAULT_PROJECT_TYPE,
            $owner
        );
    }

    public static function withOwner(Company $owner): Project
    {
        return self::create(owner: $owner);
    }

    /**
     * @throws \ReflectionException
     */
    public static function withBuyer(Company $buyer): Project
    {
        $project = self::create();
        // Assuming there's a setBuyer method or the buyer is directly accessible
        $reflection = new \ReflectionProperty($project, 'buyer');
        $reflection->setValue($project, $buyer);

        return $project;
    }

    /**
     * @throws \ReflectionException
     */
    public static function withStatus(ProjectStatus $status): Project
    {
        $project = self::create();
        // Assuming there's a setStatus method or the status is directly accessible
        $reflection = new \ReflectionProperty($project, 'status');
        $reflection->setValue($project, $status);

        return $project;
    }

    public static function inactive(): Project
    {
        $project = self::create();
        $project->setIsActive(false);
        return $project;
    }

    /**
     * @throws \ReflectionException
     */
    public static function withCustomDates(DateTime $startDate, DateTime $endDate): Project
    {
        $project = self::create();
        // Assuming there are setStartDate and setEndDate methods
        $reflectionStart = new \ReflectionProperty($project, 'startDate');
        $reflectionStart->setValue($project, $startDate);

        $reflectionEnd = new \ReflectionProperty($project, 'endDate');
        $reflectionEnd->setValue($project, $endDate);

        return $project;
    }
}