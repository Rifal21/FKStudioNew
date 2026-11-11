<?php

namespace App\Services\Promo;

use RuntimeException;

/** A failure the admin can read and act on (no key, busy API, refused request). */
class PromoException extends RuntimeException
{
}
