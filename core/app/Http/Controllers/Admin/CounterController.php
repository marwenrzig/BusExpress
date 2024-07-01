<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Counter;

class CounterController extends Controller
{
    public function counters(){
        $pageTitle = 'All Counter';
        $emptyMessage = 'No counter found';
        $counters = Counter::paginate(getPaginate());
        return view('admin.counter.list', compact('pageTitle','emptyMessage','counters'));
    }

    public function counterStore(Request $request){
        $request->validate([
            'nom' => 'required|unique:guichet',
            'ville' => 'required',
            'phone' => 'required|numeric|unique:guichet'
        ]);

        $counter = new Counter();
        $counter->nom      =  $request->nom;
        $counter->ville      =  $request->ville;
        $counter->emplacement  =  $request->emplacement;
        $counter->phone    =  $request->phone;
        $counter->save();

        $notify[] = ['success', 'Guichet sauvegardé avec succès.'];
        return back()->withNotify($notify);
    }

    public function counterUpdate(Request $request, $id){
        $request->validate([
            'nom' => 'required|unique:guichet,nom,'.$id,
            'ville' => 'required',
            'phone' => 'required|numeric|unique:guichet,phone,'.$id
        ]);

        $counter = Counter::find($id);
        $counter->nom      =  $request->nom;
        $counter->ville      =  $request->ville;
        $counter->emplacement  =  $request->emplacement;
        $counter->phone    =  $request->phone;
        $counter->save();

        $notify[] = ['success', 'Guichet modifié avec succès.'];
        return back()->withNotify($notify);
    }

    public function counterActiveDisabled(Request $request){
        $request->validate(['id' => 'required|integer']);

        $counter = Counter::find($request->id);
        $counter->status = $counter->status == 1 ? 0 : 1;
        $counter->save();
        
        if($counter->status == 1){
            $notify[] = ['success', 'Guichet actif avec succès.'];
        }else{
            $notify[] = ['success', 'Guichet désactivé avec succès.'];
        }

        return back()->withNotify($notify);
    }
}
