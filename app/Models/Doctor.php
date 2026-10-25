<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $slug
 * @property string $specialization
 * @property string|null $designation
 * @property string|null $qualifications
 * @property string|null $bio
 * @property string|null $avatar
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $chamber_address
 * @property string|null $telemedicine_link
 * @property string|null $bmdc_number
 * @property string|null $division
 * @property string|null $district
 * @property string|null $upazila
 * @property string|null $city
 * @property float $consultation_fee
 * @property float|null $telemedicine_fee
 * @property int|null $experience_years
 * @property array<int, string>|null $available_days
 * @property string|null $start_time
 * @property string|null $end_time
 * @property int|null $slot_duration_minutes
 * @property bool $is_approved
 * @property bool $is_verified
 * @property bool $is_featured
 * @property bool $is_emergency_available
 * @property bool $free_consultation_enabled
 * @property array<int, string>|null $free_days
 * @property string|null $free_start_time
 * @property string|null $free_end_time
 * @property int|null $free_patient_limit
 * @property string|null $free_chamber_address
 * @property string|null $free_service_note
 * @property array<string, mixed>|null $portfolio_data
 * @property string|null $membership_plan
 * @property Carbon|null $membership_expires_at
 * @property-read string $avatar_url
 * @property-read string $gender
 * @property-read array<string, mixed> $portfolio_settings
 * @property-read bool $is_pro
 * @property-read User|null $user
 * @property-read DoctorChamber|null $primaryChamber
 * @property-read Collection<int, DoctorChamber> $chambers
 * @property-read Collection<int, Appointment> $appointments
 * @property-read Collection<int, Prescription> $prescriptions
 * @property-read Collection<int, Payment> $payments
 * @property-read Collection<int, DoctorAssistant> $assistants
 */
