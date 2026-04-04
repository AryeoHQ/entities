<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/** @extends RuleTestCase<EntityDrivenMustUseSerializesModels> */
#[CoversClass(EntityDrivenMustUseSerializesModels::class)]
class EntityDrivenMustUseSerializesModelsTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new EntityDrivenMustUseSerializesModels;
    }

    #[Test]
    public function it_passes_when_entity_driven_uses_serializes_models(): void
    {
        $this->analyse([__DIR__.'/../../../Support/Entities/Events/Provides/EntityDriven.php'], []);
    }
}
