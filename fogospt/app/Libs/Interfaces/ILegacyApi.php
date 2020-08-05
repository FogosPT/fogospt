<?php

namespace App\Libs\Interfaces;

interface ILegacyApi
{
    public function getResults(string $endpoint) : array;
    public function getResultById(string $endpoint, string $id) : array;
}