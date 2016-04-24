<?php
namespace Jihe\Domain\Team;

interface CertificationRepository
{
    public function store(Certification $certification);
}