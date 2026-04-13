<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(FactoryMustNotBeUsedDirectly::class)]
class FactoryMustNotBeUsedDirectlyTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;

    #[Test]
    public function it_replaces_factory_new_with_model_factory(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/DirectFactoryUsage.php'));
        $rule = $this->resolveRule(FactoryMustNotBeUsedDirectly::class);

        $staticCall = $this->findStaticCallInMethod($classNode, 'usingNew');
        $this->assertNotNull($staticCall);

        $result = $rule->refactor($staticCall);

        $this->assertInstanceOf(StaticCall::class, $result);
        $this->assertStringContainsString('ValidModel', $result->class->toString());
        $this->assertSame('factory', $result->name->name);
    }

    #[Test]
    public function it_replaces_new_factory_with_model_factory(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/DirectFactoryUsage.php'));
        $rule = $this->resolveRule(FactoryMustNotBeUsedDirectly::class);

        $newExpr = $this->findNewExprInMethod($classNode, 'usingConstructor');
        $this->assertNotNull($newExpr);

        $result = $rule->refactor($newExpr);

        $this->assertInstanceOf(StaticCall::class, $result);
        $this->assertStringContainsString('ValidModel', $result->class->toString());
        $this->assertSame('factory', $result->name->name);
    }

    #[Test]
    public function it_does_not_modify_model_factory_calls(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/DirectFactoryUsage.php'));
        $rule = $this->resolveRule(FactoryMustNotBeUsedDirectly::class);

        $staticCall = $this->findStaticCallInMethod($classNode, 'usingModelFactory');
        $this->assertNotNull($staticCall);

        $result = $rule->refactor($staticCall);

        $this->assertNull($result);
    }

    private function findStaticCallInMethod(mixed $classNode, string $methodName): null|StaticCall
    {
        foreach ($classNode->stmts as $stmt) {
            if (! $stmt instanceof ClassMethod || $stmt->name->toString() !== $methodName) {
                continue;
            }

            foreach ($stmt->stmts ?? [] as $innerStmt) {
                if ($innerStmt instanceof Expression && $innerStmt->expr instanceof StaticCall) {
                    return $innerStmt->expr;
                }

                if ($innerStmt instanceof Expression) {
                    $expr = $innerStmt->expr;

                    if (property_exists($expr, 'expr') && $expr->expr instanceof StaticCall) {
                        return $expr->expr;
                    }
                }
            }
        }

        return null;
    }

    private function findNewExprInMethod(mixed $classNode, string $methodName): null|\PhpParser\Node\Expr\New_
    {
        foreach ($classNode->stmts as $stmt) {
            if (! $stmt instanceof ClassMethod || $stmt->name->toString() !== $methodName) {
                continue;
            }

            foreach ($stmt->stmts ?? [] as $innerStmt) {
                if ($innerStmt instanceof Expression && $innerStmt->expr instanceof \PhpParser\Node\Expr\New_) {
                    return $innerStmt->expr;
                }

                if ($innerStmt instanceof Expression) {
                    $expr = $innerStmt->expr;

                    if (property_exists($expr, 'expr') && $expr->expr instanceof \PhpParser\Node\Expr\New_) {
                        return $expr->expr;
                    }
                }
            }
        }

        return null;
    }
}
