<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type;

use Eccube\Form\DataTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OrderType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->app['config'];

        $builder
            ->add('name', 'name', array(
                'required' => true,
                'options' => array(
                    'attr' => array(
                        'maxlength' => $config['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $config['stext_len'])),
                    ),
                ),
            ))
            ->add('kana', 'name', array(
                'options' => array(
                    'attr' => array(
                        'maxlength' => $config['stext_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array('max' => $config['stext_len'])),
                    ),
                ),
            ))
            ->add('company_name', 'text', array(
                'label' => '会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $config['stext_len'],
                    ))
                ),
            ))
            ->add('zip', 'zip', array(
                'zip01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Regex(array('pattern' => '/^\d{3}$/'))
                    ),
                ),
                'zip02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Regex(array('pattern' => '/^\d{4}$/'))
                    ),
                ),
            ))
            ->add('address', 'address', array(
                'addr01_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
                'addr02_options' => array(
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\Length(array(
                            'max' => $config['mtext_len'],
                        )),
                    ),
                ),
            ))
            ->add('email', 'email', array(
                'label' => 'メールアドレス',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ),
            ))
            ->add('tel', 'tel', array())
            ->add('fax', 'tel', array(
                'label' => 'FAX番号',
                'required' => false,
            ))
            ->add('message', 'textarea', array(
                'label' => '備考',
                'required' => false,
            ))
            ->add('subtotal')
            ->add('discount')
            ->add('delivery_fee_total')
            ->add('charge')
            ->add('tax')
            ->add('total')
            ->add('payment_total')
            ->add('payment_method', 'hidden')
            ->add('note', 'textarea')
            ->add('OrderStatus', 'entity', array(
                'class' => 'Eccube\Entity\Master\OrderStatus',
                'property' => 'name',
                'empty_value' => false,
                'empty_data' => null,
            ))
            ->add('Payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'empty_value' => false,
                'empty_data' => null,
            ))
            ->add('OrderDetails', 'collection', array(
                'type' => new OrderDetailType($this->app)
            ))
            ->add('Shippings', 'collection', array(
                'type' => new ShippingType($this->app)
            ))
        ;
        $builder
            ->add($builder->create('Customer', 'hidden')
                ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
                    $this->app['orm.em'],
                    '\Eccube\Entity\Customer'
                )));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Order',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order';
    }
}
