<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'requires_approval',
        'has_special_privileges',
        'display_order',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'has_special_privileges' => 'boolean',
        'display_order' => 'integer',
    ];

    // Relationships
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    public function scopeHasSpecialPrivileges($query)
    {
        return $query->where('has_special_privileges', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    // Get all categories
    public static function getCategories()
    {
        return [
            'presenter' => 'Presenters & Speakers',
            'attendee' => 'Attendees & Participants',
            'commercial' => 'Commercial & Exhibition',
            'staff' => 'Staff & Organization',
            'sponsor' => 'Sponsors & Partners',
            'press' => 'Media & Press',
            'special' => 'Special & VIP',
            'support' => 'Support & Services',
        ];
    }

    // Helper methods for each category
    public function isPresenter()
    {
        return $this->category === 'presenter';
    }

    public function isAttendee()
    {
        return $this->category === 'attendee';
    }

    public function isCommercial()
    {
        return $this->category === 'commercial';
    }

    public function isStaff()
    {
        return $this->category === 'staff';
    }

    public function isSponsor()
    {
        return $this->category === 'sponsor';
    }

    public function isPress()
    {
        return $this->category === 'press';
    }

    public function isSpecial()
    {
        return $this->category === 'special';
    }

    public function isSupport()
    {
        return $this->category === 'support';
    }

    // Get participant types grouped by category
    public static function getGroupedTypes()
    {
        $categories = self::getCategories();
        $grouped = [];

        foreach ($categories as $categoryKey => $categoryName) {
            $grouped[$categoryName] = self::byCategory($categoryKey)->ordered()->get();
        }

        return $grouped;
    }
}
