<?php

declare(strict_types=1);

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\APIClassGeneratedEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\APIClassGenerationEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\ClassGenerationEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\PHPClassEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\Definitions\Objects\ApiObjectDefinition;

class APIClassGenerator extends AbstractGenerator
{
    private APIClassGenerationEvent $generationEvent;

    public function generate(ApiObjectDefinition $definition, Context $context): PHPClass
    {
        $namespaceName = $this->namespaceName($definition, $context);
        $shortName = ucfirst($definition->kind());
        $shortName = $this->validClassName($shortName);
        $className = ClassName::fromNamespaceAndName($namespaceName, $shortName);
        $this->generationEvent = new APIClassGenerationEvent($className, $context, $definition);

        return $this->doGenerate(
            $className,
            $definition->description(),
            $definition->properties(),
            $context
        );
    }

    protected function getInterfaceName(PHPClass $phpClass, Context $context): ClassName
    {
        if (!$phpClass->classType()->hasMethod('metadata')) {
            throw new \LogicException(
                sprintf(
                    'Method metadata() not found in class "%s"',
                    $phpClass->name()->fqcn()
                )
            );
        }

        $returnType = $phpClass->classType()->getMethod('metadata')->getReturnType();
        $metaClassName = ClassName::fromFQCN($returnType)->shortName();

        if ('ObjectMeta' === $metaClassName) {
            return $context->resourceInterface;
        } elseif ('ListMeta' === $metaClassName) {
            return $context->resourceListInterface;
        }

        throw new \LogicException(
            sprintf(
                'Unknown return type "%s" for %s::metadata() method',
                $returnType,
                $phpClass->name()->fqcn()
            )
        );
    }

    protected function createGenerationEvent(): ClassGenerationEventInterface
    {
        return $this->generationEvent;
    }

    protected function createPHPClassEvent(PHPClass $class): PHPClassEventInterface
    {
        return new APIClassGeneratedEvent($class);
    }

    private function namespaceName(ApiObjectDefinition $definition, Context $context): string
    {
        $pattern = \sprintf('/%s$/', \preg_quote('.k8s.io'));
        $suffix = \preg_replace($pattern, '', $definition->group());
        if (\str_contains($suffix, '.')) {
            $parts = \explode('.', $suffix);
            $suffix = trim($parts[0]);
        }
        $suffix = \ucfirst($suffix);

        $namespaceName = $context->namespacePrefix.'\\API';
        if ($suffix) {
            $namespaceName .= '\\'.$suffix;
        }

        return $namespaceName;
    }

    private function validClassName(string $className): string
    {
        return match ($className) {
            'Function' => 'TheFunction',
            'Object' => 'TheObject',
            default => $className,
        };
    }
}
