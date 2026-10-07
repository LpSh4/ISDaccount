<?php
namespace Modules\Account\Http\Controllers;
use Modules\Account\Http\Requests\UpdateProfileRequest;
use Modules\Account\Services\ProfileService;
use Illuminate\Http\RedirectResponse;

class ProfileController
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profileService->updateProfile(
            $request->user(),
            $request->validated()
        );
        //Not sure what back() does but I think its no-hard-refresh method
        return back();
    }
}
