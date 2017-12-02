<?php

namespace Domain\Model;

/**
 *
 * @author Daniel Vaqueiro <danielvc4 at gmail.com>
 */
class ContactForm
{
    private $comment;
    private $email;
    private $name;

    function __construct($name, $email, $comment)
    {

        $this->name = $name;
        $this->email = $email;
        $this->comment = $comment;
    }

    function getComment()
    {
        return $this->comment;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getName()
    {
        return $this->name;
    }

    function setComment($comment)
    {
        $this->comment = $comment;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setName($name)
    {
        $this->name = $name;
    }
}