<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if ($query) {
            $users = User::where('username', 'LIKE', "%{$query}%")
                        ->orWhere('name', 'LIKE', "%{$query}%")
                        ->limit(5)
                        ->get();
            
            return response()->json($users);
        }
        
        return response()->json([]);
    }

    public function index()
    {
        return view('search.index');
    }
}
