<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\HeroSection;
use App\Models\AboutSection;
use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\HeroSlide;
use App\Models\Client;
use App\Models\Owner;
use App\Models\Package;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::first();
        $hero = HeroSection::first();
        $heroSlides = HeroSlide::orderBy('order')->get();
        $about = AboutSection::first();
        $aboutSlides = \App\Models\AboutSlide::all();
        $services = Service::where('is_active', true)->orderBy('order')->get();
        $projects = Project::orderBy('order')->get();
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
        $clients = Client::orderBy('order')->get();
        $owners = Owner::where('is_active', true)->orderBy('created_at', 'desc')->get();
        $packages = Package::where('is_active', true)->orderBy('order')->get();

        return view('landing.index', compact('settings', 'hero', 'heroSlides', 'about', 'aboutSlides', 'services', 'projects', 'testimonials', 'clients', 'owners', 'packages'));
    }

    public function packagesIndex()
    {
        $settings = SiteSetting::first();
        $about = AboutSection::first();
        $packages = Package::where('is_active', true)->orderBy('order')->get();

        return view('landing.packages', compact('settings', 'about', 'packages'));
    }

    public function projectsIndex()
    {
        $settings = SiteSetting::first();
        $about = AboutSection::first();
        $projects = Project::orderBy('order')->get();

        return view('landing.portfolio', compact('settings', 'about', 'projects'));
    }

    public function showPackage($slug)
    {
        $settings = SiteSetting::first();
        $about = AboutSection::first();
        $package = Package::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        return view('landing.package', compact('settings', 'package', 'about'));
    }

    public function switchLanguage($locale)
    {
        if (in_array($locale, ['id', 'en'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }

    public function blogsIndex()
    {
        $settings = SiteSetting::first();
        $about = AboutSection::first();
        $blogs = BlogPost::with('author')->where('is_published', true)->orderBy('published_at', 'desc')->get();

        return view('landing.blogs.index', compact('settings', 'about', 'blogs'));
    }

    public function showBlog($slug)
    {
        $settings = SiteSetting::first();
        $about = AboutSection::first();
        $blog = BlogPost::with('author')->where('slug', $slug)->where('is_published', true)->firstOrFail();

        // Increment view counter
        $blog->increment('views');
        
        // Fetch up to 3 other related posts
        $relatedBlogs = BlogPost::where('id', '!=', $blog->id)
            ->where('is_published', true)
            ->limit(3)
            ->get();

        return view('landing.blogs.show', compact('settings', 'about', 'blog', 'relatedBlogs'));
    }
}
