<?php

namespace SlidesLive\SlidesLiveBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('email', 'email', array('label' => 'Email:', 'required' => true));
    }

    public function getName()
    {
        return 'subscribe';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SlidesLive\SlidesLiveBundle\Entity\Subscribe',
        );
  }
}
