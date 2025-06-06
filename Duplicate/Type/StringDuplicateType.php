<?php

/*
 * This file is part of the enhavo package.
 *
 * (c) WE ARE INDEED GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enhavo\Bundle\ResourceBundle\Duplicate\Type;

use Enhavo\Bundle\ResourceBundle\Duplicate\AbstractDuplicateType;
use Enhavo\Bundle\ResourceBundle\Duplicate\SourceValue;
use Enhavo\Bundle\ResourceBundle\Duplicate\TargetValue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class StringDuplicateType extends AbstractDuplicateType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function duplicate($options, SourceValue $sourceValue, TargetValue $targetValue, $context): void
    {
        if (null === $sourceValue->getValue()) {
            $targetValue->setValue(null);

            return;
        }

        $prefix = $options['prefix'] ? $this->translator->trans($options['prefix'], [], $options['translation_domain']) : '';
        $postfix = $options['postfix'] ? $this->translator->trans($options['postfix'], [], $options['translation_domain']) : '';

        $value = $prefix.$sourceValue->getValue().$postfix;
        $targetValue->setValue($value);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'prefix' => null,
            'postfix' => null,
            'translation_domain' => null,
        ]);
    }

    public static function getName(): ?string
    {
        return 'string';
    }
}
