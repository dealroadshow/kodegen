<?php

namespace Dealroadshow\Kodegen\API\CodeGeneration\PHP;

use Dealroadshow\JsonSchema\DataType\DataTypeInterface;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Type\PHPType;
use Dealroadshow\Kodegen\API\CodeGeneration\PHP\TypeResolver\TypeResolverInterface;

class PHPTypesService
{
    /**
     * @var TypeResolverInterface[]|iterable
     */
    private iterable $typeResolvers;

    public function __construct(iterable $typeResolvers)
    {
       $this->typeResolvers = $typeResolvers;
    }

    public function resolveType(DataTypeInterface $type, Context $context, bool $nullable): PHPType
    {
        foreach ($this->typeResolvers as $resolver) {
            if ($resolver->supports($type)) {
                return $resolver->resolve($type, $this, $context, $nullable);
            }
        }

        throw new \LogicException(
            sprintf('There are no resolvers for type "%s"', \get_class($type))
        );
    }
}
