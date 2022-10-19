<?php

namespace MOIREI\EventTracking\Contracts;

interface EventUser
{
    /**
     * Get the user's ID
     *
     * @return string
     */
    public function getId();

    /**
     * Get the user's name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the user's first name
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Get the user's last name
     *
     * @return string
     */
    public function getLastName();

    /**
     * Get the user's email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Get the date the user was created
     *
     * @return string
     */
    public function getCreatedDate();

    /**
     * Get (additional) user properties
     *
     * @return array
     */
    public function getProperties();
}
