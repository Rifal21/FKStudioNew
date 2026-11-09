<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DuitkuController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WhatsAppSettingsController;
use App\Http\Controllers\Shop;
use App\Http\Controllers\Dashboard\Shop as AdminShop;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

if (app()->environment('local')) {
    Route::view('/styleguide', 'styleguide')->name('styleguide');
}

Route::get('/products', [LandingController::class, 'packagesIndex'])->name('package.index');
Route::get('/portfolio', [LandingController::class, 'projectsIndex'])->name('portfolio.index');
Route::get('/blog', [LandingController::class, 'blogsIndex'])->name('blog.index');
Route::get('/blog/{slug}', [LandingController::class, 'showBlog'])->name('blog.show');
Route::get('/packages/{slug}', [LandingController::class, 'showPackage'])->name('package.show');
Route::get('switch-language/{locale}', [LandingController::class, 'switchLanguage'])->name('switch-language');
Route::get('/sitemap.xml', [App\Http\Controllers\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [App\Http\Controllers\SeoController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [App\Http\Controllers\SeoController::class, 'llms'])->name('llms');
Route::post('/chat', [ChatController::class, 'sendMessage'])->name('chat.send');
Route::get('/invoice/v/{invoice}', [InvoiceController::class, 'publicShow'])->name('invoices.public.show');

// Shop: catalog is public, cart and checkout need an account.
Route::middleware('shop')->group(function () {
    Route::get('/shop', [Shop\ShopController::class, 'index'])->name('shop.index');
    // Guests are sent to log in here and the item is added once they are back.
    Route::post('/cart/items', [Shop\CartController::class, 'store'])->middleware('throttle:60,1')->name('cart.store');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/cart', [Shop\CartController::class, 'index'])->name('cart.index');
        Route::patch('/cart/items/{cartItem}', [Shop\CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/items/{cartItem}', [Shop\CartController::class, 'destroy'])->name('cart.destroy');

        Route::get('/shop/checkout', [Shop\CheckoutController::class, 'show'])->name('shop.checkout');
        Route::post('/shop/checkout', [Shop\CheckoutController::class, 'process'])->middleware('throttle:10,1')->name('shop.checkout.process');
        Route::post('/shop/checkout/voucher', [Shop\CheckoutController::class, 'voucher'])->middleware('throttle:20,1')->name('shop.checkout.voucher');
        Route::post('/shop/checkout/shipping-rates', [Shop\CheckoutController::class, 'rates'])->middleware('throttle:30,1')->name('shop.checkout.rates');
    });

    // Defined after the static /shop/* routes so the slug does not swallow them.
    Route::get('/shop/{slug}', [Shop\ShopController::class, 'show'])->name('shop.show');
});

// Orders and downloads stay reachable even if the shop is switched off later.
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/shop/orders/{shopOrder}', [Shop\OrderController::class, 'show'])->name('shop.orders.show');
    Route::get('/shop/orders/{shopOrder}/status', [Shop\OrderController::class, 'status'])->middleware('throttle:60,1')->name('shop.orders.status');
    Route::post('/shop/orders/{shopOrder}/pay', [Shop\OrderController::class, 'pay'])->middleware('throttle:10,1')->name('shop.orders.pay');
    Route::post('/shop/orders/{shopOrder}/payment-proof', [Shop\OrderController::class, 'proof'])->middleware('throttle:10,1')->name('shop.orders.proof');
    Route::post('/shop/orders/{shopOrder}/cancel', [Shop\OrderController::class, 'cancel'])->name('shop.orders.cancel');
    Route::get('/shop/orders/{shopOrder}/invoice', [Shop\OrderController::class, 'invoice'])->name('shop.orders.invoice');

    Route::get('/my-purchases', [Shop\OrderController::class, 'index'])->name('user.purchases');
    Route::get('/my-downloads', [Shop\DownloadController::class, 'index'])->name('user.downloads');
    Route::get('/my-downloads/{entitlement}/files/{file}', [Shop\DownloadController::class, 'download'])->middleware('throttle:30,1')->name('user.downloads.get');
    Route::get('/my-downloads/{entitlement}/files/{file}/file', [Shop\DownloadController::class, 'file'])->middleware(['signed', 'throttle:30,1'])->name('user.downloads.file');

    Route::get('/my-addresses', [Shop\AddressController::class, 'index'])->name('user.addresses.index');
    Route::post('/my-addresses', [Shop\AddressController::class, 'store'])->name('user.addresses.store');
    Route::patch('/my-addresses/{address}', [Shop\AddressController::class, 'update'])->name('user.addresses.update');
    Route::delete('/my-addresses/{address}', [Shop\AddressController::class, 'destroy'])->name('user.addresses.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // API & Static Routes (defined first to prevent wildcard conflicts)
    Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.apply_voucher');
    Route::get('/api/domains/check', [CheckoutController::class, 'checkDomainAvailability'])->name('api.domains.check');

    Route::get('/checkout/{package}', [CheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout/{package}', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/pelunasan/{order}', [CheckoutController::class, 'showPelunasan'])->name('checkout.pelunasan');
    Route::post('/checkout/pelunasan/{order}', [CheckoutController::class, 'processPelunasan'])->name('checkout.pelunasan.process');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/confirm/{order}', [CheckoutController::class, 'confirmPayment'])->name('checkout.confirm');

    // User Orders & Bookings
    Route::get('/my-orders', [CheckoutController::class, 'userOrders'])->name('user.orders');
    Route::get('/my-websites', [CheckoutController::class, 'userWebsites'])->name('user.websites');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [CmsController::class, 'dashboard'])->name('dashboard');
    
    Route::middleware(['role:super_admin'])->name('dashboard.')->group(function() {
        // Site Settings
    Route::get('/settings', [CmsController::class, 'editSettings'])->name('settings.edit');
    Route::post('/settings', [CmsController::class, 'updateSettings'])->name('settings.update');

    // Hero
    Route::get('/hero', [CmsController::class, 'editHero'])->name('hero.edit');
    Route::post('/hero', [CmsController::class, 'updateHero'])->name('hero.update');
    Route::post('/hero/slides', [CmsController::class, 'storeHeroSlide'])->name('hero.slides.store');
    Route::delete('/hero/slides/{slide}', [CmsController::class, 'deleteHeroSlide'])->name('hero.slides.destroy');

    // About
    Route::get('/about', [CmsController::class, 'editAbout'])->name('about.edit');
    Route::post('/about', [CmsController::class, 'updateAbout'])->name('about.update');
    Route::post('/about/slides', [CmsController::class, 'storeAboutSlide'])->name('about.slides.store');
    Route::delete('/about/slides/{slide}', [CmsController::class, 'deleteAboutSlide'])->name('about.slides.destroy');

    // Services
    Route::get('/services', [CmsController::class, 'servicesIndex'])->name('services.index');
    Route::post('/services', [CmsController::class, 'storeService'])->name('services.store');
    Route::patch('/services/{service}', [CmsController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [CmsController::class, 'deleteService'])->name('services.destroy');

    // Projects
    Route::get('/projects', [CmsController::class, 'projectsIndex'])->name('projects.index');
    Route::post('/projects', [CmsController::class, 'storeProject'])->name('projects.store');
    Route::patch('/projects/{project}', [CmsController::class, 'updateProject'])->name('projects.update');
    Route::delete('/projects/{project}', [CmsController::class, 'deleteProject'])->name('projects.destroy');

    // Blogs
    Route::get('/blogs', [CmsController::class, 'blogsIndex'])->name('blogs.index');
    Route::get('/blogs/{blog}', [CmsController::class, 'showBlog'])->name('blogs.show');
    Route::post('/blogs', [CmsController::class, 'storeBlog'])->name('blogs.store');
    Route::patch('/blogs/{blog}', [CmsController::class, 'updateBlog'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [CmsController::class, 'deleteBlog'])->name('blogs.destroy');
    Route::post('/blogs/upload-image', [CmsController::class, 'uploadEditorImage'])->name('blogs.upload_image');

    // Testimonials
    Route::get('/testimonials', [CmsController::class, 'testimonialsIndex'])->name('testimonials.index');
    Route::post('/testimonials', [CmsController::class, 'storeTestimonial'])->name('testimonials.store');
    Route::patch('/testimonials/{testimonial}', [CmsController::class, 'updateTestimonial'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [CmsController::class, 'deleteTestimonial'])->name('testimonials.destroy');

    // Clients
    Route::get('/clients', [CmsController::class, 'clientsIndex'])->name('clients.index');
    Route::post('/clients', [CmsController::class, 'storeClient'])->name('clients.store');
    Route::patch('/clients/{client}', [CmsController::class, 'updateClient'])->name('clients.update');
    Route::delete('/clients/{client}', [CmsController::class, 'deleteClient'])->name('clients.destroy');

    // Owners
    Route::get('/owners', [CmsController::class, 'ownersIndex'])->name('owners.index');
    Route::post('/owners', [CmsController::class, 'storeOwner'])->name('owners.store');
    Route::patch('/owners/{owner}', [CmsController::class, 'updateOwner'])->name('owners.update');
    Route::delete('/owners/{owner}', [CmsController::class, 'deleteOwner'])->name('owners.destroy');

    // Packages
    Route::get('/packages', [CmsController::class, 'packagesIndex'])->name('packages.index');
    Route::post('/packages', [CmsController::class, 'storePackage'])->name('packages.store');
    Route::patch('/packages/{package}', [CmsController::class, 'updatePackage'])->name('packages.update');
    Route::delete('/packages/{package}', [CmsController::class, 'deletePackage'])->name('packages.destroy');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    Route::post('/invoices/{invoice}/whatsapp', [InvoiceController::class, 'sendWhatsApp'])->name('invoices.whatsapp');

    // Subscriptions (recurring invoices)
    Route::resource('subscriptions', SubscriptionController::class);
    Route::post('/subscriptions/{subscription}/pause', [SubscriptionController::class, 'pause'])->name('subscriptions.pause');
    Route::post('/subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::post('/subscriptions/{subscription}/generate', [SubscriptionController::class, 'generateNow'])->name('subscriptions.generate');

    // WhatsApp (WAHA) connection
    Route::get('/whatsapp', [WhatsAppSettingsController::class, 'edit'])->name('whatsapp.edit');
    Route::post('/whatsapp', [WhatsAppSettingsController::class, 'update'])->name('whatsapp.update');
    Route::get('/whatsapp/sessions', [WhatsAppSettingsController::class, 'sessions'])->name('whatsapp.sessions');
    Route::post('/whatsapp/sessions', [WhatsAppSettingsController::class, 'createSession'])->name('whatsapp.sessions.create');
    Route::prefix('/whatsapp/sessions/{session}')->where(['session' => '[A-Za-z0-9_-]{1,60}'])->name('whatsapp.sessions.')->group(function () {
        Route::get('/qr', [WhatsAppSettingsController::class, 'qr'])->name('qr');
        Route::post('/start', [WhatsAppSettingsController::class, 'start'])->name('start');
        Route::post('/restart', [WhatsAppSettingsController::class, 'restart'])->name('restart');
        Route::post('/stop', [WhatsAppSettingsController::class, 'stop'])->name('stop');
        Route::post('/logout', [WhatsAppSettingsController::class, 'logout'])->name('logout');
        Route::post('/activate', [WhatsAppSettingsController::class, 'activate'])->name('activate');
        Route::delete('/', [WhatsAppSettingsController::class, 'destroySession'])->name('destroy');
    });
    Route::post('/whatsapp/test', [WhatsAppSettingsController::class, 'test'])->name('whatsapp.test');

    // Vouchers
    Route::get('/vouchers', [CmsController::class, 'vouchersIndex'])->name('vouchers.index');
    Route::post('/vouchers', [CmsController::class, 'storeVoucher'])->name('vouchers.store');
    Route::patch('/vouchers/{voucher}', [CmsController::class, 'updateVoucher'])->name('vouchers.update');
    Route::delete('/vouchers/{voucher}', [CmsController::class, 'deleteVoucher'])->name('vouchers.destroy');

    // Manual Payment Methods
    Route::get('/payment-methods', [CmsController::class, 'editPaymentMethods'])->name('payment_methods.edit');
    Route::post('/payment-methods', [CmsController::class, 'updatePaymentMethods'])->name('payment_methods.update');

    // Orders
    Route::get('/orders', [CmsController::class, 'ordersIndex'])->name('orders.index');
    Route::patch('/orders/{order}', [CmsController::class, 'updateOrderStatus'])->name('orders.update');
    Route::patch('/orders/{order}/work-status', [CmsController::class, 'updateOrderWorkStatus'])->name('orders.work_status');
    Route::post('/orders/{order}/register-domain', [CmsController::class, 'registerOrderDomain'])->name('orders.register_domain');
    Route::post('/orders/{order}/tagih-pelunasan', [CmsController::class, 'tagihPelunasan'])->name('orders.tagih_pelunasan');

    // Tickets
    Route::get('/tickets', [CmsController::class, 'ticketsIndex'])->name('tickets.index');
    Route::patch('/tickets/{ticket}', [CmsController::class, 'updateTicketStatus'])->name('tickets.update');

    // Shop
    Route::prefix('shop')->name('shop.')->group(function () {
        Route::get('/products', [AdminShop\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminShop\ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminShop\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminShop\ProductController::class, 'edit'])->name('products.edit');
        Route::patch('/products/{product}', [AdminShop\ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminShop\ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{product}/duplicate', [AdminShop\ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::post('/products/{product}/files', [AdminShop\ProductController::class, 'storeFile'])->name('products.files.store');
        Route::delete('/products/{product}/files/{file}', [AdminShop\ProductController::class, 'destroyFile'])->name('products.files.destroy');
        Route::post('/products/{product}/keys', [AdminShop\ProductController::class, 'storeKeys'])->name('products.keys.store');
        Route::delete('/products/{product}/keys/{key}', [AdminShop\ProductController::class, 'destroyKey'])->name('products.keys.destroy');

        Route::get('/categories', [AdminShop\CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminShop\CategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [AdminShop\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminShop\CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/orders', [AdminShop\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminShop\OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/payments/{payment}/proof', [AdminShop\OrderController::class, 'proof'])->name('orders.proof');
        Route::post('/orders/{order}/payments/{payment}/approve', [AdminShop\OrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/payments/{payment}/reject', [AdminShop\OrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/cancel', [AdminShop\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/refund', [AdminShop\OrderController::class, 'refund'])->name('orders.refund');
        Route::post('/orders/{order}/ship', [AdminShop\OrderController::class, 'ship'])->name('orders.ship');
        Route::post('/orders/{order}/deliver', [AdminShop\OrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('/orders/{order}/resend-access', [AdminShop\OrderController::class, 'resendAccess'])->name('orders.resend');
        Route::post('/orders/{order}/resolve', [AdminShop\OrderController::class, 'resolve'])->name('orders.resolve');
        Route::patch('/orders/{order}/notes', [AdminShop\OrderController::class, 'notes'])->name('orders.notes');
        Route::post('/orders/{order}/entitlements/{entitlement}/reset', [AdminShop\OrderController::class, 'resetDownloads'])->name('orders.entitlements.reset');
        Route::post('/orders/{order}/entitlements/{entitlement}/toggle', [AdminShop\OrderController::class, 'toggleAccess'])->name('orders.entitlements.toggle');

        Route::get('/reports', [AdminShop\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/tax.csv', [AdminShop\ReportController::class, 'taxCsv'])->name('reports.tax');

        Route::get('/settings', [AdminShop\SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [AdminShop\SettingsController::class, 'update'])->name('settings.update');
    });

    // Users
    Route::get('/users', [CmsController::class, 'usersIndex'])->name('users.index');
    Route::patch('/users/{user}', [CmsController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [CmsController::class, 'deleteUser'])->name('users.destroy');

    });
});

require __DIR__.'/auth.php';
