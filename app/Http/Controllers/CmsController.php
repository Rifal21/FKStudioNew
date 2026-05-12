<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\HeroSection;
use App\Models\AboutSection;
use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\Client;
use App\Models\Owner;
use App\Models\Package;
use App\Models\Invoice;
use App\Traits\NextcloudStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsController extends Controller
{
    use NextcloudStorage;

    public function dashboard()
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('user.orders');
        }

        $servicesCount = Service::count();
        $projectsCount = Project::count();
        $testimonialsCount = Testimonial::count();
        $ownersCount = Owner::count();
        $packagesCount = Package::count();
        $invoicesCount = Invoice::count();
        
        return view('dashboard', compact('servicesCount', 'projectsCount', 'testimonialsCount', 'ownersCount', 'packagesCount', 'invoicesCount'));
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
        $data = $request->except(['_token', 'site_logo', 'site_favicon', 'social', 'remove_invoice_qris', 'og_image']);
        
        if ($request->has('social')) {
            $data['social_links'] = $request->social;
        }

        
        if ($request->hasFile('site_logo')) {
            if ($settings->site_logo) $this->deleteFromNextcloud($settings->site_logo);
            $data['site_logo'] = $this->uploadToNextcloud($request->file('site_logo'), 'site');
        }
        if ($request->hasFile('site_favicon')) {
            if ($settings->site_favicon) $this->deleteFromNextcloud($settings->site_favicon);
            $data['site_favicon'] = $this->uploadToNextcloud($request->file('site_favicon'), 'site');
        }
        if ($request->hasFile('og_image')) {
            if ($settings->og_image) $this->deleteFromNextcloud($settings->og_image);
            $data['og_image'] = $this->uploadToNextcloud($request->file('og_image'), 'site');
        }
        if ($request->hasFile('invoice_logo')) {
            if ($settings->invoice_logo) $this->deleteFromNextcloud($settings->invoice_logo);
            $data['invoice_logo'] = $this->uploadToNextcloud($request->file('invoice_logo'), 'invoices');
        }
        if ($request->hasFile('invoice_signature')) {
            if ($settings->invoice_signature) $this->deleteFromNextcloud($settings->invoice_signature);
            $data['invoice_signature'] = $this->uploadToNextcloud($request->file('invoice_signature'), 'invoices');
        }
        if ($request->hasFile('invoice_qris')) {
            if ($settings->invoice_qris) $this->deleteFromNextcloud($settings->invoice_qris);
            $data['invoice_qris'] = $this->uploadToNextcloud($request->file('invoice_qris'), 'invoices');
        } elseif ($request->remove_invoice_qris == '1') {
            if ($settings->invoice_qris) {
                $this->deleteFromNextcloud($settings->invoice_qris);
                $data['invoice_qris'] = null;
            }
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
            if ($hero->image) $this->deleteFromNextcloud($hero->image);
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'hero');
        }


        $hero->update($data);
        return redirect()->back()->with('success', 'Hero updated successfully');
    }

    public function storeHeroSlide(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $this->uploadToNextcloud($request->file('image'), 'hero/slides');
            \App\Models\HeroSlide::create(['image' => $path, 'order' => \App\Models\HeroSlide::count()]);
        }

        return redirect()->back()->with('success', 'Slide added');
    }

    public function deleteHeroSlide(\App\Models\HeroSlide $slide)
    {
        $this->deleteFromNextcloud($slide->image);
        $slide->delete();

        return redirect()->back()->with('success', 'Slide deleted');
    }

    // About Section
    public function editAbout()
    {
        $about = AboutSection::first();
        $slides = \App\Models\AboutSlide::all();
        $owners = Owner::orderBy('created_at', 'desc')->get();
        return view('dashboard.about', compact('about', 'slides', 'owners'));
    }

    public function updateAbout(Request $request)
    {
        $about = AboutSection::first();

        // Only pick the valid translatable columns
        $data = $request->only([
            'title_id', 'title_en', 
            'description_id', 'description_en',
            'vision_id', 'vision_en',
            'mission_id', 'mission_en'
        ]);

        if ($request->hasFile('image')) {
            if ($about->image) $this->deleteFromNextcloud($about->image);
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'about');
        }


        $about->update($data);
        return redirect()->back()->with('success', 'About updated successfully');
    }

    public function storeAboutSlide(Request $request)
    {
        if ($request->hasFile('image')) {
            $path = $this->uploadToNextcloud($request->file('image'), 'about/slides');
            \App\Models\AboutSlide::create(['image' => $path]);
        }

        return redirect()->back()->with('success', 'Slide added');
    }

    public function deleteAboutSlide(\App\Models\AboutSlide $slide)
    {
        $this->deleteFromNextcloud($slide->image);
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
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'projects');
        }

        Project::create($data);
        return redirect()->back()->with('success', 'Project added');
    }

    public function deleteProject(Project $project)
    {
        if ($project->image) {
            $this->deleteFromNextcloud($project->image);
        }
        $project->delete();

        return redirect()->back()->with('success', 'Project deleted');
    }

    public function updateProject(Request $request, Project $project)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            if ($project->image) {
                $this->deleteFromNextcloud($project->image);
            }
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'projects');
        }

        $project->update($data);
        return redirect()->back()->with('success', 'Project updated successfully');
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
            $data['avatar'] = $this->uploadToNextcloud($request->file('avatar'), 'testimonials');
        }


        Testimonial::create($data);
        return redirect()->back()->with('success', 'Testimonial added successfully');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $data = $request->except(['avatar']);
        
        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar) {
                $this->deleteFromNextcloud($testimonial->avatar);
            }
            $data['avatar'] = $this->uploadToNextcloud($request->file('avatar'), 'testimonials');
        }


        $testimonial->update($data);
        return redirect()->back()->with('success', 'Testimonial updated successfully');
    }

    public function deleteTestimonial(Testimonial $testimonial)
    {
        if ($testimonial->avatar) {
            $this->deleteFromNextcloud($testimonial->avatar);
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
            $path = $this->uploadToNextcloud($request->file('logo'), 'clients');
            Client::create([
                'name'                  => $request->name,
                'logo'                  => $path,
                'url'                   => $request->url,
                'is_server_subscribed'  => $request->has('is_server_subscribed'),
                'billing_date'          => $request->billing_date,
                'subscription_price'    => $request->subscription_price ?? 0,
                'order'                 => Client::count(),
            ]);
        }

        return redirect()->back()->with('success', 'Client added');
    }

    public function updateClient(Request $request, Client $client)
    {
        $data = [
            'name'                  => $request->name,
            'url'                   => $request->url,
            'is_server_subscribed'  => $request->has('is_server_subscribed'),
            'billing_date'          => $request->billing_date,
            'subscription_price'    => $request->subscription_price ?? 0,
        ];

        if ($request->hasFile('logo')) {
            if ($client->logo) $this->deleteFromNextcloud($client->logo);
            $data['logo'] = $this->uploadToNextcloud($request->file('logo'), 'clients');
        }

        $client->update($data);

        return redirect()->back()->with('success', 'Client updated');
    }

    public function deleteClient(Client $client)
    {
        $this->deleteFromNextcloud($client->logo);
        $client->delete();

        return redirect()->back()->with('success', 'Client removed');
    }

    // Owners CRUD
    public function ownersIndex()
    {
        $owners = Owner::orderBy('created_at', 'desc')->get();
        return view('dashboard.owners.index', compact('owners'));
    }

    public function storeOwner(Request $request)
    {
        $data = $request->except(['image']);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'owners');
        }

        Owner::create($data);
        return redirect()->back()->with('success', 'Owner added successfully');
    }

    public function updateOwner(Request $request, Owner $owner)
    {
        $data = $request->except(['image']);
        if ($request->hasFile('image')) {
            if ($owner->image) $this->deleteFromNextcloud($owner->image);
            $data['image'] = $this->uploadToNextcloud($request->file('image'), 'owners');
        }

        $owner->update($data);
        return redirect()->back()->with('success', 'Owner updated successfully');
    }

    public function deleteOwner(Owner $owner)
    {
        if ($owner->image) $this->deleteFromNextcloud($owner->image);
        $owner->delete();

        return redirect()->back()->with('success', 'Owner deleted successfully');
    }

    // Packages CRUD
    public function packagesIndex()
    {
        $packages = Package::orderBy('order')->get();
        return view('dashboard.packages.index', compact('packages'));
    }

    public function storePackage(Request $request)
    {
        $data = $request->except(['features_id_raw', 'features_en_raw']);
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
        
        // Handle features as newline separated strings
        if ($request->has('features_id_raw')) {
            $data['features_id'] = array_filter(array_map('trim', explode("\n", $request->features_id_raw)));
        }
        if ($request->has('features_en_raw')) {
            $data['features_en'] = array_filter(array_map('trim', explode("\n", $request->features_en_raw)));
        }


        Package::create($data);
        return redirect()->back()->with('success', 'Package added successfully');
    }

    public function updatePackage(Request $request, Package $package)
    {
        $data = $request->except(['features_id_raw', 'features_en_raw']);
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        // Handle features as newline separated strings
        if ($request->has('features_id_raw')) {
            $data['features_id'] = array_filter(array_map('trim', explode("\n", $request->features_id_raw)));
        }
        if ($request->has('features_en_raw')) {
            $data['features_en'] = array_filter(array_map('trim', explode("\n", $request->features_en_raw)));
        }


        $package->update($data);
        return redirect()->back()->with('success', 'Package updated successfully');
    }

    public function deletePackage(Package $package)
    {
        $package->delete();
        return redirect()->back()->with('success', 'Package deleted successfully');
    }

    // Orders Management
    public function ordersIndex()
    {
        $orders = \App\Models\PackageOrder::with(['user', 'package', 'invoice', 'tickets'])->orderBy('created_at', 'desc')->get();
        return view('dashboard.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, \App\Models\PackageOrder $order)
    {
        $order->update(['status' => $request->status]);

        // Provision Tenant if paid/completed and has subdomain
        if (in_array($request->status, ['paid', 'completed'])) {
            $order->provisionTenant();
        }

        // Sync Invoice status
        if ($order->invoice_id) {
            if (in_array($request->status, ['paid', 'completed'])) {
                $order->invoice()->update(['status' => 'Paid']);
            } elseif ($request->status === 'cancelled') {
                $order->invoice()->update(['status' => 'Cancelled']);
            }
        }

        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    // Tickets Management
    public function ticketsIndex()
    {
        $tickets = \App\Models\Ticket::with(['user', 'order'])->orderBy('created_at', 'desc')->get();
        return view('dashboard.tickets.index', compact('tickets'));
    }

    public function updateTicketStatus(Request $request, \App\Models\Ticket $ticket)
    {
        $ticket->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Ticket status updated');
    }

    // Users Management
    public function usersIndex()
    {
        $users = \App\Models\User::with(['orders', 'tickets'])->orderBy('created_at', 'desc')->get();
        return view('dashboard.users.index', compact('users'));
    }

    public function updateUser(Request $request, \App\Models\User $user)
    {
        $user->update($request->only(['name', 'email', 'role']));
        return redirect()->back()->with('success', 'User updated successfully');
    }

    public function deleteUser(\App\Models\User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself');
        }
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }

    // Tenant Management
    public function tenantsIndex()
    {
        $tenants = \App\Models\Tenant::with('domains')->orderBy('created_at', 'desc')->get();
        
        // Map owners to tenants
        foreach ($tenants as $tenant) {
            $order = \App\Models\PackageOrder::find($tenant->package_order_id);
            $tenant->owner = $order ? $order->user : null;
        }

        return view('dashboard.tenants.index', compact('tenants'));
    }

    public function deleteTenant(\App\Models\Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->back()->with('success', 'Tenant deleted successfully');
    }
}
