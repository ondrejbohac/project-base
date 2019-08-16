<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\Model\Administrator;

use Shopsys\FrameworkBundle\Model\Administrator\Administrator as BaseAdministrator;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorData as BaseAdministratorData;
use Shopsys\FrameworkBundle\Model\Administrator\AdministratorDataFactory as BaseAdministratorDataFactory;

class AdministratorDataFactory extends BaseAdministratorDataFactory
{
    /**
     * @return \Shopsys\ShopBundle\Model\Administrator\AdministratorData
     */
    public function create(): BaseAdministratorData
    {
        return new AdministratorData();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Administrator\Administrator $administrator
     * @return \Shopsys\ShopBundle\Model\Administrator\AdministratorData
     */
    public function createFromAdministrator(BaseAdministrator $administrator): BaseAdministratorData
    {
        $administratorData = new AdministratorData();
        $this->fillFromAdministrator($administratorData, $administrator);

        $administratorData->roles = $administrator->getRoles();

        return $administratorData;
    }
}
