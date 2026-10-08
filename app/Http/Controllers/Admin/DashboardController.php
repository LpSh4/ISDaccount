<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\AchievementCreated;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $users = User::latest()->get();
        return Inertia::render('Admin/Dashboard', ['users' => $users]);
    }

    public function storeAchievement(Request $request, User $user)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:2048',
        ]);
        // Creating achievements, for now this page only does that :P
        $achievement = Achievement::create($validated);
        $user->achievements()->attach($achievement->id);
        // Now it also sends a reverb event
        event(new AchievementCreated($user->id, $achievement->toArray()));
        return back();
    }
}
