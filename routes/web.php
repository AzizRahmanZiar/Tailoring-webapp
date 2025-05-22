<?php

use App\Http\Controllers\{
    AdminController,
    ClothsController,
    ContactController,
    CustomerOrderController,
    KortaiController,
    TailorPostController,
    SadraiController,
    SiteController,
    UniformController,
    UserMessageController,
    NotificationController,
    ClothController,
    PostRatingController,
};
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    $posts = \App\Models\TailorPost::with(['user'])
        ->latest()
        ->get()
        ->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'image' => $post->image ? asset('storage/' . $post->image) : null,
                'author' => $post->user->name,
                'created_at' => $post->created_at->format('Y-m-d')
            ];
        });

    $ratings = \App\Models\PostRating::with(['user', 'tailorPost'])
        ->latest()
        ->get()
        ->map(function ($rating) {
            return [
                'id' => $rating->id,
                'postId' => $rating->tailor_post_id,
                'user_name' => $rating->user->name,
                'user_image' => $rating->user->profile_image ? asset('storage/' . $rating->user->profile_image) : null,
                'rating' => $rating->rating,
                'comment' => $rating->comment,
                'created_at' => $rating->created_at->format('Y-m-d')
            ];
        });

    return Inertia::render('Site/Home', [
        'posts' => $posts,
        'ratings' => $ratings
    ]);
})->name('home');

Route::get('/tailor', [SiteController::class, 'tailors'])->name('tailors');
Route::get('/shop', [SiteController::class, 'shops'])->name('shop');

Route::get('/post', [SiteController::class, 'posts'])->name('posts');
Route::post('/post/{tailorPost}/rate', [PostRatingController::class, 'store'])->name('post.rate');
Route::get('/testimonials', [PostRatingController::class, 'getTestimonials'])->name('testimonials');

Route::get('/order', function () {
    return Inertia::render('Site/Order');
})->name('order');

Route::get('/contact', function () {
    return Inertia::render('Site/Contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/about', function () {
    return Inertia::render('Site/About');
})->name('about');


Route::resource('kortai', KortaiController::class);
Route::resource('uniforms', UniformController::class);

// Kortai routes
Route::get('/kortais', [KortaiController::class, 'index'])->name('kortais.index');
Route::post('/kortais', [KortaiController::class, 'store'])->name('kortais.store');
Route::put('/kortais/{kortai}', [KortaiController::class, 'update'])->name('kortais.update');
Route::delete('/kortais/{kortai}', [KortaiController::class, 'destroy'])->name('kortais.destroy');

// Sadrai routes
Route::resource('sadrai', SadraiController::class);

// Auth routes
require __DIR__.'/auth.php';

// Protected System routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Common system routes (accessible by both admin and tailor)
    Route::middleware([CheckRole::class . ':admin,tailor'])->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('System/Dashboard');
        })->name('dashboard');

        // Admin routes
        Route::get('/admin', [AdminController::class, 'admin'])->name('admin');
        Route::get('/messages', [UserMessageController::class, 'messages'])->name('messages');
        Route::put('/user/{user}', [AdminController::class, 'update'])->name('user.update');
        Route::delete('/user/{user}', [AdminController::class, 'destroy'])->name('user.delete');

        // Tailor routes
        Route::get('/kortai', [KortaiController::class, 'kortai'])->name('kortai');
        Route::get('/sadrai', [SadraiController::class, 'sadrai'])->name('sadrai');
        Route::get('/customerorder', [CustomerOrderController::class, 'customerorder'])->name('customerorder');

        // TailorPost routes
        Route::get('/tailor-posts', [TailorPostController::class, 'index'])->name('tailor-posts.index');
        Route::post('/tailor-posts', [TailorPostController::class, 'store'])->name('tailor-posts.store');
        Route::put('/tailor-posts/{tailorPost}', [TailorPostController::class, 'update'])->name('tailor-posts.update');
        Route::delete('/tailor-posts/{tailorPost}', [TailorPostController::class, 'destroy'])->name('tailor-posts.destroy');
    });

    Route::get('/tailorpost', [TailorPostController::class, 'index'])->name('tailorpost.index');
    Route::post('/tailorpost', [TailorPostController::class, 'store'])->name('tailorpost.store');
    Route::put('/tailorpost/{tailorPost}', [TailorPostController::class, 'update'])->name('tailorpost.update');
    Route::delete('/tailorpost/{tailorPost}', [TailorPostController::class, 'destroy'])->name('tailorpost.destroy');

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);

    // Cloths routes
    Route::get('/cloths', [ClothsController::class, 'cloths'])->name('cloths.index');
    Route::post('/cloths', [ClothsController::class, 'store'])->name('cloths.store');
    Route::put('/cloths/{cloth}', [ClothsController::class, 'update'])->name('cloths.update');
    Route::delete('/cloths/{cloth}', [ClothsController::class, 'destroy'])->name('cloths.destroy');

    // Uniform routes
    Route::get('/uniforms', [UniformController::class, 'uniforms'])->name('uniforms.index');
    Route::post('/uniforms', [UniformController::class, 'store'])->name('uniforms.store');
    Route::put('/uniforms/{uniform}', [UniformController::class, 'update'])->name('uniforms.update');
    Route::delete('/uniforms/{uniform}', [UniformController::class, 'destroy'])->name('uniforms.destroy');
});
