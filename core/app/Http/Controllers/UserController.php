<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\GeneralSetting;
use App\Models\BookedTicket;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function home()
    {
        $pageTitle = 'Dashboard';
        $emptyMessage = 'Aucun ticket réservé trouvés';
        $user = auth()->user();
        $widget['booked'] = $user->tickets()->booked()->count();
        $widget['pending'] = $user->tickets()->pending()->count();
        $widget['rejected'] = $user->tickets()->rejected()->count();

        $widget['pending'] = BookedTicket::pending()->where('user_id', auth()->user()->id)->count();
        $widget['rejected'] = BookedTicket::rejected()->where('user_id', auth()->user()->id)->count();
        $bookedTickets = BookedTicket::with(['trip.fleetType','trip.startFrom', 'trip.endTo', 'trip.schedule' ,'pickup', 'drop'])->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'bookedTickets', 'widget', 'emptyMessage'));
    }

    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = Auth::user();
        return view($this->activeTemplate. 'user.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'prenom' => 'required|string|max:50',
            'nom' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'prenom.required'=>'First name field is required',
            'nom.required'=>'Last name field is required'
        ]);

        $user = Auth::user();

        $in['prenom'] = $request->prenom;
        $in['nom'] = $request->nom;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }
        $user->fill($in)->save();
        $notify[] = ['success', 'Profil mis à jour avec succès'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$password_validation]
        ]);


        try {
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();
                $notify[] = ['success', 'Modification réussie du mot de passe'];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'Le mot de passe ne correspond pas !'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    public function ticketHistory(){
        $pageTitle = 'Booking History';
        $emptyMessage = 'Aucun ticket réservé trouvé';
        $bookedTickets = BookedTicket::with(['trip.fleetType','trip.startFrom', 'trip.endTo', 'trip.schedule' ,'pickup', 'drop'])->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate.'user.booking_history', compact('pageTitle', 'emptyMessage','bookedTickets'));
    }

    public function printTicket($id){
        $pageTitle = "Ticket Print";
        $ticket = BookedTicket::with(['trip.fleetType','trip.startFrom', 'trip.endTo', 'trip.schedule', 'trip.assignedVehicle.vehicle' ,'pickup', 'drop', 'user'])->where('user_id', auth()->user()->id)->findOrFail($id);
        return view($this->activeTemplate.'user.print_ticket', compact('ticket', 'pageTitle'));
    }

}
