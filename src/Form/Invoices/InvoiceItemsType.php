<?php

namespace App\Form\Invoices;

use App\Entity\Invoices\InvoiceItems;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceItemsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceId')
            ->add('position')
            ->add('name')
            ->add('productCode')
            ->add('productId')
            ->add('pkwiu')
            ->add('ean')
            ->add('amount')
            ->add('unit')
            ->add('vat')
            ->add('grossValue')
            ->add('netValue')
            ->add('gross')
            ->add('net')
            ->add('invoice')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceItems::class,
        ]);
    }
}
