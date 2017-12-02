<?php
namespace Application;

use Domain\Model\ContactForm;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ContactFormCommand
{
    /**
     * @var ContactForm
     */
    private $contactForm;

    function __construct(ContactForm $contactForm)
    {

        $this->contactForm = $contactForm;
    }

    function getContactForm()
    {
        return $this->contactForm;
    }

}