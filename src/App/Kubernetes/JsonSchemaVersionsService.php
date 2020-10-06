<?php

namespace App\Kubernetes;

use App\Github\FileTreeService;

class JsonSchemaVersionsService
{
    private const GITHUB_USERNAME = 'instrumenta';
    private const GITHUB_REPO = 'kubernetes-json-schema';

    private FileTreeService $treeService;
    private VersionsService $versionsService;

    public function __construct(FileTreeService $treeService, VersionsService $versionsService)
    {
        $this->treeService = $treeService;
        $this->versionsService = $versionsService;
    }

    public function latestVersionsMap(int $numberOfVersions)
    {
        $latestMinorVersions = $this->versionsService->latestMinorReleases($numberOfVersions);
        $topLevelItems = $this->treeService->topLevelItems(
            self::GITHUB_USERNAME,
            self::GITHUB_REPO
        );

        $dirs = \array_filter($topLevelItems['tree'], fn(array $item) => 'tree' === $item['type']);
        $dirNames = \array_column($dirs, 'path');
        $stableVersionPattern = '/v?\d+\.\d+\.\d+$/';
        $dirNames = \array_filter(
            $dirNames,
            fn(string $dirname) => 0 !== \preg_match($stableVersionPattern, $dirname)
        );

        $groupedByMinorRelease = [];
        $pattern = \sprintf('/(%s)\.\d+$/', \implode('|', $latestMinorVersions));
        foreach ($dirNames as $dirname) {
            if (0 === \preg_match($pattern, $dirname, $matches)) {
                continue;
            }
            $groupedByMinorRelease[$matches[1]] ??= [];
            $groupedByMinorRelease[$matches[1]][] = $dirname;
        }

        $versionsMap = [];
        foreach ($groupedByMinorRelease as $minorRelease => $dirNames) {
            \usort($dirNames, 'version_compare');
            $latestJsonSchemaVersion = $dirNames[\count($dirNames) - 1];
            $versionsMap[$minorRelease] = $latestJsonSchemaVersion;
        }

        return $versionsMap;
    }
}
