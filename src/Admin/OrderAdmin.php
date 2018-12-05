<?php

namespace PiedWeb\ReservationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use PiedWeb\CMSBundle\Entity\User;
use PiedWeb\ReservationBundle\Entity\OrderItem;
use PiedWeb\ReservationBundle\Entity\Order;

class OrderAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'orderedAt',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->with('admin.order.informations', ['class' => 'col-md-6 order-1']);
        $formMapper->add('User', ModelType::class, [
            'required' => false,
            'class' => User::class,
            'label' => 'admin.order.user.label',
            'help' => 'admin.order.user.help',
        ]);
        $formMapper->add('orderItem', ModelAutocompleteType::class, [
            'required' => false,
            'multiple' => true,
            'class' => OrderItem::class,
            'property' => 'product.name',   // A changer
            'label' => 'admin.order.orderItem.label',
             'to_string_callback' => function ($entity, $property) {
                 return $entity->getHumanId();
             },
            'btn_add' => true,
         ], ['required' => false]);
        $formMapper->end();

        $formMapper->with('admin.order.details', ['class' => 'col-md-3 order-2']);
        $formMapper->add('paiementMethod', ChoiceType::class, [
            'required' => false,
            'choices' => array_flip(Order::$paiementMethodList),
            'label' => 'admin.order.paiementMethod.label',
        ]);
        $formMapper->add('paid', ChoiceType::class, [
            'required' => false,
            'choices' => ['Oui' => true, 'Non' => false],
            'label' => 'admin.order.paid.label',
        ]);
        $formMapper->add('orderedAt', DateTimePickerType::class, [
            'required' => false,
            'label' => 'admin.order.orderedAt.label',
        ]);
        $formMapper->end();

        $formMapper->with('admin.order.canceling', ['class' => 'col-md-3 order-3']);
        $formMapper->add('canceledAt', DateTimePickerType::class, [
            'required' => false,
            'label' => 'admin.order.canceledAt.label',
        ]);
        $formMapper->add('refund', ChoiceType::class, [
            'choices' => [
                'Non' => false,
                'Oui' => true,
            ],
            'label' => 'admin.order.refund.label',
        ]);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id');
        $datagridMapper->add('user', null, [
            'label' => 'admin.order.user.label',
        ]);
        $datagridMapper->add('orderItem.product.name', null, [
            'label' => 'admin.order.orderItem.label',
        ]);
        $datagridMapper->add('paiementMethod', null, [
            'label' => 'admin.order.paiementMethod.label',
        ]);
        $datagridMapper->add('paid', null, [
            'label' => 'admin.order.paid.label',
        ]);
        $datagridMapper->add('orderedAt', null, [
            'label' => 'admin.order.orderedAt.label',
        ]);
        $datagridMapper->add('canceledAt', null, [
            'label' => 'admin.order.canceledAt.label',
        ]);
        $datagridMapper->add('refund', null, [
            'label' => 'admin.order.refund.label',
        ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('id');
        $listMapper->add('user', null, [
            'label' => 'admin.order.user.label',
        ]);
        /*
        $listMapper->add('orderItem', null, [
            'label' => 'admin.order.orderItem.label',
            'associated_property' => 'humanId',
            // 'template' => 'todo' // pour éviter d'avoir une colonne dégueulasse avec 20 réservations pour la même sortie
        ]);
        */
        $listMapper->add('paiementMethod', null, [
            'label' => 'admin.order.paiementMethod.label',
        ]);
        $listMapper->add('paid', null, [
            'label' => 'admin.order.paid.label',
        ]);
        $listMapper->add('orderedAt', null, [
            'label' => 'admin.order.orderedAt.label',
        ]);
        $listMapper->add('canceledAt', null, [
            'label' => 'admin.order.canceledAt.label',
        ]);
        $listMapper->add('refund', null, [
            'label' => 'admin.order.refund.label',
        ]);
        $listMapper->add('_action', null, [
            'actions' => [
                'edit' => [],
                'invoice' => [
                    'template' => '@PiedWebReservation/admin/CRUD/list_invoice.html.twig',
                ],
            ],
            'row_align' => 'right',
            'header_class' => 'text-right',
            'label' => 'admin.action',
        ]);
    }
}
