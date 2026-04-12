<?php

namespace App\Models\Property;

use App\Models\Property\Accessors\PropertyAccessors;
use App\Models\Property\Mutators\PropertyMutators;
use App\Models\Property\Relations\PropertyRelations;
use App\Models\Property\Scopes\PropertyScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_LET_AGREED = 'let_agreed';

    public const STATUS_ARCHIVED = 'archived';

    use HasFactory;
    use SoftDeletes;

    protected static function newFactory()
    {
        return \Database\Factories\Property\PropertyFactory::new();
    }

    use PropertyRelations;
    use PropertyAccessors;
    use PropertyMutators;
    use PropertyScopes;

    protected $table = 'properties';

    protected $fillable = [
        'user_id',
        'country_id',
        'city_id',
        'area_id',
        'map_link',
        'latitude',
        'longitude',
        'distance_university_km',
        'distance_transit_km',
        'bed_type',
        'listing_category',
        'property_type',
        'bedrooms',
        'bathrooms',
        'bathroom_type',
        'is_furnished',
        'rent_duration',
        'rent_amount',
        'bills_included',
        'included_bills',
        'min_contract_length',
        'provides_agreement',
        'deposit_required',
        'rent_for',
        'suitable_for',
        'flatmate_vibe',
        'house_rules',
        'amenities',
        'status',
        'capacity',
        'available_beds',
    ];

    protected function casts(): array
    {
        return [
            'is_furnished' => 'boolean',
            'provides_agreement' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'distance_university_km' => 'decimal:2',
            'distance_transit_km' => 'decimal:2',
            'included_bills' => 'array',
            'suitable_for' => 'array',
            'house_rules' => 'array',
            'amenities' => 'array',
            'capacity' => 'integer',
            'available_beds' => 'integer',
        ];
    }
}
