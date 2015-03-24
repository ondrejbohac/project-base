<?php

namespace SS6\ShopBundle\Model\Pricing;

use SS6\ShopBundle\Model\Pricing\InputPriceRecalculationScheduler;

class DelayedPricingSetting {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceRecalculationScheduler
	 */
	private $inputPriceRecalculationScheduler;

	public function __construct(
		PricingSetting $pricingSetting,
		InputPriceRecalculationScheduler $inputPriceRecalculationScheduler
	) {
		$this->pricingSetting = $pricingSetting;
		$this->inputPriceRecalculationScheduler = $inputPriceRecalculationScheduler;
	}

	/**
	 * @param int $inputPriceType
	 */
	public function setInputPriceType($inputPriceType) {
		if (!in_array($inputPriceType, $this->pricingSetting->getInputPriceTypes())) {
			throw new \SS6\ShopBundle\Model\Pricing\Exception\InvalidInputPriceTypeException('Unknow input price type');
		}

		$currentInputPriceType = $this->pricingSetting->getInputPriceType();

		if ($currentInputPriceType != $inputPriceType) {
			switch ($inputPriceType) {
				case PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT:
					$this->inputPriceRecalculationScheduler->scheduleSetInputPricesWithoutVat();
					break;

				case PricingSetting::INPUT_PRICE_TYPE_WITH_VAT:
					$this->inputPriceRecalculationScheduler->scheduleSetInputPricesWithVat();
					break;
			}
		}
	}

}