class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'specialization',
        'designation',
        'qualifications',
        'bio',
        'avatar',
        'email',
        'phone',
        'chamber_address',
        'telemedicine_link',
        'bmdc_number',
        'division',
        'district',
        'upazila',
        'city',
        'consultation_fee',
        'telemedicine_fee',
        'experience_years',
        'available_days',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'is_approved',
        'is_verified',
        'is_featured',
        'is_emergency_available',
        'free_consultation_enabled',
        'free_days',
        'free_start_time',
        'free_end_time',
        'free_patient_limit',
        'free_chamber_address',
        'free_service_note',
        'portfolio_data',
        'membership_plan',
        'membership_expires_at',
    ];

    protected $appends = ['avatar_url', 'gender', 'portfolio_settings', 'is_pro'];

    protected function casts(): array
    {
        return [
            'available_days' => 'array',
            'free_days' => 'array',
            'portfolio_data' => 'array',
            'consultation_fee' => 'decimal:2',
            'telemedicine_fee' => 'decimal:2',
            'is_approved' => 'boolean',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_emergency_available' => 'boolean',
            'free_consultation_enabled' => 'boolean',
            'free_patient_limit' => 'integer',
            'membership_expires_at' => 'datetime',
        ];
    }

    /**
     * Determine if doctor has active PRO / VIP membership or is featured.
     */
    public function getIsProAttribute(): bool
    {
        return in_array($this->membership_plan ?? 'free', ['pro', 'vip'], true) || (bool) $this->is_featured;
    }

    /**
     * Determine if doctor is male or female based on name, prefixes, qualifications, and specialties.
     */
    public function getGenderAttribute(): string
    {
        $text = ($this->name ?? '').' '.($this->specialization ?? '').' '.($this->qualifications ?? '');
        $pattern = '/(মোছাঃ|মোছা\.|মোসাম্মৎ|বেগম|নাজনীন|সুলতানা|তাহমিনা|জান্নাত|শামীমা|রোকসানা|ফাতেমা|নুসরাত|সাদিয়া|মারুফা|আফরোজা|ফারহানা|তানজিলা|উম্মে|লায়লা|শারমিন|সাবরিনা|রুমানা|বিলকিস|রেহানা|শিরীন|লতিফা|ফারিয়া|তাসনিম|রাবেয়া|হাসিনা|আমেনা|খাদিজা|আয়েশা|সৈয়দা|মেহেরুন|শিরিন|নাসরিন|ইয়াসমিন|জাহান|আক্তার|খাতুন|হালিমা|গাইনী|স্ত্রী\s*ও\s*প্রসূতি|গাইনোকোলজিস্ট|Gynecol|Gynaecol|Obstetric|Begum|Sultana|Fatema|Nazneen|Farhana|Faria|Akter|Khatun|Nusrat|Sadia|Tahmina|Jannat|Shamima|Roksana|Shabnam|Sharmin|Sabrina|Rumana|Nasreen|Yasmin|Tasnim|Rabeya|Hasina|Amena|Khadija|Ayesha|Mst|Most|Mrs)/iu';

        return preg_match($pattern, $text) ? 'female' : 'male';
    }

    /**
     * Get clean resolved avatar URL or gender-based default avatar.
     */
    public function getAvatarUrlAttribute(): string
    {
        $avatar = trim($this->avatar ?? '');
        if (! empty($avatar)) {
            if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
                return $avatar;
            }

            return '/'.ltrim($avatar, '/');
        }

        return $this->gender === 'female'
            ? '/images/team/default-doctor-female.svg'
            : '/images/team/default-doctor-male.svg';
    }

    /**
     * Get dynamic portfolio data with smart defaults for all sections and short videos.
     *
     * @return array<string, mixed>
     */
    public function getPortfolioSettingsAttribute(): array
    {
        $raw = $this->portfolio_data;
        /** @var array<string, mixed> $data */
        $data = is_array($raw) ? $raw : [];
        /** @var array<string, mixed> $hero */
        $hero = is_array($data['hero'] ?? null) ? $data['hero'] : [];
        /** @var array<string, mixed> $about */
        $about = is_array($data['about'] ?? null) ? $data['about'] : [];
        /** @var array<string, mixed> $contact */
        $contact = is_array($data['contact'] ?? null) ? $data['contact'] : [];

        return [
            'template' => (string) ($data['template'] ?? 'template_1_dark'),
            'hero' => array_merge([
                'eyebrow' => $hero['eyebrow'] ?? ($this->specialization ? "{$this->specialization} বিশেষজ্ঞ" : 'বিশেষজ্ঞ চিকিৎসক'),
                'hero_photo' => $hero['hero_photo'] ?? $this->avatar_url,
                'stamp_title' => $hero['stamp_title'] ?? ($this->experience_years ? "{$this->experience_years}+ বছর অভিজ্ঞতা" : 'অভিজ্ঞ চিকিৎসক'),
                'stamp_sub' => $hero['stamp_sub'] ?? 'রোগীর সন্তুষ্টি ও সেবা',
                'cta_phone' => $hero['cta_phone'] ?? ($this->phone ?: ''),
                'badges' => ! empty($hero['badges']) && is_array($hero['badges']) ? $hero['badges'] : array_values(array_filter(array_map('trim', explode(',', (string) ($this->qualifications ?? ''))))),
            ], $hero),
            'about' => array_merge([
                'heading' => $about['heading'] ?? ($this->name ? "{$this->name}-এর পরিচিতি ও চিকিৎসা দর্শন" : 'পরিচিতি ও চিকিৎসা দর্শন'),
                'about_photo' => $about['about_photo'] ?? $this->avatar_url,
                'bio' => $about['bio'] ?? ($this->bio ?: ''),
            ], $about),
            'short_videos' => ! empty($data['short_videos']) && is_array($data['short_videos']) ? $data['short_videos'] : [],
            'services' => ! empty($data['services']) && is_array($data['services']) ? $data['services'] : [],
            'education' => ! empty($data['education']) && is_array($data['education']) ? $data['education'] : [],
            'tips' => ! empty($data['tips']) && is_array($data['tips']) ? $data['tips'] : [],
            'testimonials' => ! empty($data['testimonials']) && is_array($data['testimonials']) ? $data['testimonials'] : [],
            'faqs' => ! empty($data['faqs']) && is_array($data['faqs']) ? $data['faqs'] : [],
            'gallery' => ! empty($data['gallery']) && is_array($data['gallery']) ? $data['gallery'] : [],
            'contact' => array_merge([
                'assistant_phone' => $contact['assistant_phone'] ?? '',
                'emergency_phone' => $contact['emergency_phone'] ?? ($this->phone ?: ''),
                'email' => $contact['email'] ?? ($this->email ?: ''),
                'home_address' => $contact['home_address'] ?? ($this->chamber_address ?: ''),
                'whatsapp' => $contact['whatsapp'] ?? ($this->phone ?: ''),
                'facebook' => $contact['facebook'] ?? '',
                'youtube' => $contact['youtube'] ?? '',
            ], $contact),
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Appointment, $this>
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * @return HasMany<Prescription, $this>
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<DoctorAssistant, $this>
     */
    public function assistants(): HasMany
    {
        return $this->hasMany(DoctorAssistant::class);
    }

    /**
     * @return HasMany<DoctorChamber, $this>
     */
    public function chambers(): HasMany
    {
        return $this->hasMany(DoctorChamber::class)->orderByDesc('is_primary')->orderBy('id');
    }

    /**
     * @return HasOne<DoctorChamber, $this>
     */
    public function primaryChamber(): HasOne
    {
        return $this->hasOne(DoctorChamber::class)->where('is_primary', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency_available', true);
    }

    public function scopeFreeConsultation($query)
    {
        return $query->where('free_consultation_enabled', true);
    }

    /**
     * Check if doctor provides free consultation on a given date.
     */
    public function isFreeOnDate(string $date): bool
    {
        if (! $this->free_consultation_enabled) {
            return false;
        }

        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->format('l');

        $freeDays = is_array($this->free_days) && ! empty($this->free_days)
            ? $this->free_days
            : ['Friday'];

        return in_array($dayOfWeek, $freeDays);
    }

    /**
     * Calculate remaining quota for free patient serials on a given date.
     */
    public function getFreeConsultationRemainingQuota(string $date): int
    {
        $limit = $this->free_patient_limit ?: 20;

        $bookedCount = $this->appointments()
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->count();

        return max(0, $limit - $bookedCount);
    }

    /**
     * Calculate available time slots for doctor on a given date (optionally for a specific chamber).
     */
    public function getAvailableSlotsForDate(string $date, ?int $chamberId = null): array
    {
        // If a specific chamber is requested, use that chamber's schedule
        if ($chamberId) {
            /** @var DoctorChamber|null $chamber */
            $chamber = $this->chambers()->where('id', $chamberId)->first();
            if ($chamber) {
                return $chamber->getAvailableSlotsForDate($date);
            }
        }

        $dateObj = Carbon::parse($date);
        $dayOfWeek = $dateObj->format('l');
        $isFree = $this->isFreeOnDate($date);

        $availableDays = $this->available_days ?: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // If date is marked as free consultation day, allow it even if not in regular available_days
        if (! $isFree && ! in_array($dayOfWeek, $availableDays)) {
            return [];
        }

        // Determine start and end time (use free hours if free day and set)
        $startStr = ($isFree && ! empty($this->free_start_time)) ? $this->free_start_time : ($this->start_time ?: '09:00:00');
        $endStr = ($isFree && ! empty($this->free_end_time)) ? $this->free_end_time : ($this->end_time ?: '17:00:00');

        $startTime = Carbon::parse($date.' '.$startStr);
        $endTime = Carbon::parse($date.' '.$endStr);
        $interval = ($this->slot_duration_minutes ?: 30);

        $bookedSlots = $this->appointments()
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_time')
            ->toArray();

        $remainingQuota = $isFree ? $this->getFreeConsultationRemainingQuota($date) : 999;

        $slots = [];
        $current = $startTime->copy();
        $isToday = $dateObj->isToday();
        $now = Carbon::now();

        while ($current->lt($endTime)) {
            $formattedTime = $current->format('h:i A');
            $isPast = $isToday && $current->lte($now);
            $isBooked = in_array($formattedTime, $bookedSlots);
            $isQuotaFull = ($isFree && $remainingQuota <= 0);

            $slots[] = [
                'time' => $formattedTime,
                'available' => ! $isBooked && ! $isPast && ! $isQuotaFull,
                'is_booked' => $isBooked,
                'is_past' => $isPast,
                'is_free' => $isFree,
                'is_quota_full' => $isQuotaFull,
            ];

            $current->addMinutes($interval);
        }

        return $slots;
    }
}
