<?php

namespace App\Http\Controllers\Client\Home;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredListings = [
            [
                'title' => 'The Oxford Studio',
                'uni' => 'UCL (1.2 miles)',
                'area' => 'Islington, London',
                'rating' => '4.9',
                'price' => '285',
                'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'slug' => 'the-oxford-studio',
            ],
            [
                'title' => 'Nova Premium En-suite',
                'uni' => "King's College (0.8 miles)",
                'area' => 'Camden Town, London',
                'rating' => '4.8',
                'price' => '320',
                'image' => 'https://images.unsplash.com/photo-1502672260266-1c1c294036f3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'slug' => 'nova-premium-ensuite',
            ],
            [
                'title' => 'Apex Student Living',
                'uni' => 'Imperial College (2.5 miles)',
                'area' => 'Wembley, London',
                'rating' => '4.7',
                'price' => '210',
                'image' => 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'slug' => 'apex-student-living',
            ],
            [
                'title' => 'The Chapter Loft',
                'uni' => 'LSE (0.5 miles)',
                'area' => 'Spitalfields, London',
                'rating' => '5.0',
                'price' => '395',
                'image' => 'https://images.unsplash.com/photo-1536376072261-38c75010e6c9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'slug' => 'the-chapter-loft',
            ],
        ];

        return view('client.home.index', compact('featuredListings'));
    }
}
