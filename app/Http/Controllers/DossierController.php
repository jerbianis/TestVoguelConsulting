<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DossierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $dossiers = Dossier::all();
        return response()->json($dossiers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'nom' => ['required','string'],
            'slug' => ['required','alpha_dash:ascii','unique:dossiers,slug'],
            'dossier_racine_id' => ['nullable','exists:dossiers,id']
        ]);

        $dossier = Dossier::create($data);
        return response()->json($dossier, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dossier  $dossier
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Dossier $dossier)
    {
        //
        return response()->json($dossier);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dossier  $dossier
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Dossier $dossier)
    {
        //
        $data = $request->validate([
            'nom' => ['required','string'],
            'slug' => ['required','alpha_dash:ascii',Rule::unique('dossiers','slug')->ignore($dossier->id)],
            'dossier_racine_id' => ['nullable','exists:dossiers,id']
        ]);

        $dossier->update($data);
        return response()->json([
            'message' => 'Dossier mis à jour avec succès',
            'dossier' => $dossier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dossier  $dossier
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Dossier $dossier)
    {
        //
        $dossier->delete();
        return response()->json(['message' => 'Dossier supprimé avec succès']);
    }
    /**
     * Search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        //
        $mot= $request->nom;
        $dossiers = Dossier::where('nom', 'like', '%' . $mot . '%')
                        ->orWhere('slug', 'like', '%' . $mot . '%')
                        ->get();
        return response()->json($dossiers);
    }

}
