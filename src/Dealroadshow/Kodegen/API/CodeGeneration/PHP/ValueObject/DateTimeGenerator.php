<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP\ValueObject;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\JsonSchema\DataType\StringType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\PHPTypesService;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\ClassName;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPClass;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;
use Nette\PhpGenerator\ClassType;

class DateTimeGenerator extends AbstractGenerator
{
    private const VALUE_TYPE = \DateTimeInterface::class;
    private const DATE_TIME_FORMAT = 'c';

    private PHPType $phpType;

    public function __construct()
    {
        $this->phpType = new PHPType(self::VALUE_TYPE, self::VALUE_TYPE, false);
    }

    /**
     * @param ClassName                    $className
     * @param StringType|DataTypeInterface $type
     * @param Context                      $context
     * @param PHPTypesService              $service
     *
     * @return PHPClass
     */
    public function generate(ClassName $className, DataTypeInterface $type, Context $context, PHPTypesService $service): PHPClass
    {
        $class = new ClassType($className->shortName());

        $this
            ->defineProperty($class, $this->phpType)
            ->defineConstructor($class, $this->phpType)
            ->defineToStringMethod($class)
            ->defineFactoryMethod($class, $this->phpType, 'fromDateTime', 'dateTime');

        $phpClass = new PHPClass($className, $class);
        $phpClass->useClass(ClassName::fromFQCN(\DateTimeInterface::class));
        $this->defineJsonSerializeMethod($phpClass);

        return $phpClass;
    }

    public function supports(ClassName $className, DataTypeInterface $type): bool
    {
        return $type instanceof StringType && StringType::FORMAT_DATETIME === $type->format();
    }

    private function defineToStringMethod(ClassType $class): self
    {
        $method = $class->addMethod('toString');
        $method
            ->setPublic()
            ->setReturnType('string')
            ->setReturnNullable(false)
            ->addBody(
                \sprintf(
                    'return $this->%s->format(\'%s\');',
                    self::VALUE_PROPERTY_NAME,
                    self::DATE_TIME_FORMAT
                )
            );

        return $this;
    }

    protected function jsonSerializeBody(): string
    {
        return 'return $this->toString();';
    }
}
