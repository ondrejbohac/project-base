<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Payment\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentFormType extends AbstractType {
	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	public function __construct(array $payments) {
		$this->payments = $payments;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParent() {
		return FormType::SINGLE_CHECKBOX_CHOICE;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'payment';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'choice_list' => new ObjectChoiceList($this->payments, 'name', [], null, 'id'),
			'data_class' => Payment::class,
		]);
	}

}
