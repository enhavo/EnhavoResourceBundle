<?php

namespace Enhavo\Bundle\ResourceBundle\Batch\Type;

use Doctrine\ORM\EntityManagerInterface;
use Enhavo\Bundle\ApiBundle\Data\Data;
use Enhavo\Bundle\ApiBundle\Endpoint\Context;
use Enhavo\Bundle\ResourceBundle\Batch\AbstractBatchType;
use Enhavo\Bundle\ResourceBundle\Exception\BatchExecutionException;
use Enhavo\Bundle\ResourceBundle\Repository\EntityRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

class AssignBatchType extends AbstractBatchType
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function execute(array $options, array $ids, EntityRepositoryInterface $repository, Data $data, Context $context): void
    {
        $form = $this->formFactory->create($options['form']);

        $form->handleRequest($context->getRequest());
        if(!$form->isValid()) {
            throw new BatchExecutionException($this->translator->trans($options['error_assign'], [], $options['translation_domain']));
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $data = $form->getData();

        if($options['data_property']) {
            $data = $propertyAccessor->getValue($data, $options['data_property']);
        }

        $i = 0;
        foreach ($ids as $id) {
            $resource = $repository->find($id);
            if ($resource) {
                $propertyAccessor->setValue($resource, $options['property'], $data);
                $i++;
            }
            if ($i%100 === 0) {
                $this->em->flush();
            }
        }

        $this->em->flush();
    }

    public function createViewData(array $options, Data $data): void
    {
        $data['route'] = $options['route'];
        $data['routeParameters'] = $options['route_parameters'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'batch.assign.label',
            'translation_domain' => 'EnhavoResourceBundle',
            'route' => null,
            'route_parameters' => null,
            'form_parameters' => [],
            'error_assign' => 'batch.assign.error.assign',
            'data_property' => null
        ]);

        $resolver->setRequired(['form', 'property']);
    }

    public static function getParentType(): ?string
    {
        return FormBatchType::class;
    }

    public static function getName(): ?string
    {
        return 'assign';
    }
}
