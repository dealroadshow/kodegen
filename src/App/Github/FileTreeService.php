<?php

namespace App\Github;

use Github\Client;

class FileTreeService
{
    private const BRANCH_MASTER = 'master';

    private Client $githubClient;
    private CommitsService $commitsService;

    public function __construct(Client $githubClient, CommitsService $commitsService)
    {
        $this->githubClient = $githubClient;
        $this->commitsService = $commitsService;
    }

    public function topLevelItems(string $githubUsername, string $repoName): array
    {
        $latestCommit = $this->commitsService->latestCommitInBranch(
            $githubUsername,
            $repoName,
            self::BRANCH_MASTER
        );

        $sha = $latestCommit['sha'];

        return $this->githubClient
            ->api('gitData')
            ->trees()
            ->show($githubUsername, $repoName, $sha);
    }
}
