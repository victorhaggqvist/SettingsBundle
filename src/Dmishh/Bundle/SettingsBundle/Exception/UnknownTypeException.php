<?php
/**
 * User: Victor Häggqvist
 * Date: 12/18/15
 * Time: 12:59 AM
 */

namespace Dmishh\Bundle\SettingsBundle\Exception;


class UnknownTypeException extends SettingsException {

    /**
     * InvalidTypeException constructor.
     */
    public function __construct($type)
    {
        parent::__construct(sprintf('Unknown type "%s"', $type));
    }
}
