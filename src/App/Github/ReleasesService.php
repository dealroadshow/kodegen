<?php

declare(strict_types=1);

namespace App\Github;

use Github\Client;

class ReleasesService
{
    private Client $githubClient;

    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    public function all(string $githubUsername, string $repoName): array
    {
        return $this->githubClient
                   ->api('repo')
                   ->releases()
                   ->all($githubUsername, $repoName);
    }

    /**
     * Returns an array of release names
     *
     * @param string $githubUsername
     * @param string $repoName
     *
     * @return string[]|array
     */
    public function names(string $githubUsername, string $repoName): array
    {
        $releases = $this->all($githubUsername, $repoName);

        return \array_column($releases, 'name');
    }

    public function latestMinorReleasesNames(string $githubUsername, string $repoName, int $numberOfReleases): array
    {
        $names = $this->names($githubUsername, $repoName);

        // Filter out alpa, beta, rc and other non-stable versions
        $stableVersionPattern = '/v?\d+\.\d+\.\d+$/';
        $names = \array_filter(
            $names,
            fn (string $name) => 0 !== \preg_match($stableVersionPattern, $name)
        );

        $pattern = '/v?\d+\.\d+/';
        $minorReleases = [];
        foreach ($names as $name) {
            \preg_match($pattern, $name, $matches);
            if (0 === \count($matches)) {
                continue;
            }
            $match = $matches[0];
            $minorReleases[$match] = null;
        }

        $minorReleases = \array_keys($minorReleases);
        \usort($minorReleases, 'version_compare');

        return \array_slice($minorReleases, -($numberOfReleases+1));
    }
}
