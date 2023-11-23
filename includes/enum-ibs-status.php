<?php

/**
 *  A simple enum-like class for Know your Customer status
 *
 * @since      1.0.0
 * TODO: Replace with enum for PHP 8.1
 */
class Kyc_Status
{
    const FAILED = 'FAILED';
    const PENDING = 'PENDING';
    const SUCCESS = 'SUCCESS';
}

/**
 *  A simple enum-like class for Evidence status
 *
 * @since      1.2.0
 * TODO: Replace with enum for PHP 8.1
 */
class Evidence_Status
{
    const CERTIFIED = 'CERTIFIED';
    const WAITING = 'WAITING';
}
