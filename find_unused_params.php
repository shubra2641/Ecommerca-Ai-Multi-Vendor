<?php

declare(strict_types=1);

function findUnusedParameters($directory)
{
    $unusedParams = [];

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    $phpFiles = new RegexIterator($iterator, '/\.php$/');

    foreach ($phpFiles as $file) {
        $content = file_get_contents($file->getPathname());
        $tokens = token_get_all($content);

        $functions = [];
        $currentFunction = null;
        $inFunction = false;
        $braceCount = 0;

        foreach ($tokens as $i => $token) {
            if (is_array($token)) {
                $tokenType = $token[0];
                $tokenValue = $token[1];

                if ($tokenType === T_FUNCTION) {
                    $inFunction = true;
                } elseif ($tokenType === T_STRING && $inFunction) {
                    $currentFunction = $tokenValue;
                    $functions[$currentFunction] = ['params' => [], 'used' => []];
                } elseif ($tokenType === T_VARIABLE && $inFunction && $currentFunction) {
                    // Check if this is a parameter
                    $prevToken = $tokens[$i - 1] ?? null;
                    $nextToken = $tokens[$i + 1] ?? null;

                    if ($prevToken && is_array($prevToken) && $prevToken[0] === T_WHITESPACE) {
                        $prevPrevToken = $tokens[$i - 2] ?? null;
                        if (
                            $prevPrevToken && is_array($prevPrevToken) &&
                            ($prevPrevToken[0] === T_STRING || $prevPrevToken[0] === T_ARRAY ||
                                $prevPrevToken[0] === T_CALLABLE || $prevPrevToken[0] === T_NS_SEPARATOR)
                        ) {
                            // This is likely a parameter
                            $paramName = $tokenValue;
                            if (! isset($functions[$currentFunction]['params'][$paramName])) {
                                $functions[$currentFunction]['params'][$paramName] = 0;
                            }
                        }
                    }

                    // Check if variable is used
                    if (isset($functions[$currentFunction]['params'][$tokenValue])) {
                        $functions[$currentFunction]['params'][$tokenValue]++;
                    }
                }
            } elseif ($token === '{') {
                $braceCount++;
            } elseif ($token === '}') {
                $braceCount--;
                if ($braceCount === 0 && $inFunction) {
                    $inFunction = false;
                    $currentFunction = null;
                }
            }
        }

        // Check for unused parameters
        foreach ($functions as $funcName => $data) {
            foreach ($data['params'] as $param => $usageCount) {
                if ($usageCount === 0) {
                    $unusedParams[] = [
                        'file' => $file->getPathname(),
                        'function' => $funcName,
                        'parameter' => $param,
                    ];
                }
            }
        }
    }

    return $unusedParams;
}

// Run the analysis
$unusedParams = findUnusedParameters(__DIR__ . '/app');

echo 'Found ' . count($unusedParams) . " unused parameters:\n\n";
foreach ($unusedParams as $param) {
    echo 'File: ' . str_replace(__DIR__ . '/', '', $param['file']) . "\n";
    echo 'Function: ' . $param['function'] . "\n";
    echo 'Parameter: ' . $param['parameter'] . "\n";
    echo "---\n";
}
