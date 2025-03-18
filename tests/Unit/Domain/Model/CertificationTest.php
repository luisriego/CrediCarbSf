<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Certification;
use App\Domain\Model\CertificationAuthority;
use App\Domain\Model\CertificationTypeEntity;
use PHPUnit\Framework\TestCase;

class CertificationTest extends TestCase
{
    public function testCreateCertification(): void
    {
        $name = 'ISO 9001';
        $description = 'Quality Management System';
        $type = $this->createMock(CertificationTypeEntity::class);
        $authority = $this->createMock(CertificationAuthority::class);

        $certification = new Certification($name, $description, $type, $authority);

        $this->assertInstanceOf(Certification::class, $certification);
        $this->assertEquals($name, $certification->getName());
        $this->assertEquals($description, $certification->getDescription());
        $this->assertSame($type, $certification->getType());
        $this->assertSame($authority, $certification->getAuthority());
        $this->assertNotNull($certification->getId());
        $this->assertNotNull($certification->getCreatedOn());
        $this->assertFalse($certification->isActive());
    }

    public function testSetName(): void
    {
        $certification = $this->createCertification();
        $newName = 'ISO 14001';
        $certification->setName($newName);

        $this->assertEquals($newName, $certification->getName());
    }

    public function testSetDescription(): void
    {
        $certification = $this->createCertification();
        $newDescription = 'Environmental Management System';
        $certification->setDescription($newDescription);

        $this->assertEquals($newDescription, $certification->getDescription());
    }

    public function testSetType(): void
    {
        $certification = $this->createCertification();
        $newType = $this->createMock(CertificationTypeEntity::class);
        $certification->setType($newType);

        $this->assertSame($newType, $certification->getType());
    }

    public function testSetAuthority(): void
    {
        $certification = $this->createCertification();
        $newAuthority = $this->createMock(CertificationAuthority::class);
        $certification->setAuthority($newAuthority);

        $this->assertSame($newAuthority, $certification->getAuthority());
    }

    public function testSetAuthorityToNull(): void
    {
        $authority = $this->createMock(CertificationAuthority::class);
        $certification = $this->createCertification($authority);
        $certification->setAuthority(null);

        $this->assertNull($certification->getAuthority());
    }

    public function testToArray(): void
    {
        $name = 'ISO 9001';
        $description = 'Quality Management System';
        $type = $this->createMock(CertificationTypeEntity::class);
        $type->method('toArray')->willReturn(['id' => 'type-id', 'name' => 'Type Name']);
        $authority = $this->createMock(CertificationAuthority::class);
        $authority->method('toArray')->willReturn(['id' => 'authority-id', 'name' => 'Authority Name']);

        $certification = new Certification($name, $description, $type, $authority);
        $array = $certification->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('authority', $array);
        $this->assertEquals($name, $array['name']);
        $this->assertEquals($description, $array['description']);
        $this->assertEquals(['id' => 'type-id', 'name' => 'Type Name'], $array['type']);
        $this->assertEquals(['id' => 'authority-id', 'name' => 'Authority Name'], $array['authority']);
    }

    private function createCertification(?CertificationAuthority $authority = null): Certification
    {
        $name = 'ISO 9001';
        $description = 'Quality Management System';
        $type = $this->createMock(CertificationTypeEntity::class);
        $authority ??= $this->createMock(CertificationAuthority::class);

        return new Certification($name, $description, $type, $authority);
    }
}
