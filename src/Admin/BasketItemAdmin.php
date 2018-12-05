<?php

namespace PiedWeb\ReservationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BasketItemAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'addedAt',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('basket.user.email', null, [
            'label' => 'admin.basket_item.user.label',
        ]);
        $listMapper->add('basket.id', null, [
            'label' => 'admin.basket_item.basket_id.label',
        ]);
        $listMapper->add('addedAt', null, [
            'label' => 'admin.basket_item.addedAt.label',
        ]);
        $listMapper->add('product', null, [
            'associated_property' => 'name',
            'label' => 'admin.order_item.product.label',
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
    }
}
