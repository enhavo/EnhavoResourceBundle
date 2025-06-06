<?php

/*
 * This file is part of the enhavo package.
 *
 * (c) WE ARE INDEED GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enhavo\Bundle\ResourceBundle\ExpressionLanguage;

interface ResourceExpressionVariableProviderInterface
{
    /** @return array<string, mixed> */
    public function getVariables(): array;
}
