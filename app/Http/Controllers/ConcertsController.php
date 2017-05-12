<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;

class ConcertsController extends Controller
{
    public function show($id)
    {
        if (Concert::published()->find($id))
        {
            $concert = Concert::published()->find($id);

            return view('concerts.show', ['concert' => $concert]);
        }

        return null;
    }
}
