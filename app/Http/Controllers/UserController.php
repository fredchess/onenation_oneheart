<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::with('roles')->get()->filter(function (User $user) {
            return !$user->hasRole('responsable');
        });
        
        return view("admin.users.index", compact("users"));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $fields = [];
        $fields[] = [
            "name" => "Nom",
            "field_name" => "name",
            "placeholder" => "Nom de l'utilisateur",
            "type" => "text",
            "required" => true,
        ];
        $fields[] = [
            "name" => "Email",
            "field_name" => "email",
            "placeholder" => "Email de l'utilisateur",
            "type" => "email",
            "required" => true,
        ];
        $fields[] = [
            "name" => "Tel",
            "field_name" => "tel",
            "placeholder" => "N° de téléphone l'utilisateur",
            "type" => "tel",
            "required" => true,
        ];
        $fields[] = [
            "name" => "Titre",
            "field_name" => "title",
            "placeholder" => "Titre de l'utilisateur",
            "type" => "text",
        ];
        $fields[] = [
            "name" => "Ville",
            "field_name" => "city",
            "placeholder" => "Ville",
            "type" => "text",
        ];
        $fields[] = [
            "name" => "Pays",
            "field_name" => "country",
            "placeholder" => "Pays",
            "type" => "text",
        ];

        $fields[] = [
            "name" => "Profil Twitter",
            "field_name" => "twitter",
            "placeholder" => "Lien du compte twitter",
            "type" => "text",
        ];

        $fields[] = [
            "name" => "Profil Facebook",
            "field_name" => "facebook",
            "placeholder" => "Lien du compte facebook",
            "type" => "text",
        ];
        $fields[] = [
            "name" => "Profil LinkedIn",
            "field_name" => "linkedin",
            "placeholder" => "Lien du compte LinkedIn",
            "type" => "text",
        ];

        return view("admin.users.create", compact("fields"));
        //
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
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make(Str::random(8));
        $datas = [];
        $datas["visible"] = $request->visible ? 1 : 0;
        $datas["title"] = $request->title;
        $datas["tel"] = $request->tel;
        $datas["provenance"] = $request->provenance;
        $datas["preferences"] = $request->preferences;
        $datas["ville"] = $request->city;
        $datas["pays"] = $request->country;
        $datas["twitter"] = $request->twitter;
        $datas["facebook"] = $request->facebook;
        $datas["whatsapp"] = $request->whatsapp;
        $datas["instagram"] = $request->instagram;
        $datas["linkedin"] = $request->linkedin;
        $datas["description"] = $request->description;
        $user->datas = $datas;
        $user->save();

        $user->assignRole(['user']);

        if ($request->file) {
            $user->addMedia($request->file)->toMediaCollection();
        }

        return redirect()->route("users.index")->with("success", "L'utilisateur a été enregistré avec succès.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {

        $fields = [];
        $fields[] = [
            "value" => $user->name,
            "name" => "Nom",
            "field_name" => "name",
            "placeholder" => "Nom de l'utilisateur",
            "type" => "text",
            "required" => true,
        ];
        $fields[] = [
            "value" => $user->email,
            "name" => "Email",
            "field_name" => "email",
            "placeholder" => "Email de l'utilisateur",
            "type" => "email",
            "required" => true,
        ];
        $fields[] = [
            "value" => ($user->datas != null && isset($user->datas["tel"])) ? $user->datas["tel"] : "",
            "name" => "Tel",
            "field_name" => "tel",
            "placeholder" => "N° de téléphone l'utilisateur",
            "type" => "tel",
            "required" => true,
        ];

        $fields[] = [
            "value" => ($user->datas != null && isset($user->datas["title"])) ? $user->datas["title"] : "",
            "name" => "Titre",
            "field_name" => "title",
            "placeholder" => "Titre de l'utilisateur",
            "type" => "text",
        ];
        $fields[] = [
            "value" => ($user->datas != null && isset($user->datas["ville"])) ? $user->datas["ville"] : "",
            "name" => "Ville",
            "field_name" => "city",
            "placeholder" => "Ville",
            "type" => "text",
        ];
        $fields[] = [
            "value" => ($user->datas != null && isset($user->datas["pays"])) ? $user->datas["pays"] : "",
            "name" => "Pays",
            "field_name" => "country",
            "placeholder" => "Pays",
            "type" => "text",
        ];

        $fields[] = [
            "value" => ($user->datas != null && isset($user->datas["twitter"])) ? $user->datas["twitter"] : "",
            "name" => "Profil Twitter",
            "field_name" => "twitter",
            "placeholder" => "Lien du compte twitter",
            "type" => "text",
        ];

        $fields[] = [
            "value" => ($user->datas != null && isset($user->datas["facebook"])) ? $user->datas["facebook"] : "",
            "name" => "Profil Facebook",
            "field_name" => "facebook",
            "placeholder" => "Lien du compte facebook",
            "type" => "text",
        ];
        $fields[] = [
            "value" => ($user->datas != null && isset($user->datas["linkedin"])) ? $user->datas["linkedin"] : "",
            "name" => "Profil LinkedIn",
            "field_name" => "linkedin",
            "placeholder" => "Lien du compte LinkedIn",
            "type" => "text",
        ];

        return view("admin.users.edit", compact("fields", "user"));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make(Str::random(8));
        $datas = [];
        $datas["visible"] = $request->visible ? 1 : 0;
        $datas["title"] = $request->title;
        $datas["tel"] = $request->tel;
        $datas["provenance"] = $request->provenance;
        $datas["preferences"] = $request->preferences;
        $datas["ville"] = $request->city;
        $datas["pays"] = $request->country;
        $datas["twitter"] = $request->twitter;
        $datas["facebook"] = $request->facebook;
        $datas["whatsapp"] = $request->whatsapp;
        $datas["instagram"] = $request->instagram;
        $datas["linkedin"] = $request->linkedin;
        $datas["description"] = $request->description;
        $user->datas = $datas;
        $user->save();

        if ($request->file) {
            foreach ($user->getMedia() as $media) {
                $media->delete();
            }
            $user->addMedia($request->file)->toMediaCollection();
        }

        return redirect()->back()->with("success", "L'utilisateur a bien été modifié");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
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
                if ($id != 1) {

                    $users = User::find($id);
                    //ActivityLogger::activity("Suppression de l'équipe ID:".$client->id.' par l\'utilisateur ID:'.Auth::id());
                    $users->delete();
                }
            }
            $message = sizeof($ids) . ' utilisteur(s) supprimé(s) avec succès';
        } else {
            $message = "Aucun utilisteur n'a été supprimé";
        }
        return redirect()->route("users.index")->with('success', $message);
    }
}
