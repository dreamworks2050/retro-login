#!/usr/bin/env php
<?php
/**
 * Documentation Generator for RetroLogin Plugin
 *
 * Generates API documentation from PHP docblocks.
 * Lightweight alternative to phpDocumentor.
 */

declare(strict_types=1);

$baseDir = dirname(__DIR__);
$outputDir = $baseDir . '/docs/api';
$appDir = $baseDir . '/app';

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

/**
 * Get all PHP files in a directory recursively
 */
function getPhpFiles(string $dir): array
{
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

/**
 * Parse docblock and extract information
 */
function parseDocblock(string $docblock): array
{
    $info = [
        'description' => '',
        'tags' => [],
    ];

    if (preg_match('/\/\*\*(.*?)\*\//s', $docblock, $matches)) {
        $content = trim($matches[1]);
        $content = preg_replace('/^\s*\*\s?/m', '', $content);
        $lines = explode("\n", $content);

        $description = [];
        $inTags = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (strpos($line, '@') === 0) {
                $inTags = true;
                $tagParts = preg_split('/\s+/', $line, 2);
                $tagName = ltrim($tagParts[0], '@');
                $tagValue = $tagParts[1] ?? '';

                if ($tagName === 'param') {
                    $paramParts = preg_split('/\s+/', $tagValue, 3);
                    $info['tags'][$tagName][] = [
                        'type' => $paramParts[0] ?? '',
                        'name' => $paramParts[1] ?? '',
                        'description' => $paramParts[2] ?? '',
                    ];
                } elseif ($tagName === 'return') {
                    $returnParts = preg_split('/\s+/', $tagValue, 2);
                    $info['tags'][$tagName] = [
                        'type' => $returnParts[0] ?? '',
                        'description' => $returnParts[1] ?? '',
                    ];
                } else {
                    $info['tags'][$tagName] = $tagValue;
                }
            } elseif (!$inTags) {
                $description[] = $line;
            }
        }

        $info['description'] = implode(' ', $description);
    }

    return $info;
}

/**
 * Extract class information from a PHP file
 */
function extractClassInfo(string $filePath): ?array
{
    $content = file_get_contents($filePath);

    // Check for namespace
    $namespace = '';
    if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
        $namespace = $matches[1];
    }

    // Check for class - handle optional modifiers (final, abstract)
    $className = '';
    // Note: final and abstract must be followed by whitespace
    $classPattern = '/(?:^|\n)[ \t]*(?:final[ \t]+|abstract[ \t]+)?class[ \t]+([a-zA-Z_][a-zA-Z0-9_]*)/m';
    if (preg_match($classPattern, $content, $matches)) {
        $className = $matches[1];
    }

    if (empty($className)) {
        return null;
    }

    // Get file docblock
    $docblock = '';
    if (preg_match('/^\/\*\*(.*?)\*\//s', $content, $matches)) {
        $docblock = $matches[0];
    }

    // Extract methods
    $methods = [];
    if (preg_match_all('/(function\s+(\w+)\s*\([^)]*\)\s*:[^;]*\s*\{)/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[1] as $idx => $match) {
            $methodName = $matches[2][$idx][0];
            $methodStart = $match[1];

            // Get method docblock
            $methodDocblock = '';
            $beforeMethod = substr($content, 0, $methodStart);
            if (preg_match_all('/(\/\*\*[^*]*\*+(?:[^\/*][^*]*\*+)*\/\s*)/', $beforeMethod, $docMatches, PREG_OFFSET_CAPTURE)) {
                $lastDoc = end($docMatches[0]);
                $methodDocblock = $lastDoc[0];
            }

            $methods[] = [
                'name' => $methodName,
                'docblock' => $methodDocblock,
                'info' => parseDocblock($methodDocblock),
            ];
        }
    }

    return [
        'file' => $filePath,
        'namespace' => $namespace,
        'class' => $className,
        'docblock' => $docblock,
        'info' => parseDocblock($docblock),
        'methods' => $methods,
    ];
}

/**
 * Generate Markdown documentation for a class
 */
function generateClassDoc(array $classInfo): string
{
    $md = "# {$classInfo['class']}\n\n";

    if (!empty($classInfo['info']['description'])) {
        $md .= "{$classInfo['info']['description']}\n\n";
    }

    if (!empty($classInfo['info']['tags'])) {
        foreach ($classInfo['info']['tags'] as $tag => $value) {
            if (is_array($value)) {
                continue; // Skip complex tags in overview
            }
            $md .= "**@{$tag}**: {$value}\n\n";
        }
    }

    $md .= "## Namespace\n\n`{$classInfo['namespace']}`\n\n";

    $md .= "## Methods\n\n";
    $md .= "| Method | Description |\n";
    $md .= "|--------|-------------|\n";

    foreach ($classInfo['methods'] as $method) {
        $desc = $method['info']['description'] ?? 'No description';
        $params = '';
        if (isset($method['info']['tags']['param'])) {
            $paramNames = array_map(fn($p) => '$' . ($p['name'] ?? ''), $method['info']['tags']['param']);
            $params = '(' . implode(', ', $paramNames) . ')';
        }
        $returnType = $method['info']['tags']['return']['type'] ?? '';

        $md .= "| `{$method['name']}{$params}` {$returnType} | {$desc} |\n";
    }

    $md .= "\n";

    return $md;
}

// Main execution
echo "Generating API documentation...\n";

$files = getPhpFiles($appDir);
$classes = [];

foreach ($files as $file) {
    $classInfo = extractClassInfo($file);
    if ($classInfo !== null) {
        $classes[] = $classInfo;
        echo "  Found: {$classInfo['namespace']}\\{$classInfo['class']}\n";
    }
}

// Generate index
$index = "# RetroLogin API Documentation\n\n";
$index .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
$index .= "## Classes\n\n";

foreach ($classes as $class) {
    $relPath = 'api/' . str_replace('\\', '-', $class['class']) . '.md';
    $index .= "- [{$class['class']}]({$relPath}) - {$class['info']['description']}\n";
}

file_put_contents($outputDir . '/index.md', $index);

// Generate individual class docs
foreach ($classes as $class) {
    $filename = str_replace('\\', '-', $class['class']) . '.md';
    $content = generateClassDoc($class);
    file_put_contents($outputDir . '/' . $filename, $content);
}

echo "\nDocumentation generated in: {$outputDir}\n";
echo "Total classes documented: " . count($classes) . "\n";
