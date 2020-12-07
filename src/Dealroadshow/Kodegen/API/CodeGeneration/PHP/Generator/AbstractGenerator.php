<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\Generator;

use Dealroadshow\JsonSchema\DataType\ArrayType;
use Dealroadshow\JsonSchema\DataType\PropertyDefinition;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\ClassGenerationEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\ClassNameEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\JsonSerializeMethodEvent;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Event\CodeGeneration\PHPClassEventInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Processor\PropertyProcessingService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AbstractGenerator
{
    protected PHPTypesService $typesService;
    protected PropertyProcessingService $propertyService;
    private ListClassGenerator $listGenerator;
    protected EventDispatcherInterface $dispatcher;

    /**
     * @var ClassName[]|array
     */
    protected array $classesToUse = [];

    abstract protected function createGenerationEvent(): ClassGenerationEventInterface;
    abstract protected function createPHPClassEvent(PHPClass $class): PHPClassEventInterface;
    abstract protected function getInterfaceName(PHPClass $phpClass, Context $context): ClassName;

    public function __construct(PHPTypesService $typesService, PropertyProcessingService $propertyService, ListClassGenerator $listGenerator, EventDispatcherInterface $dispatcher)
    {
        $this->typesService = $typesService;
        $this->propertyService = $propertyService;
        $this->listGenerator = $listGenerator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ClassName                  $className
     * @param string                     $description
     * @param PropertyDefinition[]|array $properties
     * @param Context                    $context
     *
     * @return PHPClass
     */
    protected function doGenerate(ClassName $className, string $description, array $properties, Context $context): PHPClass
    {
        $className = $this->className($className);
        $event = $this->generationEvent();
        if ($event->hasClass()) {
            return $event->getClass();
        }

        $class = new ClassType($className->shortName());
        $class->addComment(wordwrap($description, 80));
        $this->defineClassMembers($className, $class, $properties, $context);
        $this->defineJsonSerializeMethod($className, $class);

        $phpClass = new PHPClass($className, $class);
        foreach ($this->classesToUse as $classNameToUse) {
            $phpClass->useClass($classNameToUse);
        }

        $this->classGeneratedEvent($phpClass);

        $interfaceName = $this->getInterfaceName($phpClass, $context);
        $this->addImplement($interfaceName, $class);

        return $phpClass;
    }

    private function className(ClassName $className): ClassName
    {
        $event = new ClassNameEvent($className);
        $this->dispatcher->dispatch($event);

        return $event->getClassName();
    }

    private function generationEvent(): ClassGenerationEventInterface
    {
        $event = $this->createGenerationEvent();
        $this->dispatcher->dispatch($event);

        return $event;
    }

    private function classGeneratedEvent(PHPClass $phpClass): PHPClassEventInterface
    {
        $event = $this->createPHPClassEvent($phpClass);
        $this->dispatcher->dispatch($event);

        return $event;
    }

    /**
     * @param ClassName                  $className
     * @param ClassType                  $class
     * @param PropertyDefinition[]|array $properties
     * @param Context                    $context
     */
    protected function defineClassMembers(ClassName $className, ClassType $class, array $properties, Context $context): void
    {
        foreach ($properties as $propertyDefinition) {
            $this->propertyService->processDefinition($propertyDefinition, $className, $class, $context);
            if ($propertyDefinition->skipped()) {
                continue;
            }
            $property = $this->defineProperty($className, $propertyDefinition, $class, $context);
            $this->defineGetter($property, $class);
            $this->defineSetter($property, $class);
        }

        $this->defineConstructor($class, $properties);
    }

    /**
     * @param ClassType                  $class
     * @param PropertyDefinition[]|array $properties
     */
    protected function defineConstructor(ClassType $class, array $properties)
    {
        $constructor = $class->addMethod('__construct');

        foreach ($properties as $propertyDefinition) {
            if ($propertyDefinition->skipped()) {
                continue;
            }
            $property = $class->getProperty($propertyDefinition->name());

            if ($propertyDefinition->hasInitializer()) {
                $initializer = $propertyDefinition->getInitializer();
                $initializer($property, $constructor, $class);

                continue;
            }

            if (!$propertyDefinition->required()) {
                continue;
            }

            $param = $constructor
                ->addParameter($property->getName())
                ->setType($property->getType())
                ->setNullable(false);

            $constructor->addBody(
                sprintf(
                    '$this->%s = $%s;',
                    $property->getName(),
                    $param->getName()
                )
            );
        }
    }

    protected function defineProperty(ClassName $className, PropertyDefinition $definition, ClassType $class, Context $context): Property
    {
        $propertyType = $definition->type();

        // This is a special case: we do not need to generate duplicate collection classes,
        // like DeploymentList for the $items property of Kubernetes *List classes
        // TODO this should not be happening here, we need to refactor this and move this case
        // to a better place
        if (
            str_ends_with($className->shortName(), 'List')
            && 'items' === $definition->name()
            && $propertyType instanceof ArrayType
        ) {
            $itemPHPType = $this->typesService->resolveType(
                $propertyType->itemType(),
                $context,
                $definition->nullable()
            );
            $this->listGenerator->generateForClass($class, $itemPHPType);
            $property = $class->getProperty('items');

            $this->propertyService->processProperty($definition, $property, $className, $class, $context);

            return $property;
        }

        $type = $this->typesService->resolveType($definition->type(), $context, $definition->nullable());
        $property = $class->addProperty($definition->name());
        $property
            ->setType($type->name())
            ->addComment(PHP_EOL);
        $description = $definition->description();
        if (null !== $description) {
            $property
                ->addComment($description)
                ->addComment(' ');
        }
        if ($type->docType() !== $type->name()) {
            $property->addComment('@var '.$type->docType());
        }
        $property
            ->addComment(PHP_EOL)
            ->setPrivate()
            ->setNullable($definition->nullable());

        if ($property->isNullable()) {
            $property->setInitialized(true);
            $property->setValue(null);
        }

        $this->propertyService->processProperty($definition, $property, $className, $class, $context);

        return $property;
    }

    protected function defineGetter(Property $property, ClassType $class): Method
    {
        $comment = $property->getComment();
        preg_match('/@var\s+(.+)$/im', $comment, $matches);
        $docType = $matches[1] ?? null;

        $methodName = 'get'.ucfirst($property->getName());
        $method = $class->addMethod($methodName);
        $method
            ->setReturnType($property->getType())
            ->addBody('return $this->'.$property->getName().';')
            ->setReturnNullable($property->isNullable());

        if (null !== $docType) {
            $method
                ->addComment(PHP_EOL)
                ->addComment('@return '.$docType)
                ->addComment(PHP_EOL);
        }

        return $method;
    }

    protected function defineSetter(Property $property, ClassType $class): Method
    {
        $methodName = 'set'.ucfirst($property->getName());
        $method = $class->addMethod($methodName);
        $method
            ->setReturnType('self')
            ->setReturnNullable(false);

        $param = $method
            ->addParameter($property->getName())
            ->setType($property->getType())
            ->setNullable(false);

        $method
            ->addBody(
                sprintf(
                    '$this->%s = $%s;',
                    $property->getName(),
                    $param->getName()
                )
            )
            ->addBody('')
            ->addBody('return $this;');

        return $method;
    }

    protected function defineJsonSerializeMethod(ClassName $className, ClassType $class)
    {
        $method = $class
            ->addMethod('jsonSerialize')
            ->setReturnNullable(false)
            ->setReturnType('array');
        $method->addBody('return [');

        $indent = str_repeat(' ', 4);

        $event = new JsonSerializeMethodEvent($className, $class, $method);
        $this->dispatcher->dispatch($event);
        foreach ($event->jsonProperties() as $propertyName => $valueCode) {
            $method->addBody(
                "{$indent}'{$propertyName}' => {$valueCode},"
            );
        }

        foreach ($class->getProperties() as $property) {
            $method->addBody(
                "{$indent}'{$property->getName()}' => \$this->{$property->getName()},"
            );
        }

        $method->addBody('];');
    }

    protected function addImplement(ClassName $className, ClassType $class): void
    {
        $implements = $class->getImplements();
        $implements[] = $className->fqcn();
        $implements = array_unique($implements);
        $class->setImplements($implements);
    }
}
