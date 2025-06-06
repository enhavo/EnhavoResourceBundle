<?php

/*
 * This file is part of the enhavo package.
 *
 * (c) WE ARE INDEED GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enhavo\Bundle\ResourceBundle\Delete;

use Doctrine\ORM\EntityManagerInterface;
use Enhavo\Bundle\ResourceBundle\Event\ResourcePostDeleteEvent;
use Enhavo\Bundle\ResourceBundle\Event\ResourcePreDeleteEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DoctrineDeleteHandler implements DeleteHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function delete(object $resource): void
    {
        $this->eventDispatcher->dispatch(new ResourcePreDeleteEvent($resource), 'enhavo_resource.pre_delete');
        $this->em->remove($resource);
        $this->em->flush();
        $this->eventDispatcher->dispatch(new ResourcePostDeleteEvent($resource), 'enhavo_resource.post_delete');
    }
}
