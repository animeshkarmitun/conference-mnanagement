<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Use ConferenceDocItem instead. This model is kept for backward compatibility.
 */
class ConferenceKitItem extends ConferenceDocItem
{
    // This model now extends ConferenceDocItem for backward compatibility
    // The table has been renamed from conference_kit_items to conference_doc_items
    // The foreign key has been renamed from kit_id to doc_id
}
