<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $documents = Document::all();
        return response()->json($documents);
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
            'description' => ['required','string'],
            'fichier' => ['required','file','mimes:doc,docm,docx,txt,pdf,rtf,xml,csv','max:10240'],
            'dossier_racine_id' => ['required','exists:dossiers,id']
        ]);
        $file = $request->file('fichier');
        $filePath = $file->store('documents','public');

        $document = Document::create([
            'nom'               => $data['nom'],
            'description'       => $data['description'],
            'fichier'           => $filePath,
            'dossier_racine_id' => $data['dossier_racine_id']
        ]);

        $document['fichier']= asset('storage/'.$document['fichier']);
        return response()->json($document, 201);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Document $document)
    {
        //
        $document['fichier']= asset('storage/'.$document['fichier']);
        return response()->json($document);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Document $document)
    {
        //
        $data = $request->validate([
            'nom' => ['required','string'],
            'description' => ['required','string'],
            'fichier' => ['nullable','file','mimes:doc,docm,docx,txt,pdf,rtf,xml,csv','max:10240'],
            'dossier_racine_id' => ['required','exists:dossiers,id']
        ]);

        if ($request->hasFile('fichier')) {
            Storage::delete($document->fichier);
            $file = $request->file('fichier');
            $filePath = $file->store('documents','public');
            $data['fichier'] = $filePath;
        }
        $document->update($data);

        $document['fichier']= asset('storage/'.$document['fichier']);
        return response()->json([
            'message' => 'Document mis à jour avec succès',
            'document' => $document
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Document $document)
    {
        //
        Storage::delete($document->fichier);
        $document->delete();
        return response()->json(['message' => 'Document supprimé avec succès']);
    }

    public function search(Request $request)
    {
        //
        $mot= $request->nom;
        $documents = Document::where('nom', 'like', '%' . $mot . '%')
            ->orWhere('description', 'like', '%' . $mot . '%')
            ->get();

        $documents->each(function ($item){
            $item['fichier'] = asset('storage/'.$item['fichier']);
        });

        return response()->json($documents);
    }
}
