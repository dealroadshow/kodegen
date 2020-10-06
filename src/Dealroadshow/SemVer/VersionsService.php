<?php

namespace Dealroadshow\SemVer;

class VersionsService
{
    /**
     * Returns latest most stable version, or null, if there are no versions
     *
     * @param Version[]|array $versions
     *
     * @return Version|null
     */
    public function latestMostStable(array $versions): Version
    {
        $v = $versions;

        return ($this->latestStable($v) ?? $this->latestBeta($v)) ?? $this->latest($v);
    }

    /**
     * Returns latest stable version, or null, if there are no stable versions
     *
     * @param Version[]|array $versions
     *
     * @return Version|null
     */
    public function latestStable(array $versions): ?Version
    {
        foreach ($this->sortDescending($versions) as $version) {
            if (!$version->stable()) {
                continue;
            }

            return $version;
        }

        return null;
    }

    /**
     * @param Version[]|array $versions
     *
     * @return Version|null
     */
    public function latestBeta(array $versions): ?Version
    {
        foreach ($this->sortDescending($versions) as $version) {
            if ($version->beta()) {
                return $version;
            }
        }

        return null;
    }

    /**
     * @param Version[]|array $versions
     *
     * @return Version|null
     */
    public function latest(array $versions): Version
    {
        return $this->sortDescending($versions)[0];
    }

    /**
     * @param array $versions
     *
     * @return Version[]|array
     */
    private function sortDescending(array $versions)
    {
        \usort(
            $versions,
            fn(Version $lh, Version $rh) => \version_compare($rh->string(), $lh->string())
        );

        return $versions;
    }
}
