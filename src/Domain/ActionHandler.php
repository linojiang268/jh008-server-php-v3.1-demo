<?php
namespace Jihe\Domain;

/**
 * handle the request action
 */
interface ActionHandler
{
    /**
     * handle the action, use the domain services to finish the domain logic
     *
     * @param array|null $request
     */
    public function handle(array $request = []);
}