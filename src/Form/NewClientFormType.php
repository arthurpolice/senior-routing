<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\ServiceProvider;
use App\Enum\WeekDays;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('schedule', ChoiceType::class, [
                'choices' => WeekDays::cases(),
                'choice_value' => 'value',
                'choice_label' => static fn(WeekDays $day): string => ucfirst(strtolower($day->name)),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('address')
            ->add('city')
            ->add('country')
            ->add('serviceProviders', EntityType::class, [
                'class' => ServiceProvider::class,
                'choice_label' => static function (ServiceProvider $serviceProvider): string {
                    return $serviceProvider->getName() ?? \sprintf('Provider #%d', $serviceProvider->getId());
                },
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
