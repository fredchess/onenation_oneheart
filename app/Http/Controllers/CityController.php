<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $cities = City::all();
        return view("admin.cities.index", compact("cities"));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $fields = [];
        $fields[] = [
            "name" => "Nom",
            "field_name" => "name",
            "placeholder" => "Nom de la ville",
            "type" => "text",
            "required" => true,
        ];
        $fields[] = [
            "name" => "Description",
            "field_name" => "description",
            "placeholder" => "Description de la ville",
            "type" => "text",
        ];
        $fields[] = [
            "name" => "Nom du pays",
            "field_name" => "country_name",
            "placeholder" => "Nom du pays",
            "type" => "text",
        ];
        $fields[] = [
            "name" => "Code du pays",
            "field_name" => "country_code",
            "placeholder" => "Code à 2 lettres du pays",
            "type" => "text",
        ];

        return view("admin.cities.create", compact("fields"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $ville = new City;
        $ville->name = $request->name;
        $ville->description = $request->description;
        $ville->country_name = $request->country_name;
        $ville->country_code = $request->country_code;
        $ville->save();
        return redirect()->route("cities.index")->with("success", "La ville a bien été enregistrée");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        //

        $fields = [];
        $fields[] = [
            "value" => $city->name,
            "name" => "Nom",
            "field_name" => "name",
            "placeholder" => "Nom de la ville",
            "type" => "text",
            "required" => true,
        ];
        $fields[] = [
            "value" => $city->description,
            "name" => "Description",
            "field_name" => "description",
            "placeholder" => "Description de la ville",
            "type" => "text",
        ];
        $fields[] = [
            "value" => $city->country_name,
            "name" => "Nom du pays",
            "field_name" => "country_name",
            "placeholder" => "Nom du pays",
            "type" => "text",
        ];
        $fields[] = [
            "value" => $city->country_code,
            "name" => "Code du pays",
            "field_name" => "country_code",
            "placeholder" => "Code à 2 lettres du pays",
            "type" => "text",
        ];

        return view("admin.cities.edit", compact("fields", "city"));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $city->name = $request->name;
        $city->description = $request->description;
        $city->country_name = $request->country_name;
        $city->country_code = $request->country_code;
        $city->save();
        return redirect()->route("cities.index")->with("success", "La ville a bien été enregistrée");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Request
     * @return \Illuminate\Http\Response
     */
    public function multipleDestroy(Request $request)
    {
        if ($request->ids) {
            $ids = $request->ids;
            foreach ($ids as $id) {
                $city = City::find($id);
                //ActivityLogger::activity("Suppression de l'équipe ID:".$client->id.' par l\'utilisateur ID:'.Auth::id());
                $city->delete();
            }
            $message = sizeof($ids) . ' ville(s) supprimée(s) avec succès';
        } else {
            $message = "Aucun ville n'a été supprimée";
        }
        return redirect()->route("cities.index")->with('success', $message);
    }

}
