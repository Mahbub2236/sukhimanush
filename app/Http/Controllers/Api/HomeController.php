<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Doctor;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Get aggregate data for SaaS landing page.
     */
    public function index(): JsonResponse
    {
        $heroDoctor = Doctor::approved()
            ->where('slug', 'asst-prof-dr-md-mosharof-hossain-ibnsina-3165')
            ->with(['chambers' => function ($q) {
                $q->where('is_active', true)->orderByDesc('is_primary');
            }])
            ->first() ?? Doctor::approved()->featured()->first();

        $featuredDoctors = Doctor::approved()
            ->featured()
            ->take(6)
            ->get();

        $specializations = Doctor::approved()
            ->select('specialization')
            ->distinct()
            ->pluck('specialization');

        $testimonials = Testimonial::all();
        $recentBlogs = BlogPost::published()->take(3)->get();

        $stats = [
            'total_doctors' => Doctor::approved()->count(),
            'happy_patients' => '15,000+',
            'cities_covered' => '12+',
            'satisfaction_rate' => '99.4%',
        ];

        return response()->json([
            'hero_doctor' => $heroDoctor,
            'featured_doctors' => $featuredDoctors,
            'specializations' => $specializations,
            'testimonials' => $testimonials,
            'recent_blogs' => $recentBlogs,
            'stats' => $stats,
        ]);
    }
}
