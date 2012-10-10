<?php
namespace SlidesLive\SlidesLiveBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use SlidesLive\SlidesLiveBundle\DependencyInjection\Privacy;

class PrivacyChoiceType extends AbstractType
{
    public function getDefaultOptions(array $options)
    {
        return array(
            'choices' => Privacy::getChoices(),
        );
    }

    public function getParent(array $options)
    {
        return 'choice';
    }

    public function getName()
    {
        return 'privacy';
    }
}