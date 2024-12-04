<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingPageController extends Controller
{
    public function index()
    {
        return Inertia::render('LandingPage', [
            // Kirim data yang dibutuhkan
            'appName' => config('app.name'),
            'features' => [
                [
                    'title' => 'Fitur 1',
                    'description' => 'Deskripsi fitur pertama'
                ],
                [
                    'title' => 'Fitur 2',
                    'description' => 'Deskripsi fitur kedua'
                ]
            ]
        ]);
    }
}
