<?php

namespace App\Http\Controllers;

use App\Lib\BusLayout;
use App\Models\AdminNotification;
use App\Models\FleetType;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\Schedule;
use App\Models\Trip;
use App\Models\TicketPrice;
use App\Models\BookedTicket;
use App\Models\VehicleRoute;
use App\Models\Counter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class SiteController extends Controller
{
    public function __construct(){
        $this->activeTemplate = activeTemplate();
    }

    public function index(){
        $count = Page::where('tempname',$this->activeTemplate)->where('slug','home')->count();
        if($count == 0){
            $page = new Page();
            $page->tempname = $this->activeTemplate;
            $page->nom = 'HOME';
            $page->slug = 'home';
            $page->save();
        }

        $pageTitle = 'Home';
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();

        return view($this->activeTemplate . 'home', compact('pageTitle','sections'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname',$this->activeTemplate)->where('slug',$slug)->firstOrFail();
        $pageTitle = $page->nom;
        $sections = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle','sections'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','contact')->first();
        $content = Frontend::where('data_keys', 'contact.content')->first();

        return view($this->activeTemplate . 'contact',compact('pageTitle', 'sections', 'content'));
    }


    public function contactSubmit(Request $request)
    {
        $attachments = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

        $this->validate($request, [
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'sujet' => 'required|max:100',
            'message' => 'required',
        ]);


        $random = getNumber();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->titre = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view',$ticket->id);
        $adminNotification->save();

        $notify[] = ['success', 'Ticket créé avec succès !'];

        return redirect()->route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'fr';
        session()->put('lang', $lang);
        return redirect()->back();
    }

    

    public function ticket(){
        $pageTitle = 'Book Ticket';
        $emptyMessage = 'There is no trip available';
        $fleetType = FleetType::active()->get();

        $trips = Trip::with(['fleetType' ,'route', 'schedule', 'startFrom' , 'endTo'])->where('status', 1)->paginate(getPaginate(10));

        if(auth()->user()){
            $layout = 'layouts.master';
        }else{
            $layout = 'layouts.frontend';
        }

        $schedules = Schedule::all();
        $routes = VehicleRoute::active()->get();
        return view($this->activeTemplate.'ticket', compact('pageTitle' ,'fleetType', 'trips','routes' ,'schedules', 'emptyMessage', 'layout'));
    }

    public function showSeat($id){

        $trip = Trip::with( ['fleetType' ,'route', 'schedule', 'startFrom' , 'endTo', 'assignedVehicle.vehicle', 'bookedTickets'])->where('status', 1)->where('id', $id)->firstOrFail();
        $pageTitle = $trip->title;
        $route     = $trip->route;
        $stoppageArr = $trip->route->stoppages;
        $stoppages = Counter::routeStoppages($stoppageArr);
        $ticketPrice       = TicketPrice::where('vehicle_route_id', $trip->vehicle_route_id)->where('fleet_type_id', $trip->fleet_type_id)->with('route')->first();
        $busLayout = new BusLayout($trip);

        if(auth()->user()){
            $layout = 'layouts.master';
        }else{
            $layout = 'layouts.frontend';
        }
        return view($this->activeTemplate.'book_ticket', compact('pageTitle','trip' , 'stoppages','busLayout', 'layout', 'ticketPrice'));
    }

    /*public function getTicketPrice(Request $request){
        $ticketPrice       = TicketPrice::where('vehicle_route_id', $request->vehicle_route_id)->where('fleet_type_id', $request->fleet_type_id)->with('route')->first();
        $route              = $ticketPrice->route;
        $stoppages          = $ticketPrice->route->stoppages;
        $trip               = Trip::find($request->trip_id);
        $sourcePos         = array_search($request->source_id, $stoppages);
        $destinationPos    = array_search($request->destination_id, $stoppages);

        $bookedTicket  = BookedTicket::where('trip_id', $request->trip_id)->where('date_of_journey', Carbon::parse($request->date)->format('Y-m-d'))->whereIn('status', [1,2])->get()->toArray();

        $startPoint = array_search($trip->start_from , array_values($trip->route->stoppages));
        $endPoint = array_search($trip->end_to , array_values($trip->route->stoppages));
        if($startPoint < $endPoint){
            $reverse = false;
        }else{
            $reverse = true;
        }

        if(!$reverse){
            $can_go = ($sourcePos < $destinationPos)?true:false;
        }else{
            $can_go = ($sourcePos > $destinationPos)?true:false;
        }

        if(!$can_go){
            $data = [
                'error' => "Sélectionnez correctement le point de départ et le point d'arrivée"
            ];
            return response()->json($data);
        }
        $sdArray  = [$request->source_id, $request->destination_id];
        $getPrice = $ticketPrice->prices()->where('source_destination', json_encode($sdArray))->orWhere('source_destination', json_encode(array_reverse($sdArray)))->first();

        if($getPrice){
            $price = $getPrice->price;
        }else{
            $price = [
                'error' => 'L’administrateur ne peut pas fixer de prix pour cette route. Donc, vous ne pouvez pas acheter de ticket pour ce voyage.'
            ];
        }
        $data['bookedSeats']        = $bookedTicket;
        $data['reqSource']         = $request->source_id;
        $data['reqDestination']    = $request->destination_id;
        $data['reverse']            = $reverse;
        $data['stoppages']          = $stoppages;
        $data['price']              = $price;
        return response()->json($data);
    }*/
    public function getTicketPrice(Request $request){
        $ticketPrice       = TicketPrice::where('vehicle_route_id', $request->vehicle_route_id)->where('fleet_type_id', $request->fleet_type_id)->with('route')->first();
        $route              = $ticketPrice->route;
        $stoppages          = $ticketPrice->route->stoppages;
        $trip               = Trip::find($request->trip_id);
        $sourcePos         = array_search($request->source_id, $stoppages);
        $destinationPos    = array_search($request->destination_id, $stoppages);

        $bookedTicket  = BookedTicket::where('trip_id', $request->trip_id)->where('date_of_journey', Carbon::parse($request->date)->format('Y-m-d'))->whereIn('status', [1,2])->get()->toArray();

        $startPoint = array_search($trip->start_from , array_values($trip->route->stoppages));
        $endPoint = array_search($trip->end_to , array_values($trip->route->stoppages));
        if($startPoint < $endPoint){
            $reverse = false;
        }else{
            $reverse = true;
        }

        if(!$reverse){
            $can_go = ($sourcePos < $destinationPos)?true:false;
        }else{
            $can_go = ($sourcePos > $destinationPos)?true:false;
        }

        if(!$can_go){
            $data = [
                'error' => 'Select Pickup Point & Dropping Point Properly'
            ];
            return response()->json($data);
        }
        $sdArray  = [$request->source_id, $request->destination_id];
        $getPrice = $ticketPrice->prices()->where('source_destination', json_encode($sdArray))->orWhere('source_destination', json_encode(array_reverse($sdArray)))->first();

        if($getPrice){
            $price = $getPrice->price;
        }else{
            $price = [
                'error' => 'Admin may not set prices for this route. So, you can\'t buy ticket for this trip.'
            ];
        }
        $data['bookedSeats']        = $bookedTicket;
        $data['reqSource']         = $request->source_id;
        $data['reqDestination']    = $request->destination_id;
        $data['reverse']            = $reverse;
        $data['stoppages']          = $stoppages;
        $data['price']              = $price;
        return response()->json($data);
    }
    public function bookTicket(Request $request,$id){

        $request->validate([
            "pickup_point"   => "required|integer|gt:0",
            "dropping_point"  => "required|integer|gt:0",
            "date_of_journey" => "required|date",
            "seats"           => "required|string"
        ],[
            "seats.required"  => "Sélectionnez au moins une place.	"
        ]);

        if(!auth()->user()){
            $notify[] = ['error', 'Sans connexion, vous ne pouvez pas réserver de tickets'];
            return redirect()->route('user.login')->withNotify($notify);
        }

        $date_of_journey  = Carbon::parse($request->date_of_journey);
        $today            = Carbon::today()->format('Y-m-d');
        if($date_of_journey->format('Y-m-d') < $today ){
            $notify[] = ['error', "La date du voyage ne peut pas être inférieure à celle d'aujourd'hui."];
            return redirect()->back()->withNotify($notify);
        }

        $dayOff =  $date_of_journey->format('w');
        $trip   = Trip::findOrFail($id);
        $route              = $trip->route;
        $stoppages          = $trip->route->stoppages;
        $source_pos         = array_search($request->pickup_point, $stoppages);
        $destination_pos    = array_search($request->dropping_point, $stoppages);

        if(!empty($trip->day_off)) {
            if(in_array($dayOff, $trip->day_off)) {
                $notify[] = ['error', 'Le voyage n’est pas disponible pour '.$date_of_journey->format('l')];
                return redirect()->back()->withNotify($notify);
            }
        }

        $booked_ticket  = BookedTicket::where('trip_id', $id)->where('date_of_journey', Carbon::parse($request->date)->format('Y-m-d'))->whereIn('status',[1,2])->where('pickup_point', $request->pickup_point)->where('dropping_point', $request->dropping_point)->whereJsonContains('seats', rtrim($request->seats, ","))->get();
        if($booked_ticket->count() > 0){
            $notify[] = ['error', 'Pourquoi choisissez-vous les places qui sont déjà réservés ?'];
            return redirect()->back()->withNotify($notify);
        }

        $startPoint = array_search($trip->start_from , array_values($trip->route->stoppages));
        $endPoint = array_search($trip->end_to , array_values($trip->route->stoppages));
        if($startPoint < $endPoint){
            $reverse = false;
        }else{
            $reverse = true;
        }

        if(!$reverse){
            $can_go = ($source_pos < $destination_pos)?true:false;
        }else{
            $can_go = ($source_pos > $destination_pos)?true:false;
        }

        if(!$can_go){
            $notify[] = ['error', "Sélectionnez correctement le point de départ et le point d'arrivé"];
            return redirect()->back()->withNotify($notify);
        }

        $route = $trip->route;
        $ticketPrice = TicketPrice::where('fleet_type_id', $trip->fleetType->id)->where('vehicle_route_id', $route->id)->first();
        $sdArray     = [$request->pickup_point, $request->dropping_point];

        $getPrice    = $ticketPrice->prices()
                    ->where('source_destination', json_encode($sdArray))
                    ->orWhere('source_destination', json_encode(array_reverse($sdArray)))
                    ->first();
        if (!$getPrice) {
            $notify[] = ['error','Sélection non valide'];
            return back()->withNotify($notify);
        }
        $seats = array_filter((explode(',', $request->seats)));
        $unitPrice = getAmount($getPrice->price);
        $pnr_number = getTrx(10);
        $bookedTicket = new BookedTicket();
        $bookedTicket->user_id = auth()->user()->id;
        $bookedTicket->trip_id = $trip->id;
        $bookedTicket->source_destination = [$request->pickup_point, $request->dropping_point];
        $bookedTicket->pickup_point = $request->pickup_point;
        $bookedTicket->dropping_point = $request->dropping_point;
        $bookedTicket->seats = $seats;
        $bookedTicket->ticket_count = sizeof($seats);
        $bookedTicket->unit_price = $unitPrice;
        $bookedTicket->sub_total = sizeof($seats) * $unitPrice;
        $bookedTicket->date_of_journey = Carbon::parse($request->date_of_journey)->format('Y-m-d');
        $bookedTicket->pnr_number = $pnr_number;
        $bookedTicket->status = 0;
        $bookedTicket->save();
        session()->put('pnr_number',$pnr_number);
        return redirect()->route('user.deposit');
    }

    public function ticketSearch(Request $request)
    {
        if($request->pickup && $request->destination && $request->pickup == $request->destination){
            $notify[] = ['error', 'Veuillez sélectionner correctement le point de prise en charge et le point de destination'];
            return redirect()->back()->withNotify($notify);
        }
        if($request->date_of_journey && $request->date_of_journey < Carbon::now()->format('Y-m-d')){
            $notify[] = ['error', "La date du voyage ne peut pas être inférieure à celle d'aujourd'hui."];
            return redirect()->back()->withNotify($notify);
        }

        $trips = Trip::active();

        if($request->pickup && $request->destination){
            Session::flash('pickup', $request->pickup);
            Session::flash('destination', $request->destination);

            $pickup = $request->pickup;
            $destination = $request->destination;
            $trips = $trips->with('route')->get();
            $tripArray = array();

            foreach ($trips as $trip) {
                $startPoint = array_search($trip->start_from , array_values($trip->route->stoppages));
                $endPoint = array_search($trip->end_to , array_values($trip->route->stoppages));
                $pickup_point = array_search($pickup , array_values($trip->route->stoppages));
                $destination_point = array_search($destination , array_values($trip->route->stoppages));
                if($startPoint < $endPoint){
                    if($pickup_point === $startPoint && $pickup_point < $endPoint && $destination_point > $startPoint && $destination_point === $endPoint){
                        array_push($tripArray, $trip->id);
                    }
                }else{
                    $revArray = array_reverse($trip->route->stoppages);
                    $startPoint = array_search($trip->start_from ,array_values($revArray));
                    $endPoint = array_search($trip->end_to ,array_values($revArray));
                    $pickup_point = array_search($pickup ,array_values($revArray));
                    $destination_point = array_search($destination ,array_values($revArray));
                    if($pickup_point >= $startPoint && $pickup_point < $endPoint && $destination_point > $startPoint && $destination_point <= $endPoint){
                        array_push($tripArray, $trip->id);
                    }
                }
            }

            $trips = Trip::active()->whereIn('id',$tripArray);
        }else{
            if($request->pickup){
                Session::flash('pickup', $request->pickup);
                $pickup = $request->pickup;
                $trips = $trips->whereHas('route' , function($route) use ($pickup){
                    $route->whereJsonContains('stoppages' , $pickup);
                });
            }

            if($request->destination){
                Session::flash('destination', $request->destination);
                $destination = $request->destination;
                $trips = $trips->whereHas('route' , function($route) use ($destination){
                    $route->whereJsonContains('stoppages' , $destination);
                });
            }
        }

        if($request->fleetType){
            $trips = $trips->whereIn('fleet_type_id',$request->fleetType);
        }

        if($request->routes){
            $trips = $trips->whereIn('vehicle_route_id',$request->routes);
        }

        if($request->schedules){
            $trips = $trips->whereIn('schedule_id',$request->schedules);
        }

        if($request->date_of_journey){
            Session::flash('date_of_journey', $request->date_of_journey);
            $dayOff = Carbon::parse($request->date_of_journey)->format('w');
            $trips = $trips->whereJsonDoesntContain('day_off', $dayOff);
        }

        $trips = $trips->with( ['fleetType' ,'route', 'schedule', 'startFrom' , 'endTo'] )->where('status', 1)->paginate(getPaginate());

        $pageTitle = 'Search Result';
        $emptyMessage = 'There is no trip available';
        $fleetType = FleetType::active()->get();
        $schedules = Schedule::all();
        $routes = VehicleRoute::active()->get();

        if(auth()->user()){
            $layout = 'layouts.master';
        }else{
            $layout = 'layouts.frontend';
        }
        return view($this->activeTemplate.'ticket', compact('pageTitle' ,'fleetType', 'trips','routes', 'schedules', 'emptyMessage', 'layout'));
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

}
