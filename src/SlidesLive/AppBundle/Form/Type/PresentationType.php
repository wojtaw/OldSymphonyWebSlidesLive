<?php

namespace SlidesLive\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PresentationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('description', 'textarea');
        $builder->add('lang', 'text');
        $builder->add('dateRecorded', 'datetime', array("widget" => "single_text",
                                                "attr" => array(
                                                    "class" => "date"
                                                )));

        $builder->add('service', 'text');
        $builder->add('serviceId', 'text');
        $builder->add('externalService', 'text');
        $builder->add('externalServiceId', 'text');

        $builder->add('length', 'integer');
        $builder->add('slides', 'integer');
        $builder->add('video', 'integer');
        $builder->add('privacy', 'integer');
        $builder->add('flag', 'integer');
        $builder->add('showSpeaker', 'integer');
        $builder->add('startSlide', 'integer');
        $builder->add('autoPlay', 'text');
    }

    public function getName()
    {
        return 'presentation';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class'        => 'SlidesLive\SlidesLiveBundle\Entity\Presentation',
            'csrf_protection'   => false,
        );
    }
}

