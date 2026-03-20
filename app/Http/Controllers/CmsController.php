<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\HeroSection;
use App\Models\AboutSection;
use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    public function dashboard()
    {
        $servicesCount = Service::count();
        $projectsCount = Project::count();
        $testimonialsCount = Testimonial::count();
        
        return view('dashboard', compact('servicesCount', 'projectsCount', 'testimonialsCount'));
    }

    // Site Settings
    public function editSettings()
    {
        $settings = SiteSetting::first();
        return view('dashboard.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = SiteSetting::first();
        $data = $request->except(['_token', 'site_logo', 'site_favicon', 'social']);
        
        if ($request->has('social')) {
            $data['social_links'] = json_encode($request->social);
        }
        
        if ($request->hasFile('site_logo')) {
            $data['site_logo'] = $request->file('site_logo')->store('site', 'public');
        }
        if ($request->hasFile('site_favicon')) {
            $data['site_favicon'] = $request->file('site_favicon')->store('site', 'public');
        }

        $settings->update($data);
        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    // Hero Section
    public function editHero()
    {
        $hero = HeroSection::first();
        $slides = \App\Models\HeroSlide::orderBy('order')->get();
        return view('dashboard.hero', compact('hero', 'slides'));
    }

    public function updateHero(Request $request)
    {
        $hero = HeroSection::first();
        $data = $request->except(['_token', 'image']);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('hero', 'public');
        }

        $hero->update($data);
        return redirect()->back()->with('success', 'Hero updated successfully');
    }

    public function storeHeroSlide(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hero/slides', 'public');
            \App\Models\HeroSlide::create(['image' => $path, 'order' => \App\Models\HeroSlide::count()]);
        }
        return redirect()->back()->with('success', 'Slide added');
    }

    public function deleteHeroSlide(\App\Models\HeroSlide $slide)
    {
        Storage::disk('public')->delete($slide->image);
        $slide->delete();
        return redirect()->back()->with('success', 'Slide deleted');
    }

    // About Section
    public function editAbout()
    {
        $about = AboutSection::first();
        $slides = \App\Models\AboutSlide::all();
        return view('dashboard.about', compact('about', 'slides'));
    }

    public function updateAbout(Request $request)
    {
        $about = AboutSection::first();

        // Only pick the valid translatable columns
        $data = $request->only(['title_id', 'title_en', 'description_id', 'description_en']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('about', 'public');
        }

        // Stats to JSON
        $stats = [];
        if ($request->has('stats_labels_id')) {
            foreach ($request->stats_labels_id as $key => $val) {
                $stats[] = [
                    'label_id' => $val,
                    'label_en' => $request->stats_labels_en[$key],
                    'value'    => $request->stats_values[$key],
                ];
            }
        }
        $data['stats'] = json_encode($stats);

        $about->update($data);
        return redirect()->back()->with('success', 'About updated successfully');
    }

    public function storeAboutSlide(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('about/slides', 'public');
            \App\Models\AboutSlide::create(['image' => $path]);
        }
        return redirect()->back()->with('success', 'Slide added');
    }

    public function deleteAboutSlide(\App\Models\AboutSlide $slide)
    {
        Storage::disk('public')->delete($slide->image);
        $slide->delete();
        return redirect()->back()->with('success', 'Slide deleted');
    }

    // Services CRUD (simplified)
    public function servicesIndex()
    {
        $services = Service::orderBy('order')->get();
        return view('dashboard.services.index', compact('services'));
    }

    public function storeService(Request $request)
    {
        Service::create($request->all());
        return redirect()->route('dashboard.services.index')->with('success', 'Service added');
    }

    public function updateService(Request $request, Service $service)
    {
        $service->update($request->all());
        return redirect()->back()->with('success', 'Service updated');
    }

    public function deleteService(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('success', 'Service deleted');
    }

    // Projects CRUD (simplified)
    public function projectsIndex()
    {
        $projects = Project::orderBy('order')->get();
        return view('dashboard.projects.index', compact('projects'));
    }

    public function storeProject(Request $request)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('projects', 'public');
        }
        Project::create($data);
        return redirect()->back()->with('success', 'Project added');
    }

    public function deleteProject(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('success', 'Project deleted');
    }

    public function testimonialsIndex()
    {
        $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
        return view('dashboard.testimonials.index', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $data = $request->except(['avatar']);
        
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        Testimonial::create($data);
        return redirect()->back()->with('success', 'Testimonial added successfully');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $data = $request->except(['avatar']);
        
        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar) {
                Storage::disk('public')->delete($testimonial->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $testimonial->update($data);
        return redirect()->back()->with('success', 'Testimonial updated successfully');
    }

    public function deleteTestimonial(Testimonial $testimonial)
    {
        if ($testimonial->avatar) {
            Storage::disk('public')->delete($testimonial->avatar);
        }
        $testimonial->delete();
        return redirect()->back()->with('success', 'Testimonial deleted successfully');
    }

    // Clients CRUD
    public function clientsIndex()
    {
        $clients = Client::orderBy('order')->get();
        return view('dashboard.clients.index', compact('clients'));
    }

    public function storeClient(Request $request)
    {
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('clients', 'public');
            Client::create([
                'name'  => $request->name,
                'logo'  => $path,
                'url'   => $request->url,
                'order' => Client::count(),
            ]);
        }
        return redirect()->back()->with('success', 'Client added');
    }

    public function deleteClient(Client $client)
    {
        Storage::disk('public')->delete($client->logo);
        $client->delete();
        return redirect()->back()->with('success', 'Client removed');
    }
}
