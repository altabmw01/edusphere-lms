<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEACHER = 'teacher';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_STUDENT = 'student';

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'avatar', 'bio',
        'address', 'city', 'country', 'social_links', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // --- Relationships ---
    public function teacherProfile(): HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    public function coursesCreated(): HasMany
    {
        return $this->hasMany(Course::class, 'created_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function bookPurchases(): HasMany
    {
        return $this->hasMany(BookPurchase::class);
    }

    /** Batches this user teaches (only meaningful when role = teacher). */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'teacher_id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    // --- Scopes ---
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // --- Helpers ---
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    public function hasPurchasedCourse(int $courseId): bool
    {
        return $this->enrollments()->where('course_id', $courseId)->exists();
    }

    public function hasPurchasedBook(int $bookId): bool
    {
        return $this->bookPurchases()->where('book_id', $bookId)->exists();
    }

    public function avatarUrl(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563EB&color=fff';
    }
}
