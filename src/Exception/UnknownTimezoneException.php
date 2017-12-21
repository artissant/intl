<?php

namespace CommerceGuys\Intl\Exception;

/**
 * This exception is thrown when an unknown timezone ID is passed to the
 * TimezoneRepository.
 */
class UnknownTimezoneException extends InvalidArgumentException implements ExceptionInterface
{
}
