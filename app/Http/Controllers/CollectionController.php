<?php

namespace App\Http\Controllers;

use App\Models\CollectionModel;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Menampilkan detail koleksi film
     */
    public function show($id)
    {
        // Mengambil data koleksi berdasarkan tmdb_collection_id beserta film & genre di dalamnya
        $collection = CollectionModel::with('movies.genres.genre')
            ->where('tmdb_collection_id', $id)
            ->firstOrFail();

        return view('pages.collection-detail', compact('collection'));
    }
}