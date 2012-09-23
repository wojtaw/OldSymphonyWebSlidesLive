<?php

namespace SlidesLive\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username', 'email');
        $builder->add('name', 'text');
        $builder->add('password', 'password');

        $builder->add('canonicalName', 'text');
    }

    public function getName()
    {
        return 'account';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class'        => 'SlidesLive\SlidesLiveBundle\Entity\Account',
            'csrf_protection'   => false,
        );
    }
}

