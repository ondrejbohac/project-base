<?php

declare(strict_types=1);

namespace Tests\App\Acceptance\acceptance\PageObject\Front;

use Shopsys\FrameworkBundle\Component\Form\TimedFormTypeExtension;
use Tests\App\Acceptance\acceptance\PageObject\AbstractPage;
use Tests\FrameworkBundle\Test\Codeception\FrontCheckbox;

class RegistrationPage extends AbstractPage
{
    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $firstPassword
     * @param string $secondPassword
     */
    public function register($firstName, $lastName, $email, $firstPassword, $secondPassword)
    {
        $this->tester->fillFieldByName('registration_form[firstName]', $firstName);
        $this->tester->fillFieldByName('registration_form[lastName]', $lastName);
        $this->tester->fillFieldByName('registration_form[email]', $email);
        $this->tester->fillFieldByName('registration_form[password][first]', $firstPassword);
        $this->tester->fillFieldByName('registration_form[password][second]', $secondPassword);

        $frontCheckboxClicker = FrontCheckbox::createByCss(
            $this->tester,
            '#registration_form_privacyPolicy'
        );
        $frontCheckboxClicker->check();

        $this->tester->wait(TimedFormTypeExtension::MINIMUM_FORM_FILLING_SECONDS);
        $this->tester->clickByName('registration_form[save]');
    }

    /**
     * @param string $text
     */
    public function seeEmailError($text)
    {
        $this->seeErrorForField('.js-validation-error-list-registration_form_email', $text);
    }

    /**
     * @param string $text
     */
    public function seePasswordError($text)
    {
        $this->seeErrorForField('.js-validation-error-list-registration_form_password_first', $text);
    }

    /**
     * @param string $fieldClass
     * @param string $text
     */
    private function seeErrorForField($fieldClass, $text)
    {
        // Error message might be in popup - wait for animation
        $this->tester->wait(1);
        // Error message might be in fancy title - hover over field
        $this->tester->moveMouseOverByCss($fieldClass);

        $this->tester->seeTranslationFrontend($text, 'validators');
    }
}
