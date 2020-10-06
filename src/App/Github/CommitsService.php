<?php

namespace App\Github;

use Github\Client;

class CommitsService
{
    private Client $githubClient;

    public function __construct(Client $githubClient)
    {
        $this->githubClient = $githubClient;
    }

    public function all(string $githubUsername, string $repoName, string $branchName)
    {
        return $this->githubClient
            ->api('repo')
            ->commits()
            ->all($githubUsername, $repoName, ['sha' => $branchName]);
    }

    public function latestCommitInBranch(string $githubUsername, string $repoName, string $branchName)
    {
        return $this->all($githubUsername, $repoName, $branchName)[0];
    }
}
