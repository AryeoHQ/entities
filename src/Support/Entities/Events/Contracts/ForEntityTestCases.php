<?php

declare(strict_types=1);

namespace Support\Entities\Events\Contracts;

use Illuminate\Broadcasting\Channel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @mixin TestCase
 */
trait ForEntityTestCases
{
    #[Test]
    public function it_can_broadcast(): void
    {
        $channels = $this->event->broadcastOn();

        $this->assertNotEmpty($channels);
        $this->assertContainsOnlyInstancesOf(Channel::class, $channels);
    }
}
