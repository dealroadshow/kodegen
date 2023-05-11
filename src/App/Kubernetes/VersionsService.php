<?php

declare(strict_types=1);

namespace App\Kubernetes;

use App\Github\ReleasesService;

class VersionsService
{
    public const GITHUB_USERNAME = 'kubernetes';
    public const GITHUB_REPO = 'kubernetes';

    private ReleasesService $releasesService;

    public function __construct(ReleasesService $releasesService)
    {
        $this->releasesService = $releasesService;
    }

    /**
     * @param int $numberOfReleases
     *
     * @return string[]|array
     */
    public function latestMinorReleases(int $numberOfReleases): array
    {
        return $this->releasesService->latestMinorReleasesNames(
            self::GITHUB_USERNAME,
            self::GITHUB_REPO,
            $numberOfReleases
        );
    }
}
