<?php

namespace PiedWeb\ReservationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;

class OrderItemAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'order.orderedAt', // todo: check it's work
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->with('admin.order.informations', ['class' => 'col-md-6 order-1']);
        $formMapper->add('firstname', TextType::class, [
            'label' => 'admin.user.firstname.label',
        ]);
        $formMapper->add('lastname', TextType::class, [
            'label' => 'admin.user.lastname.label',
        ]);
        $formMapper->add('phone', TextType::class, [
            'label' => 'admin.user.phone.label',
            'required' => false,
        ]);
        $formMapper->add('email', TextType::class, [
            'label' => 'admin.user.email.label',
            'required' => false,
        ]);
        $formMapper->add('dateOfBirth', DatePickerType::class, [
            'label' => 'admin.user.dateOfBirth.label',
            'required' => false,
        ]);

        $formMapper->end();
        $formMapper->with('admin.order.details', ['class' => 'col-md-3']);

        $formMapper->add('product', ModelType::class, [
            'class' => $this->getConfigurationPool()->getContainer()->getParameter('app.entity_product'),
            'btn_add' => false,
            'property' => 'name',
            'label' => 'admin.order_item.product.label',
        ]);
        $formMapper->add('order', ModelType::class, [
            'class' => $this->getConfigurationPool()->getContainer()->getParameter('app.entity_order'),
            'label' => 'admin.order_item.order.label',
            'btn_add' => false,
        ]);
        $formMapper->add('canceledAt', DateTimePickerType::class, [
            'label' => 'admin.order_item.canceledAt.label',
            'required' => false,
        ]);
        $formMapper->end();

        $formMapper->with('admin.product.inscription', ['class' => 'col-md-3']);

        $formMapper->add('priceHt', null, [
            'label' => 'admin.order_item.price.label',
            'help' => 'admin.order_item.price.help',
        ]);
        $formMapper->add('vat', null, [
            'label' => 'admin.order_item.vat.label',
            'help' => 'admin.order_item.vat.help',
        ]);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('product.id', null, [
            'label' => 'admin.order_item.product_id.label',
        ]);
        $datagridMapper->add('product.name', null, [
            'label' => 'admin.order_item.product.label',
        ]);

        $datagridMapper->add('canceledAt', null, [
            'label' => 'admin.order_item.canceledAt.label',
        ]);
        $datagridMapper->add('firstname', null, [
            'label' => 'admin.user.firstname.label',
        ]);
        $datagridMapper->add('lastname', null, [
            'label' => 'admin.user.lastname.label',
        ]);
        $datagridMapper->add('dateOfBirth', null, [
            'label' => 'admin.user.dateOfBirth.label',
        ]);
        $datagridMapper->add('email', null, [
            'label' => 'admin.user.email.label',
        ]);
        $datagridMapper->add('phone', null, [
            'label' => 'admin.user.phone.label',
        ]);
        $datagridMapper->add('order.paid', null, [
            'label' => 'admin.order.paid.label',
        ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('product', null, [
            'associated_property' => 'name',
            'label' => 'admin.order_item.product.label',
        ]);
        $listMapper->add('canceledAt', null, [
            'label' => 'admin.order_item.canceledAt.label',
        ]);
        $listMapper->add('firstname', null, [
            'label' => 'admin.user.firstname.label',
        ]);
        $listMapper->add('lastname', null, [
            'label' => 'admin.user.lastname.label',
        ]);
        $listMapper->add('dateOfBirth', null, [
            'label' => 'admin.user.dateOfBirth.label',
        ]);
        $listMapper->add('email', null, [
            'label' => 'admin.user.email.label',
        ]);
        $listMapper->add('phone', null, [
            'label' => 'admin.user.phone.label',
        ]);
        $listMapper->add('order.paid', null, [
            'label' => 'admin.order.paid.label',
        ]);
        $listMapper->add('_action', null, [
            'actions' => [
                'edit' => [],
            ],
            'row_align' => 'right',
            'header_class' => 'text-right',
            'label' => 'admin.action',
        ]);
    }
}
