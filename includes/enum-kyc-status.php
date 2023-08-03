<?php

/**
 *  A simple enum-like class for Know your Customer status
 *
 * @since      1.0.0
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/includes
 * @author     Toni Ruiz <info@toniruiz.es>
 * TODO: Replace with enum for PHP 8.1
 */
class Kyc_Status
{
    const FAILED = 'FAILED';
    const PENDING = 'PENDING';
    const SUCCESS = 'SUCCESS';
}
