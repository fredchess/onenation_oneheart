<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $blogs = Blog::all();
        return view("admin.blogs.index", compact("blogs"));
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
            "name" => "Titre",
            "field_name" => "name",
            "placeholder" => "Titre de l'article",
            "type" => "text",
            "required" => true,
        ];

        return view("admin.blogs.create", compact("fields"));
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
        $blog = new Blog();

        $blog->name = $request->name;
        $blog->slug = Str::slug($request->name);
        $blog->status = $request->status ? 1 : 0;
        $datas = [
            "author_id" => Auth::user()->id,
            "author" => Auth::user()->name,
            "description" => $request->description,
            "public_content" => $request->public_content,
        ];
        $blog->datas = $datas;
        $blog->save();

        // dd($request->files);
        if ($request->files) {
            foreach ($request->files as $image) {
                $blog->addMedia($image)->toMediaCollection("images");
            }
        }

        return redirect()->route("blogs.index")->with("success", "L'article a été enregistré avec succès.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {

        $fields = [];
        $fields[] = [
            "name" => "Titre",
            "field_name" => "name",
            "placeholder" => "Titre de l'article",
            "type" => "text",
            "required" => true,
            "value"=>$blog->name
        ];

        return view("admin.blogs.edit", compact("fields", "blog"));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {

        $blog->name = $request->name;
        $blog->slug = Str::slug($request->name);
        $blog->status = $request->status ? 1 : 0;
        $datas = [
            "description" => $request->description,
            "public_content" => $request->public_content,
        ];
        $blog->datas = $datas;
        $blog->save();

        if ($request->files) {
            foreach ($blog->getMedia("images") as $media) {
                $media->delete();
            }

            foreach ($request->files as $image) {
                $blog->addMedia($image)->toMediaCollection("images");
            }
        }
        return redirect()->route("blogs.index")->with("success", "L'article a été modifié avec succès.");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
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
                $blog = Blog::find($id);
                //ActivityLogger::activity("Suppression de l'équipe ID:".$client->id.' par l\'utilisateur ID:'.Auth::id());
                $blog->delete();
            }
            $message = sizeof($ids) . ' article(s) supprimé(s) avec succès';
        } else {
            $message = "Aucun article n'a été supprimé";
        }
        return redirect()->route("blogs.index")->with('success', $message);
    }

    public function addImage(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'image', 'max:2048']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid image'
            ], 400);
        }

        $image = $request->file('file');
        $image_name = $image->getClientOriginalName();

        if (Storage::exists('public/article_images/'.$image_name)) {
            return response()->json(['location' => Storage::url('public/article_images/'.$image_name)]);
        }

        $path = $image->storeAs('public/article_images', $image_name);

        return response()->json([
            'location' => Storage::url($path)
        ]);
    }

}
