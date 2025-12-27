<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    public function index(Request $request){

        $user = auth()->user();
        if ($user->roles->pluck('name')->contains('responsable')) {
            if ($user->orphanage == null) return abort(403);
            return redirect(route('orphanages.edit', ['orphanage' => $user->orphanage]));
        }
        
        return view("admin.pages.home");
    }
}
