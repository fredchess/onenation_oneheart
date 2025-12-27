<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $partners = Partner::all();

        return view('admin.partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $fields = [];
        $fields[] = [
            "name" => "Nom",
            "field_name" => "name",
            "placeholder" => "Nom du partenaire",
            "type" => "text",
            "required" => true,
        ];

        $fields[] = [
            "name" => "Description",
            "field_name" => "description",
            "placeholder" => "Decription du partenaire",
            "type" => "text",
            "required" => true,
        ];

        $fields[] = [
            "name" => "Domaine",
            "field_name" => "domain",
            "placeholder" => "Domaine du partenaire",
            "type" => "text",
            "required" => true,
        ];

        $fields[] = [
            "name" => "URL",
            "field_name" => "url",
            "placeholder" => "Url vers le partenaire",
            "type" => "text",
        ];

        return view('admin.partners.create', compact('fields'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'domain' => 'required|string',
            'url' => 'required|string',
        ]);

        $partner = Partner::create($validated);

        if ($request->file('image')) $partner->addMediaFromRequest('image')->toMediaCollection('images');

        return redirect()->route('partners.index')->with('success', 'Le partenaire a ete enregistré avec succès');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Partner $partner
     * @return Application|Factory|View
     */
    public function edit(Partner $partner)
    {
        $fields = [];
        $fields[] = [
            "value" => $partner->name,
            "name" => "Nom",
            "field_name" => "name",
            "placeholder" => "Nom du partenaire",
            "type" => "text",
            "required" => true,
        ];

        $fields[] = [
            "value" => $partner->description,
            "name" => "Description",
            "field_name" => "description",
            "placeholder" => "Decription du partenaire",
            "type" => "text",
            "required" => true,
        ];

        $fields[] = [
            "value" => $partner->domain,
            "name" => "Domaine",
            "field_name" => "domain",
            "placeholder" => "Domaine du partenaire",
            "type" => "text",
            "required" => true,
        ];

        $fields[] = [
            "value" => $partner->url,
            "name" => "URL",
            "field_name" => "url",
            "placeholder" => "Url vers le partenaire",
            "type" => "text",
        ];

        return view("admin.partners.edit", compact("fields", "partner"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'domain' => 'required|string',
            'url' => 'required|string',
        ]);

        $partner->update($validated);

        if ($request->file('image')) {
            $partner->media()->delete();
            $partner->addMediaFromRequest('image')->toMediaCollection('images');
        }

        return redirect()->route("partners.index")->with("success", "Les modifications ont bien été enregistrées");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function multipleDestroy(Request $request)
    {
        if ($request->ids) {
            $ids = $request->ids;
            foreach ($ids as $id) {
                $partner = Partner::find($id);
                $partner->delete();
            }
            $message = sizeof($ids) . ' Partenaire(s) supprimé(s) avec succès';
        } else {
            $message = "Aucun partenaire n'a été supprimé";
        }
        return redirect()->route("partners.index")->with('success', $message);
    }
}
