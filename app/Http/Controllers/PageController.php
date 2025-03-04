<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\City;
use App\Models\Donation;
use App\Models\Orphanage;
use App\Models\Partner;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function home(Request $request)
    {
        $partners = Partner::all();

        $testimonies = [];
        $testimonies[] = [
            "name" => "Mr Kameni",
            "message" => "« Je loue d’éjà l’initiative et je me dois de coopérer pour la bonne marche du projet. Si tu as besoin de données, fais-moi savoir et que le Seigneur fasse grâce pour la bonne marche du projet.»",
            "title" => "Responsable du Centre d’Accueil Eau Vive",
            "image" => null,
        ];
        $testimonies[] = [
            "name" => "Paul Fouda",
            "message" => "« J’apprécie l’initiative et me rendrai disponible… »",
            "title" => "Champion du Cameroun des échecs parties rapide 2021.",
            "image" => null,
        ];
        $testimonies[] = [
            "name" => "M. OLA Antoine",
            "message" => "« La plateforme est intéressante parce qu’en effet les besoins réels des orphelinats les gens ne les connaissent pas en général. [...]»",
            "title" => "Bénévole et responsable communication de l’orphelinat Main dans la main",
            "image" => null,
        ];
        $testimonies[] = [
            "name" => "Hervé Cédric",
            "message" => "« Together we are more stronger! God Bless you and this marvelous initiative! We’ll definitely work together! »",
            "title" => "Vice Président de l’Association Yes Health",
            "image" => null,
        ];

        $testimonies[] = [
            "name" => "Maman Nanfack",
            "message" => "« Je trouve le projet très louable et vous encourage dans cette lancée. J’aimerais que nous travaillions ensemble et avoir des retours par rapports aux actions que vous menez.»",
            "title" => "Assistante sociale à l'hôpital de district de Biyem-Assi",
            "image" => null,
        ];

        $total_enfants = 0;

        $orphelinats = Orphanage::get();

        $total_orphanages = $orphelinats->count();

        foreach ($orphelinats as $data) {
            $total_kids = $data->data_stats['children_number'] ?? 0;

            $total_enfants += intval($total_kids);
        }

        // Ne considerer que les dons qui ont ete valides
        $total_donations = Donation::where('status', 1)->sum("amount");
        $total_cities = count(City::all());

        $blogs = Blog::latest()->paginate(9);

        return view("front.home", compact("partners", "testimonies", "total_donations", "blogs", "total_enfants", "total_orphanages", "total_cities"));
    }
    public function contact(Request $request)
    {
        return view("front.contact");
    }
    public function about(Request $request)
    {
        $users = User::all();
        return view("front.about", compact("users"));
    }

    public function blog(Request $request)
    {
        $blogs = Blog::paginate(9);
        return view("front.blog", compact("blogs"));
    }

    public function blog_detail(Request $request, $orphanage_slug)
    {

        $blog = Blog::where("slug", $orphanage_slug)->first();
        views($blog)->record();
        return view("front.blogs_details", compact("blog"));
    }

    public function orphanages(Request $request)
    {
        try {
            $villes = City::all();
            // recherche par le nom
            $orphelinats = Orphanage::where('name', 'like', "%{$request->search}%")
                                    ->where('status', '=', 1);

            //quartier
            if ($request->street != null) {
                $query = $orphelinats->newQuery();

                $orphelinats = $query->where('orphanages.data_address->localisation', 'like', "%{$request->street}%");
            }

            // Recherche par tranche d'age
            if ($request->ages != null) {
                $query = $orphelinats->newQuery();

                $ages = $request->ages;

                $orphelinats = $orphelinats->where(function ($q) use ($ages) {
                    foreach ($ages as $age) {
                        switch ($age) {
                            case 1:
                                $q->orwhere('orphanages.data_stats->children_number_0_6', '>', 0);
                                break;
                            case 2:
                                $q->orwhere('orphanages.data_stats->children_number_7_13', '>', 0);
                                break;
                            case 3:
                                $q->orwhere('orphanages.data_stats->children_number_14_21', '>', 0);
                                break;
                            default:
                                break;
                        }
                    }
                });
            }


            // Recherche par nom des villes
            if ($request->villes != null)
            {
                $cities = $request->villes;

                $orphelinats = $orphelinats->whereIn('city_id', function ($q) use ($cities) {
                    $q->select('id')
                            ->from('cities')
                            ->whereIn('name', $cities);
                });
            }

            //le nombre d'enfants
            if ($request->profesional_search_children != null) {

            }

            //le nombre d'enfants en recherche de professionnalisation
            if ($request->total_children != null) {

            }

            //le nombre d'enfants en classe d'examen
            if ($request->exam_class_children != null) {

            }

            // Trier par ...
            if ($request->input('sort')) {
                switch ($request->input('sort')) {
                    case 1:
                        $orphelinats->orderBy('data_stats->children_number', 'asc');
                        break;
                    case 2:
                        $orphelinats->orderBy('data_stats->children_number', 'desc');
                        break;
                }
            }

            $orphelinats = $orphelinats->paginate(9);

            $orphelinats->appends(['search' => $request->input('search'),
                                'street' => $request->input('street'),
                                'sort' => $request->input('sort'),
                                'villes' => $request->input('villes', []),
                                'ages' => $request->input('ages', [])]);

            return view("front.orphanages", compact("orphelinats", "villes"));
        }
        catch (Exception $e) {
            return redirect()->route('public.orphanages');
        }
    }
    public function orphanages_detail(Request $request, $orphanage_slug)
    {

        $orphelinat = Orphanage::where("slug", $orphanage_slug)->first();
        views($orphelinat)->record();
        $other_orphanages = City::where('id', $orphelinat->city_id)->first();
        $other_orphanages = $other_orphanages->orphanages;

        return view("front.orphanages_details", compact("orphelinat", "other_orphanages"));
    }

    public function joinus(Request $request)
    {
        return view("front.joinus");
    }

    public function joinus_register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
        ]);
        
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $datas = [];
        $datas["visible"] = 0; // invisible on homepage
        $datas["tel"] = $request->tel;
        $datas["register_from"] = "JoinUs Page";
        $datas["past_experience"] = $request->past_experience;
        $datas["provenance"] = $request->provenance;
        $datas["preferences"] = $request->preferences;
        $datas["ville"] = $request->city;
        $datas["pays"] = "";
        $datas["twitter"] = "";
        $datas["facebook"] = "";
        $datas["whatsapp"] = "";
        $datas["instagram"] = "";
        $datas["linkedin"] = "";
        $datas["description"] = "";
        $datas["profile_picture"] = "";
        $user->password = bcrypt(Str::random(8));
        $user->datas = $datas;
        $user->save();
        $user->assignRole(['user', 'volunteer']);

        return redirect()->back()->with("success", "Votre enregistrement a bien été pris en compte");
    }
    //
}
