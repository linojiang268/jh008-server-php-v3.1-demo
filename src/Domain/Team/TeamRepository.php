<?php
namespace Jihe\Domain\Team;

interface TeamRepository
{
    public function store(Team $team);
}