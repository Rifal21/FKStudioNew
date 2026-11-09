<?php

namespace App\Services\Shop;

use RuntimeException;

/** A problem the customer or admin can act on; the message is safe to show as-is. */
class ShopException extends RuntimeException
{
}
