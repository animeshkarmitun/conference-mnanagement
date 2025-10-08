<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Use ConferenceDoc instead. This model is kept for backward compatibility.
 */
class ConferenceKit extends ConferenceDoc
{
    // This model now extends ConferenceDoc for backward compatibility
    // The table has been renamed from conference_kits to conference_docs
}
