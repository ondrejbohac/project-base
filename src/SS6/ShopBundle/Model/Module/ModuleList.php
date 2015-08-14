<?php

namespace SS6\ShopBundle\Model\Module;

use SS6\ShopBundle\Component\ConstantList\AbstractTranslatedConstantList;

class ModuleList extends AbstractTranslatedConstantList {

	const ACCESSORIES_ON_BUY = 'accessoriesOnBuy';
	const PRODUCT_FILTER_COUNTS = 'productFilterCounts';

	/**
	 * {@inheritDoc}
	 */
	public function getTranslationsIndexedByValue() {
		return [
			self::ACCESSORIES_ON_BUY => $this->translator->trans('Příslušenství v mezikošíku'),
			self::PRODUCT_FILTER_COUNTS => $this->translator->trans('Počty zboží ve filtru'),
		];
	}

}
