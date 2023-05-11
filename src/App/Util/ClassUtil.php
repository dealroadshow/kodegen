<?php

declare(strict_types=1);

namespace App\Util;

use Dealroadshow\Kodegen\API\CodeGeneration\PHP\Context;

class ClassUtil
{
    public static function filePathForClass(string $fqcn, Context $context): string
    {
        $pattern = sprintf('/^%s/', \preg_quote($context->namespacePrefix));
        $withoutNamespacePrefix = \preg_replace($pattern, '', $fqcn);
        $relativePath = \str_replace('\\', DIRECTORY_SEPARATOR, $withoutNamespacePrefix);
        $absPath = $context->outputDir.DIRECTORY_SEPARATOR.$relativePath.'.php';
        $absPath = \str_replace('//', '/', $absPath);

        $dir = \dirname($absPath);
        if (!\file_exists($dir)) {
            \mkdir(\dirname($absPath), 0o777, true);
        }

        return $absPath;
    }
}
