<?php
namespace Jihe\Domain\City;

interface CityRepository
{
    /**
     * find a city by id
     *
     * @param $id string
     * @return City|null
     */
    public function find($id);
}