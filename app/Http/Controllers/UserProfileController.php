<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use File;
use Session;
use Intervention\Image\Facades\Image;

class UserProfileController extends Controller
{
    public function profile() {
       $user = Auth::user();
       return view('admin.profile.profile', [
           'user'   =>  $user
       ]);
    }

    public function updateImage() {
        return view('admin.profile.image');
    }

    protected function uploadUserImage($request) {
        $user = User::find(Auth::user()->id);

        $this->validate($request, [
            'image' =>  'required|image|mimes:jpeg,jpg,gif|max:1024'
        ]);

        // Handle the user upload of avatar
        if ($request->hasFile('image')) {
            $userImage = $request->file('image');
            $imageName = 'profile'.'_'.Auth::user()->id.'.'.$userImage->getClientOriginalExtension();

            // Delete current image before uploading new image
            if ($user->image !== 'bg_user.jpg') {
                // $file = public_path('uploads/avatars/' . $user->avatar);
                $directory = 'user-images/';
                $imageUrl = $directory.$imageName;
//                $file = 'uploads/avatars/' . $user->avatar;
                //$destinationPath = 'uploads/' . $id . '/';

                if (file_exists($user->image)) {
                    unlink($user->image);
                }
            }
            // Image::make($avatar)>resize(300, 300)>save(public_path('uploads/avatars/' . $filename));
            Image::make($userImage)->resize(300, 300)->save($imageUrl);
            $user = Auth::user();
            $user->image = $imageUrl;
            $user->save();
        }
    }

    public function imageUpload(Request $request) {
        $imageUrl = $this->uploadUserImage($request);
        return redirect('/profile');
    }

    public function changeProfileInfo() {
        return view('admin.profile.info');
    }

    public function updateProfileInfo(Request $request) {
        $user = User::find(Auth::user()->id);
        $user->name         =   $request->name;
        $user->designation  =   $request->designation;
        $user->email        =   $request->email;
        $user->phone        =   $request->phone;
        $user->save();
        return redirect('/profile');
    }

    public function uploadProfileImage() {
        return view('admin.login.image');
    }

    public function saveProfilePicture(Request $request) {
        $imageUrl = $this->uploadUserImage($request);
        Session::flush();
        return view('admin.login.registration-complete');
    }

    protected function completeRegistration() {
        Session::flush();
        return view('admin.login.registration-complete');
    }





}
