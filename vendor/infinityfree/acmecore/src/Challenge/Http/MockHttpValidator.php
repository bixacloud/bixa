<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Challenge\Http;

use InfinityFree\AcmeCore\Challenge\SolverInterface;
use InfinityFree\AcmeCore\Challenge\ValidatorInterface;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;

/**
 * Validator for pebble-challtestsrv.
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class MockHttpValidator implements ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(AuthorizationChallenge $authorizationChallenge, SolverInterface $solver): bool
    {
        return 'http-01' === $authorizationChallenge->getType() && $solver instanceof MockServerHttpSolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(AuthorizationChallenge $authorizationChallenge, SolverInterface $solver): bool
    {
        return true;
    }
}
