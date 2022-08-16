<?php

namespace App\Form\Invoices;

use App\Entity\Invoices\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('externalId')
            ->add('type')
            ->add('subtype')
            ->add('countingSumType')
            ->add('number')
            ->add('issueDate')
            ->add('saleDate')
            ->add('dueDate')
            ->add('paymentDate')
            ->add('paymentAmount')
            ->add('currency')
            ->add('exchange')
            ->add('paymentType')
            ->add('language')
            ->add('template')
            ->add('issuerName')
            ->add('receiverName')
            ->add('orderNumber')
            ->add('department')
            ->add('sendMail')
            ->add('storehouse')
            ->add('autoDocCreate')
            ->add('remarks')
            ->add('additionalRemarks')
            ->add('netValue')
            ->add('grossValue')
            ->add('vat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
