<?php

declare(strict_types=1);

namespace Support\Entities\References\Concerns;

use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Policy;
use Support\Entities\References\Provider;

/**
 * @mixin \PHPUnit\Framework\TestCase
 */
trait EntityTestCases
{
    #[Test]
    public function it_derives_singular_and_plural(): void
    {
        $this->assertSame('Post', $this->subject->singular->toString());
        $this->assertSame('Posts', $this->subject->plural->toString());
    }

    #[Test]
    public function it_derives_namespace(): void
    {
        $this->assertSame('App\\Entities\\Posts', $this->subject->namespace->toString());
    }

    #[Test]
    public function it_derives_fqcn(): void
    {
        $this->assertSame('App\\Entities\\Posts\\Post', $this->subject->fqcn->toString());
    }

    #[Test]
    public function it_derives_variable_name(): void
    {
        $this->assertSame('post', $this->subject->variableName->toString());
    }

    #[Test]
    public function it_derives_file_path(): void
    {
        $this->assertStringEndsWith('Entities/Posts/Post.php', $this->subject->filePath->toString());
    }

    #[Test]
    public function it_derives_test_name_and_path(): void
    {
        $this->assertSame('PostTest', $this->subject->test->name->toString());
        $this->assertStringEndsWith('Entities/Posts/PostTest.php', $this->subject->test->filePath->toString());
    }

    #[Test]
    public function it_provides_a_policy_reference(): void
    {
        $subject = $this->subject;

        $this->assertInstanceOf(Policy::class, $subject->policy);
        $this->assertSame($subject, $subject->policy->entity);
    }

    #[Test]
    public function it_provides_a_provider_reference(): void
    {
        $subject = $this->subject;

        $this->assertInstanceOf(Provider::class, $subject->provider);
        $this->assertSame($subject, $subject->provider->entity);
    }
}
