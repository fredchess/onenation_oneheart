<?php

namespace App\Http\Controllers;

use App\Exports\OrphanagesExport;
use App\Models\City;
use App\Models\Orphanage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class OrphanageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $user = auth()->user();
        if ($user->roles->pluck('name')->contains('responsable')) {
            if ($user->orphanage == null) return abort(403);
            return redirect(route('orphanages.edit', ['orphanage' => $user->orphanage]));
        }
        
        $orphanages = Orphanage::all();
        return view("admin.orphanages.index", compact("orphanages"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();

        $orphanage = new Orphanage();

        $data_identity = [];
        $data_identity_promoter = [];
        $data_address = [];
        $data_financial_infos = [];
        $data_stats = [];
        $data_education = [];
        $data_needs = [];
        $data_projects = [];

        // Identite de l'orphelinat

        $data_identity['name'] = [
            "name" => "Nom",
            "field_name" => "name",
            "placeholder" => "Nom de l'orphelinat",
            "type" => "text",
            "required" => true,
        ];

        $data_identity['email'] = [
            "name" => "Email",
            "field_name" => "email",
            "placeholder" => "Email de l'orphelinat",
            "type" => "text",
        ];

        $data_identity['website'] = [
            "name" => "Site web",
            "field_name" => "website",
            "placeholder" => "Site web de l'orphelinat",
            "type" => "text",
        ];

        $data_identity['phone'] = [
            "name" => "Téléphone",
            "field_name" => "phone",
            "placeholder" => "Téléphone de l'orphelinat",
            "type" => "text",
            "required" => true,
        ];

        $data_identity['arrete'] = [
            "name" => "Arrêté",
            "field_name" => "arrete_number",
            "placeholder" => "Numero d'Arrêté",
            "type" => "text",
        ];

        $data_identity['mini_description'] = [
            "name" => "Mini description",
            "field_name" => "mini_description",
            "placeholder" => "Mini Description de l'orphelinat",
            "type" => "textarea",
        ];

        $data_identity['description'] = [
            "name" => "Description",
            "field_name" => "description",
            "placeholder" => "Description de l'orphelinat",
            "type" => "text",
        ];

        $data_identity['history'] = [
            "name" => "Histoire",
            "field_name" => "history",
            "placeholder" => "Histoire de l'orphelinat",
            "type" => "text",
        ];

        // Infos promoter
        $data_identity_promoter['name'] = [
            "name" => "Nom",
            "field_name" => "promoter_name",
            "placeholder" => "Nom du promoteur",
            "type" => "text",
            "required" => true,
        ];

        $data_identity_promoter['phone'] = [
            "name" => "Téléphone",
            "field_name" => "promoter_phone",
            "placeholder" => "Téléphone du promoteur",
            "type" => "text",
            "required" => true,
        ];

        $data_identity_promoter['email'] = [
            "name" => "Email",
            "field_name" => "promoter_email",
            "placeholder" => "Email du promoteur",
            "type" => "text",
            "required" => true,
        ];

        $data_identity_promoter['second_name'] = [
            "name" => "Nom du second responsable",
            "field_name" => "second_name",
            "placeholder" => "Nom du second responsable",
            "type" => "text",
        ];

        $data_identity_promoter['second_phone'] = [
            "name" => "Téléphone du second",
            "field_name" => "second_phone",
            "placeholder" => "Téléphone du second responsable",
            "type" => "text"
        ];

        // Address

        $data_address['google_address'] = [
            "name" => "Adresse Google",
            "field_name" => "google_address",
            "placeholder" => "Localisation google",
            "type" => "text",
            "required" => true,
        ];

        $data_address['localisation'] = [
            "name" => "Localisation",
            "field_name" => "localisation",
            "placeholder" => "localisation (quartier)",
            "type" => "text",
            "required" => true,
        ];

        // financials

        $data_financial_infos['bank_number'] = [
            "name" => "Compte bancaire",
            "field_name" => "bank_number",
            "placeholder" => "Numero de compte bancaire",
            "type" => "text",
        ];

        $data_financial_infos['om_momo'] = [
            "name" => "OM/MOMO",
            "field_name" => "om_momo",
            "placeholder" => "Compte OM/MOMO",
            "type" => "text",
        ];

        // stats
        $data_stats['children_number'] = [
            "name" => "Nombre d'enfants",
            "field_name" => "children_number",
            "placeholder" => "Nombre d'enfants",
            "type" => "number",
            "required" => true,
        ];

        $data_stats['children_number_0_6'] = [
            "name" => "0 - 6 ans",
            "field_name" => "children_number_0_6",
            "placeholder" => "Nombres d'enfants dans la tranche 0-6 ans",
            "type" => "number",
        ];

        $data_stats['children_number_7_13'] = [
            "name" => "7 - 13 ans",
            "field_name" => "children_number_7_13",
            "placeholder" => "Nombres d'enfants dans la tranche 7-13 ans",
            "type" => "number",
        ];

        $data_stats['children_number_14_21'] = [
            "name" => "14 - 21 ans",
            "field_name" => "children_number_14_21",
            "placeholder" => "Nombres d'enfants dans la tranche 14-21 ans",
            "type" => "number",
        ];

        $data_stats['boys_number'] = [
            "name" => "Nombre de garçons",
            "field_name" => "boys_number",
            "placeholder" => "Nombre de garçons",
            "type" => "number",
            "required" => true,
        ];

        $data_stats['girls_number'] = [
            "name" => "Nombre de filles",
            "field_name" => "girls_number",
            "placeholder" => "Nombre de filles",
            "type" => "number",
            "required" => true,
        ];

        // Education

        $data_education['school_children_number'] = [
            "name" => "scolarisés",
            "field_name" => "school_children_number",
            "placeholder" => "Nombre d'enfants scolarisés",
            "type" => "number",
        ];

        $data_education['schoolexam_children_number'] = [
            "name" => "Enfants en classe d'examen",
            "field_name" => "schoolexam_children_number",
            "placeholder" => "Nombre d'enfants en classe d'examen",
            "type" => "number",
        ];

        $data_education['maternelle_children_number'] = [
            "name" => "Enfants en maternelle",
            "field_name" => "maternelle_children_number",
            "placeholder" => "Nombre d'enfants en maternelle",
            "type" => "number",
        ];

        $data_education['primary_children_number'] = [
            "name" => "Enfants en primaire",
            "field_name" => "primary_children_number",
            "placeholder" => "Nombre d'enfants en primaire",
            "type" => "number",
        ];

        $data_education['college_children_number'] = [
            "name" => "Enfants au collège",
            "field_name" => "college_children_number",
            "placeholder" => "Nombre d'enfants au collège",
            "type" => "number",
        ];

        $data_education['university_children_number'] = [
            "name" => "Enfants à l'université",
            "field_name" => "university_children_number",
            "placeholder" => "Nombre d'enfants à l'université",
            "type" => "number",
        ];

        $data_education['professional_children_number'] = [
            "name" => "professionels",
            "field_name" => "professional_children_number",
            "placeholder" => "Nombre d'enfants en recherche de formation professionnelle",
            "type" => "number",
        ];

        // Besoins

        $data_needs['food_needs'] = [
            "name" => "Alimentaires",
            "field_name" => "food_needs",
            "placeholder" => "Besoins alimentaires",
            "type" => "text",
        ];

        $data_needs['health_needs'] = [
            "name" => "Sanitaires + hygiéniques",
            "field_name" => "health_needs",
            "placeholder" => "Besoins sanitaires + hygiéniques",
            "type" => "text",
        ];

        $data_needs['school_needs'] = [
            "name" => "Scolaires",
            "field_name" => "school_needs",
            "placeholder" => "Besoins Scolaires",
            "type" => "text",
        ];

        $data_needs['clothes_needs'] = [
            "name" => "Vestimentaires",
            "field_name" => "clothes_needs",
            "placeholder" => "Besoins vestimentaires",
            "type" => "text",
        ];

        $data_needs['ludic_needs'] = [
            "name" => "Ludiques",
            "field_name" => "ludic_needs",
            "placeholder" => "Besoins ludiques",
            "type" => "text",
        ];

        $data_needs['other_needs'] = [
            "name" => "Autres",
            "field_name" => "other_needs",
            "placeholder" => "Autres besoins",
            "type" => "text",
        ];

        // projets
        $data_projects['projects'] = [
            "name" => "Projets",
            "field_name" => "projects",
            "placeholder" => "Projets",
            "type" => "text",
        ];

        // responsables

        $responsables = User::with('roles')->get()->filter(function (User $user) {
            return $user->hasRole('responsable');
        });

        return view("admin.orphanages.create", compact("cities", "data_needs", 'data_education',
        'data_stats', 'data_financial_infos', 'data_address', 'data_identity_promoter', 'data_identity', 'data_projects', 'orphanage', 'responsables'));
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


        $validator = Validator::make($request->all(), [
            'responsable_id' => [
                'nullable',
                'unique:orphanages,responsable_id'
            ]
        ]);

        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors())->withInput();


        $orphanage = new Orphanage();
        $orphanage->slug = Str::slug($request->name);
        $orphanage->name = $request->name;
        $orphanage->data_identity = [
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'phone' => $request->phone,
            'arrete_number' => $request->arrete_number,
            'history' => $request->history,
            'description' => $request->description,
            'withonoh' => $request->withonoh,
            'mini_description' => $request->mini_description
        ];
        $orphanage->data_identity_promoter = [
            'promoter_name' => $request->promoter_name,
            'promoter_phone' => $request->promoter_phone,
            'promoter_email' => $request->promoter_email,
            'second_name' => $request->second_name,
            'second_phone' => $request->second_phone,
        ];
        $orphanage->data_address = [
            'google_address' => $request->google_address,
            'localisation' => $request->localisation,
        ];
        $orphanage->data_financial_infos = [
            'bank_number' => $request->bank_number,
            'om_momo' => $request->om_momo,
        ];
        $orphanage->data_stats = [
            'children_number' => $request->children_number,
            'children_number_0_6' => $request->children_number_0_6,
            'children_number_7_13' => $request->children_number_7_13,
            'children_number_14_21' => $request->children_number_14_21,
            'boys_number' => $request->boys_number,
            'girls_number' => $request->girls_number,
        ];
        $orphanage->data_education = [
            'school_children_number' => $request->school_children_number,
            'schoolexam_children_number' => $request->schoolexam_children_number,
            'maternelle_children_number' => $request->maternelle_children_number,
            'primary_children_number' => $request->primary_children_number,
            'college_children_number' => $request->college_children_number,
            'university_children_number' => $request->university_children_number,
            'professional_children_number' => $request->professional_children_number,
        ];
        $orphanage->data_needs = [
            'food_needs' => $request->food_needs,
            'health_needs' => $request->health_needs,
            'school_needs' => $request->school_needs,
            'clothes_needs' => $request->clothes_needs,
            'ludic_needs' => $request->ludic_needs,
            'other_needs' => $request->other_needs,
        ];
        $orphanage->data_projects = [
            'projects' => $request->projects
        ];

        $orphanage->city_id = $request->city;
        $orphanage->status = $request->status;


        $orphanage->save();

        // dd($request->files);
        // if ($request->files) {
        //     foreach ($request->files as $image) {
        //         $orphanage->addMedia($image)->toMediaCollection("images");
        //     }
        // }

        if ($request->hasFile('profile_image')) {
            $orphanage->addMedia($request->file('profile_image'))->toMediaCollection('profile_images');
        }

        return redirect()->route("orphanages.index")->with("success", "L'orphelinat a été enregistré avec succès.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Orphanage  $orphanage
     * @return \Illuminate\Http\Response
     */
    public function show(Orphanage $orphanage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Orphanage  $orphanage
     * @return \Illuminate\Http\Response
     */
    public function edit(Orphanage $orphanage)
    {

        $cities = City::all();

        /**
         * @var User $connected_user
         */
        $connected_user = auth()->user();

        if ($connected_user->hasRole('responsable') && $connected_user->id != $orphanage->responsable_id) {
            return abort(403);
        }

        $responsables = User::with('roles')->get()->filter(function (User $user) {
            return $user->hasRole('responsable');
        });

        $data_identity = [];
        $data_identity_promoter = [];
        $data_address = [];
        $data_financial_infos = [];
        $data_stats = [];
        $data_education = [];
        $data_needs = [];
        $data_projects = [];

        // Identite de l'orphelinat

        $data_identity['name'] = [
            "name" => "Nom",
            "field_name" => "name",
            "value" => $orphanage->data_identity['name'] ?? '',
            "placeholder" => "Nom de l'orphelinat",
            "type" => "text",
            "required" => true,
        ];

        $data_identity['email'] = [
            "name" => "Email",
            "field_name" => "email",
            "value" => $orphanage->data_identity['email'] ?? '',
            "placeholder" => "Email de l'orphelinat",
            "type" => "text",
        ];

        $data_identity['website'] = [
            "name" => "Site web",
            "field_name" => "website",
            "value" => $orphanage->data_identity['website'] ?? '',
            "placeholder" => "Site web de l'orphelinat",
            "type" => "text",
        ];

        $data_identity['phone'] = [
            "name" => "Téléphone",
            "field_name" => "phone",
            "value" => $orphanage->data_identity['phone'] ?? '',
            "placeholder" => "Téléphone de l'orphelinat",
            "type" => "text",
            "required" => true,
        ];

        $data_identity['arrete'] = [
            "name" => "Arrêté",
            "field_name" => "arrete_number",
            "value" => $orphanage->data_identity['arrete_number'] ?? '',
            "placeholder" => "Numero d'Arrêté",
            "type" => "text",
        ];

        $data_identity['mini_description'] = [
            "name" => "Mini description",
            "field_name" => "mini_description",
            "value" => $orphanage->data_identity['mini_description'] ?? '',
            "placeholder" => "Mini Description de l'orphelinat",
            "type" => "textarea",
        ];

        $data_identity['description'] = [
            "name" => "Description",
            "field_name" => "description",
            "value" => $orphanage->data_identity['description'] ?? '',
            "placeholder" => "Description de l'orphelinat",
            "type" => "text",
        ];

        $data_identity['history'] = [
            "name" => "Histoire",
            "field_name" => "history",
            "value" => $orphanage->data_identity['history'] ?? '',
            "placeholder" => "Histoire de l'orphelinat",
            "type" => "text",
        ];

        $data_identity['withonoh'] = [
            "name" => "withonoh",
            "field_name" => "withonoh",
            "value" => $orphanage->data_identity['withonoh'] ?? '',
            "placeholder" => "Histoire de l'orphelinat avec onoh",
            "type" => "text",
        ];

        // Infos promoter
        $data_identity_promoter['name'] = [
            "name" => "Nom",
            "field_name" => "promoter_name",
            "value" => $orphanage->data_identity_promoter['promoter_name'] ?? '',
            "placeholder" => "Nom du promoteur",
            "type" => "text",
            "required" => true,
        ];

        $data_identity_promoter['phone'] = [
            "name" => "Téléphone",
            "field_name" => "promoter_phone",
            "value" => $orphanage->data_identity_promoter['promoter_phone'] ?? '',
            "placeholder" => "Téléphone du promoteur",
            "type" => "text",
            "required" => true,
        ];

        $data_identity_promoter['email'] = [
            "name" => "Email",
            "field_name" => "promoter_email",
            "value" => $orphanage->data_identity_promoter['promoter_email'] ?? '',
            "placeholder" => "Email du promoteur",
            "type" => "text",
        ];

        $data_identity_promoter['second_name'] = [
            "name" => "Nom du second",
            "field_name" => "second_name",
            "value" => $orphanage->data_identity_promoter['second_name'] ?? '',
            "placeholder" => "Nom du second responsable",
            "type" => "text",
        ];

        $data_identity_promoter['second_phone'] = [
            "name" => "Téléphone du second",
            "field_name" => "second_phone",
            "value" => $orphanage->data_identity_promoter['second_phone'] ?? '',
            "placeholder" => "Téléphone du second responsable",
            "type" => "text",
        ];

        // Address

        $data_address['google_address'] = [
            "name" => "Adresse Google",
            "field_name" => "google_address",
            "value" => $orphanage->data_address['google_address'] ?? '',
            "placeholder" => "Localisation google",
            "type" => "text",
            "required" => true,
        ];

        $data_address['localisation'] = [
            "name" => "Localisation",
            "field_name" => "localisation",
            "value" => $orphanage->data_address['localisation'] ?? '',
            "placeholder" => "localisation (quartier)",
            "type" => "text",
            "required" => true,
        ];

        // financials

        $data_financial_infos['bank_number'] = [
            "name" => "Compte bancaire",
            "field_name" => "bank_number",
            "value" => $orphanage->data_financial_infos['bank_number'] ?? '',
            "placeholder" => "Numero de compte bancaire",
            "type" => "text",
        ];

        $data_financial_infos['om_momo'] = [
            "name" => "OM/MOMO",
            "field_name" => "om_momo",
            "value" => $orphanage->data_financial_infos['om_momo'] ?? '',
            "placeholder" => "Compte OM/MOMO",
            "type" => "text",
        ];

        // stats
        $data_stats['children_number'] = [
            "name" => "Nombre d'enfants",
            "field_name" => "children_number",
            "value" => $orphanage->data_stats['children_number'] ?? '',
            "placeholder" => "Nombre d'enfants",
            "type" => "number",
            "required" => true,
        ];

        $data_stats['children_number_0_6'] = [
            "name" => "0 - 6 ans",
            "field_name" => "children_number_0_6",
            "value" => $orphanage->data_stats['children_number_0_6'] ?? '',
            "placeholder" => "Nombres d'enfants dans la tranche 0-6 ans",
            "type" => "number",
        ];

        $data_stats['children_number_7_13'] = [
            "name" => "7 - 13 ans",
            "field_name" => "children_number_7_13",
            "value" => $orphanage->data_stats['children_number_7_13'] ?? '',
            "placeholder" => "Nombres d'enfants dans la tranche 7-13 ans",
            "type" => "number",
        ];

        $data_stats['children_number_14_21'] = [
            "name" => "14 - 21 ans",
            "field_name" => "children_number_14_21",
            "value" => $orphanage->data_stats['children_number_14_21'] ?? '',
            "placeholder" => "Nombres d'enfants dans la tranche 14-21 ans",
            "type" => "number",
        ];

        $data_stats['boys_number'] = [
            "name" => "Nombre de garçons",
            "field_name" => "boys_number",
            "value" => $orphanage->data_stats['boys_number'] ?? '',
            "placeholder" => "Nombre de garçons",
            "type" => "number",
            "required" => true,
        ];

        $data_stats['girls_number'] = [
            "name" => "Nombre de filles",
            "field_name" => "girls_number",
            "value" => $orphanage->data_stats['girls_number'] ?? '',
            "placeholder" => "Nombre de filles",
            "type" => "number",
            "required" => true,
        ];

        // Education

        $data_education['school_children_number'] = [
            "name" => "scolarisés",
            "field_name" => "school_children_number",
            "value" => $orphanage->data_education['school_children_number'] ?? '',
            "placeholder" => "Nombre d'enfants scolarisés",
            "type" => "number",
        ];

        $data_education['schoolexam_children_number'] = [
            "name" => "Enfants en classe d'examen",
            "field_name" => "schoolexam_children_number",
            "value" => $orphanage->data_education['schoolexam_children_number'] ?? '',
            "placeholder" => "Nombre d'enfants en classe d'examen",
            "type" => "number",
        ];

        $data_education['maternelle_children_number'] = [
            "name" => "Enfants en maternelle",
            "field_name" => "maternelle_children_number",
            "value" => $orphanage->data_education['maternelle_children_number'] ?? '',
            "placeholder" => "Nombre d'enfants en maternelle",
            "type" => "number",
        ];

        $data_education['primary_children_number'] = [
            "name" => "Enfants en primaire",
            "field_name" => "primary_children_number",
            "value" => $orphanage->data_education['primary_children_number'] ?? '',
            "placeholder" => "Nombre d'enfants en primaire",
            "type" => "number",
        ];

        $data_education['college_children_number'] = [
            "name" => "Enfants au collège",
            "field_name" => "college_children_number",
            "value" => $orphanage->data_education['college_children_number'] ?? '',
            "placeholder" => "Nombre d'enfants au collège",
            "type" => "number",
        ];

        $data_education['university_children_number'] = [
            "name" => "Enfants à l'université",
            "field_name" => "university_children_number",
            "value" => $orphanage->data_education['university_children_number'] ?? '',
            "placeholder" => "Nombre d'enfants à l'université",
            "type" => "number",
        ];

        $data_education['professional_children_number'] = [
            "name" => "professionels",
            "field_name" => "professional_children_number",
            "value" => $orphanage->data_education['professional_children_number'] ?? '',
            "placeholder" => "Nombre d'enfants en recherche de formation professionnelle",
            "type" => "number",
        ];

        // Besoins

        $data_needs['food_needs'] = [
            "name" => "Alimentaires",
            "field_name" => "food_needs",
            "value" => $orphanage->data_needs['food_needs'] ?? '',
            "placeholder" => "Besoins alimentaires",
            "type" => "text",
        ];

        $data_needs['health_needs'] = [
            "name" => "Sanitaires + hygiéniques",
            "field_name" => "health_needs",
            "value" => $orphanage->data_needs['health_needs'] ?? '',
            "placeholder" => "Besoins sanitaires + hygiéniques",
            "type" => "text",
        ];

        $data_needs['school_needs'] = [
            "name" => "Scolaires",
            "field_name" => "school_needs",
            "value" => $orphanage->data_needs['school_needs'] ?? '',
            "placeholder" => "Besoins Scolaires",
            "type" => "text",
        ];

        $data_needs['clothes_needs'] = [
            "name" => "Vestimentaires",
            "field_name" => "clothes_needs",
            "value" => $orphanage->data_needs['clothes_needs'] ?? '',
            "placeholder" => "Besoins vestimentaires",
            "type" => "text",
        ];

        $data_needs['ludic_needs'] = [
            "name" => "Ludiques",
            "field_name" => "ludic_needs",
            "value" => $orphanage->data_needs['ludic_needs'] ?? '',
            "placeholder" => "Besoins ludiques",
            "type" => "text",
        ];

        $data_needs['other_needs'] = [
            "name" => "Autres",
            "field_name" => "other_needs",
            "value" => $orphanage->data_needs['other_needs'] ?? '',
            "placeholder" => "Autres besoins",
            "type" => "text",
        ];

        // projets
        $data_projects['projects'] = [
            "name" => "Projets",
            "field_name" => "projects",
            "value" => $orphanage->data_projects['projects'] ?? '',
            "placeholder" => "Projets",
            "type" => "text",
        ];

        return view("admin.orphanages.edit", compact("cities", "data_needs", 'data_education',
            'data_stats', 'data_financial_infos', 'data_address', 'data_identity_promoter', 'data_identity', 'data_projects', 'orphanage', 'responsables'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Orphanage  $orphanage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Orphanage $orphanage)
    {
        /**
         * @var User $connected_user
         */
        $connected_user = auth()->user();
        if (!$connected_user->hasRole('responsable')) {
            $validator = Validator::make($request->all(), [
                'responsable_id' => [
                    'nullable',
                    Rule::unique('orphanages')->ignore($orphanage->responsable_id, 'responsable_id'),
                ]
            ]);
    
            if ($validator->fails()) return redirect()->back()->withErrors($validator->errors())->withInput();


            $orphanage->data_identity_promoter = [
                'promoter_name' => $request->promoter_name,
                'promoter_phone' => $request->promoter_phone,
                'promoter_email' => $request->promoter_email,
                'second_name' => $request->second_name,
                'second_phone' => $request->second_phone,
            ];
        }

        $orphanage->slug = Str::slug($request->name);

        $orphanage->name = $request->name;
        
        $orphanage->data_identity = [
            'name' => $request->name,
            'email' => $request->email,
            'website' => $request->website,
            'phone' => $request->phone,
            'arrete_number' => $request->arrete_number,
            'history' => $request->history,
            'description' => $request->description,
            'withonoh' => $request->withonoh,
            'mini_description' => $request->mini_description
        ];

        $orphanage->data_address = [
            'google_address' => $request->google_address,
            'localisation' => $request->localisation,
        ];
        $orphanage->data_financial_infos = [
            'bank_number' => $request->bank_number,
            'om_momo' => $request->om_momo,
        ];
        $orphanage->data_stats = [
            'children_number' => $request->children_number,
            'children_number_0_6' => $request->children_number_0_6,
            'children_number_7_13' => $request->children_number_7_13,
            'children_number_14_21' => $request->children_number_14_21,
            'boys_number' => $request->boys_number,
            'girls_number' => $request->girls_number,
        ];
        $orphanage->data_education = [
            'school_children_number' => $request->school_children_number,
            'schoolexam_children_number' => $request->schoolexam_children_number,
            'maternelle_children_number' => $request->maternelle_children_number,
            'primary_children_number' => $request->primary_children_number,
            'college_children_number' => $request->college_children_number,
            'university_children_number' => $request->university_children_number,
            'professional_children_number' => $request->professional_children_number,
        ];
        $orphanage->data_needs = [
            'food_needs' => $request->food_needs,
            'health_needs' => $request->health_needs,
            'school_needs' => $request->school_needs,
            'clothes_needs' => $request->clothes_needs,
            'ludic_needs' => $request->ludic_needs,
            'other_needs' => $request->other_needs,
        ];
        $orphanage->data_projects = [
            'projects' => $request->projects
        ];

        $orphanage->city_id = $request->city;
        $orphanage->status = $request->status ?? 0;

        if ($request->has('responsable_id')) $orphanage->responsable_id = $request->responsable_id;

        $orphanage->save();

        if ($request->hasFile('profile_image')) {
            foreach($orphanage->getMedia("profile_images") as $media)
                $media->delete();
            $orphanage->addMedia($request->file('profile_image'))->toMediaCollection('profile_images');
        }

        if ($connected_user->hasRole('responsable')) return redirect()->route("admins.home")->with("success", "L'orphelinat a été modifié avec succès.");

        return redirect()->route("orphanages.index")->with("success", "L'orphelinat a été modifié avec succès.");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Orphanage  $orphanage
     * @return \Illuminate\Http\Response
     */
    public function destroy(Orphanage $orphanage)
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
                $orphanage = Orphanage::find($id);
                //ActivityLogger::activity("Suppression de l'équipe ID:".$client->id.' par l\'utilisateur ID:'.Auth::id());
                $orphanage->delete();
            }
            $message = sizeof($ids) . ' orphelinat(s) supprimé(s) avec succès';
        } else {
            $message = "Aucun orphelinat n'a été supprimé";
        }
        return redirect()->route("orphanages.index")->with('success', $message);
    }

    public function storeImages(Request $request): JsonResponse
    {
        $path = storage_path('tmp/uploads');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function download() 
    {
        return Excel::download(new OrphanagesExport, 'orphelinats.xlsx');
    }

}
