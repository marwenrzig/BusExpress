<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeatLayout;
use App\Models\FleetType;
use App\Models\Vehicle;

class ManageFleetController extends Controller
{
    public function seatLayouts(){
        $pageTitle = 'Seat Layouts';
        $emptyMessage = 'No layouts found';
        $layouts = SeatLayout::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.fleet.seat_layouts', compact('pageTitle', 'emptyMessage', 'layouts'));
    }

    public function seatLayoutStore(Request $request){
        $request->validate([
            'layout' => 'required|unique:mise_page_places'
        ]);

        $seatLayout = new SeatLayout();
        $seatLayout->layout = $request->layout;
        $seatLayout->save();
        $notify[] = ['success', 'Mise en page de place enrgistrée avec succès.'];
        return back()->withNotify($notify);
    }

    public function seatLayoutUpdate(Request $request, $id){
        $request->validate([
            'layout' => 'required|unique:mise_page_places,layout,'.$id
        ]);
        
        $seat = SeatLayout::find($request->id);
        $seat->layout = $request->layout;
        $seat->save();
        $notify[] = ['success', 'Mis a jour réussie avec succès.'];
        return back()->withNotify($notify);
    }

    public function seatLayoutDelete(Request $request){
        $request->validate(['id' => 'required|integer']);
        SeatLayout::find($request->id)->delete();
        $notify[] = ['success', 'Mise en page de place supprimée avec succès.'];
        return back()->withNotify($notify);
    }


    public function fleetLists(){
        $pageTitle = 'Fleet Type';
        $emptyMessage = 'No fleet type found';
        $seatLayouts = SeatLayout::all();
        $fleetType = FleetType::orderBy('id','desc')->paginate(getPaginate());
        return view('admin.fleet.type', compact('pageTitle', 'emptyMessage', 'fleetType', 'seatLayouts'));
    }

    public function fleetTypeStore(Request $request){
        $request->validate([
            'nom'        => 'required|unique:type_bus',
            'place_layout' => 'required',
            'places_pont'  => 'required|array|min:1',
            'places_pont.*'=> 'required|numeric|gt:0'
        ],[
            'places_pont.*.required'  => 'Seat number for all deck is required',
            'places_pont.*.numeric'   => 'Seat number for all deck is must be a number',
            'places_pont.*.gt:0'      => 'Seat number for all deck is must be greater than 0',
        ]);
        $fleetType = new FleetType();
        $fleetType->nom = $request->nom;
        $fleetType->place_layout = $request->place_layout;
        $fleetType->places_pont = $request->places_pont;
        $fleetType->est_climatise	 = $request->est_climatise ? $request->est_climatise : 0;
        $fleetType->status = 1;
        $fleetType->save();

        $notify[] = ['success','Type de bus enregistré avec succès'];
        return back()->withNotify($notify);
    }

    public function fleetTypeUpdate(Request $request, $id){
        $request->validate([
            'nom'        => 'required|unique:type_bus,nom,'.$id,
            'place_layout' => 'required',
            'places_pont'  => 'required|array|min:1',
            'places_pont.*'=> 'required|numeric|gt:0'
        ],[
            'places_pont.*.required'  => 'Seat number for all deck is required',
            'places_pont.*.numeric'   => 'Seat number for all deck is must be a number',
            'places_pont.*.gt:0'      => 'Seat number for all deck is must be greater than 0',
        ]);
        // return $request;
        $fleetType = FleetType::find($id);
        $fleetType->nom = $request->nom;
        $fleetType->place_layout = $request->place_layout;
        $fleetType->places_pont = $request->places_pont;
        $fleetType->est_climatise = $request->est_climatise ? 1 : 0;
        $fleetType->save();
        $notify[] = ['success','Type de bus mis a jour avec succès'];
        return back()->withNotify($notify);
    }

    public function fleetEnableDisabled(Request $request){
        $request->validate(['id' => 'required|integer']);
        $fleetType = FleetType::find($request->id);
        $fleetType->status = $fleetType->status == 1 ? 0 : 1;
        $fleetType->save();
        if($fleetType->status == 1){
            $notify[] = ['success', 'Le type de bus actif avec succès.'];
        }else{
            $notify[] = ['success', 'Le type de bus désactivé avec succès.'];
        }
        return back()->withNotify($notify);
    }

    public function vehicles(){
        $pageTitle = 'All Vehicles';
        $emptyMessage = 'No vehicles found';
        $fleetType = FleetType::where('status', 1)->orderBy('id','desc')->get();
        $vehicles = Vehicle::with('fleetType')->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.fleet.vehicles', compact('pageTitle', 'emptyMessage', 'vehicles', 'fleetType'));
    }

    public function vehicleSearch(Request $request){
        $search = $request->search;
        $pageTitle = 'Vehicles - '. $search;
        $emptyMessage = 'No vehicles found';
        $fleetType = FleetType::where('status', 1)->orderBy('id','desc')->get();
        $vehicles = Vehicle::with('fleetType')->where('register_no', $search)->orderBy('id','desc')->paginate(getPaginate());
        return view('admin.fleet.vehicles', compact('pageTitle', 'emptyMessage', 'vehicles', 'fleetType', 'search'));
    }

    public function vehiclesStore(Request $request){
        $this->validate($request,[
            'label'         => 'required|string',
            'fleet_type'        => 'required|numeric',
            'register_no'       => 'required|string|unique:bus',
        ]);

        $vehicle = new Vehicle();
        $vehicle->label = $request->label;
        $vehicle->fleet_type_id = $request->fleet_type;
        $vehicle->register_no = $request->register_no;
        $vehicle->save();

        $notify[] = ['success', 'Bus enregistrée avec succès.'];
        return back()->withNotify($notify);
    }

    public function vehiclesUpdate(Request $request,$id){
        $this->validate($request,[
            'label'         => 'required|string',
            'fleet_type'        => 'required|numeric',
            'register_no'       => 'required|string|unique:bus,register_no,'.$id,
        ]);

        $vehicle = Vehicle::find($id);
        $vehicle->label = $request->label;
        $vehicle->fleet_type_id = $request->fleet_type;
        $vehicle->register_no = $request->register_no;
        $vehicle->save();

        $notify[] = ['success', 'Bus mis a jour avec succès.'];
        return back()->withNotify($notify);
    }

    public function vehiclesActiveDisabled(Request $request){
        $request->validate(['id' => 'required|integer']);

        $vehicle = Vehicle::find($request->id);
        $vehicle->status = $vehicle->status == 1 ? 0 : 1;
        $vehicle->save();
        if($vehicle->status == 1){
            $notify[] = ['success', 'Bus actif avec succès.'];
        }else{
            $notify[] = ['success', 'Bus désactivé avec succès.'];
        }
        return back()->withNotify($notify);
    }
}
