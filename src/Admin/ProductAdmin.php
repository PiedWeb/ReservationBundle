<?php

namespace PiedWeb\ReservationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;

class ProductAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'departureDate',
    ];

    public function configure()
    {
        //$this->setTemplate('show', '@PiedWebReservation/admin/show_reservation.html.twig');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $now = new \DateTime();

        $formMapper->with('admin.product.name.label');
        $formMapper->add('name', TextType::class, [
            'label' => ' ',
            'label_attr' => ['style' => 'display:none'],
            'help' => 'admin.product.name.help',
        ]);
        $formMapper->end();

        $formMapper->with('admin.product.inscription', ['class' => 'col-md-4']);
        $formMapper->add('participantNumber', IntegerType::class, [
            'required' => false,
            'label' => 'admin.product.participantNumber.label',
            'help' => '<hr>',
        ]);

        $formMapper->add('priceHt', null, [
            'label' => 'admin.product.price.label',
            'help' => 'admin.product.price.help',
        ]);
        $formMapper->add('vat', null, [
            'label' => 'admin.product.vat.label',
            'help' => 'admin.product.vat.help',
        ]);
        $formMapper->end();

        $formMapper->with('admin.product.time.label', ['class' => 'col-md-4']);
        $formMapper->add('departureDate', DateTimePickerType::class, [
                        'dp_min_date' => '1-1-'.$now->format('Y'),
                        'label' => 'admin.product.departureDate.label',
                        'help' => 'admin.product.departureDate.help',
                    ]);
        $formMapper->add('time', TextType::class, [
            'label' => 'admin.product.time.label',
            'help' => 'admin.product.time.help',
            'required' => false,
        ]);
        $formMapper->end();

        $formMapper->with('admin.product.page.label', ['class' => 'col-md-4']);
        $formMapper->add('page', ModelType::class, [
            'class' => $this->getConfigurationPool()->getContainer()->getParameter('app.entity_page'),
            'property' => 'title',
            'label' => 'admin.product.page.label',
            'help' => 'admin.product.page.help',
            'required' => false,
            'btn_add' => false,
        ]);
        $formMapper->add('specifications', TextType::class, [
            'label' => 'admin.product.description.label',
            'required' => false,
            'help' => 'admin.product.description.help',
        ]);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name', null, [
            'label' => 'admin.product.name.short_label',
        ]);
        $datagridMapper->add('page', null, [
            'label' => 'admin.product.page.label',
        ]);
        /*
         * todo: filter data in sonata admin
        $datagridMapper->add('departureDate', null, [
            'label' => 'admin.product.departureDate.label',
        ]);
        /**/
        $datagridMapper->add('time', null, [
            'label' => 'admin.product.time.label',
        ]);
        $datagridMapper->add('priceHt', null, [
            'label' => 'admin.product.price.label',
        ]);
        $datagridMapper->add('participantNumber', null, [
            'label' => 'admin.product.participantNumber.label',
        ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('name', null, [
            'label' => 'admin.product.name.short_label',
        ]);
        $listMapper->add('page', null, [
            'label' => 'admin.product.page.label',
        ]);
        $listMapper->add('departureDate', null, [
            'label' => 'admin.product.departureDate.label',
            'format' => 'd/m/Y à H:i',
        ]);
        $listMapper->add('time', null, [
            'label' => 'admin.product.time.label',
        ]);
        $listMapper->add('priceHt', null, [
            'label' => 'admin.product.price.label',
        ]);
        $listMapper->add('participantNumber', null, [
            'label' => 'admin.product.participantNumber.label',
        ]);
        $listMapper->add('_action', null, [
            'actions' => [
                'edit' => [],
                'form' => [
                    'template' => '@PiedWebReservation/admin/CRUD/list_form.html.twig',
                ],
            ],
            'row_align' => 'right',
            'header_class' => 'text-right',
            'label' => 'admin.action',
        ]);
    }
}
