<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
   /**
	 * @return $this
	 */
	public function index()
	{
		$countries = Country::orderBy('GCI_score', 'desc')->take(30)->get();
		$categories = $countries->pluck('name');
		//dd($countries);
		$standard = Country::standard();
		//dump($standard);
		$series = [];
		foreach($standard as $first) {
			$values = [];
			$urls = [];
			foreach( $countries as $key => $country) {
				$values[$key] = $country[$first['id']];
				$urls[$key] = $country->id;
			}
			array_push($series, [
				'name' => $first['name'],
			    'data' => $values,
			    'options' => $urls
			]);
		}
		/*
		foreach($countries as $key=>$tmp)
			$aaa[$key] = ($tmp->legal+$tmp->technical+$tmp->organizational+$tmp->capacityBuilding+$tmp->cooperation)/5.0;
		return json_encode($aaa);*/
		//return json_encode($categories);  
		return view('countries.index')->with([
			'companies' => $countries,
		    'categories' => json_encode($categories),
		    'series' => json_encode($series)
		]);
	}


	public function show($id)
    {
        $country = Country::findOrFail($id);

        if (empty($country)) {
            Flash::error('country not found');

            return redirect(route('coountries.index'));
        }


        $standard = Country::standard();

        $series = [];
        $categories = [];
        foreach($standard as $first) {
            array_push($series,  $country[ $first['id'] ]*100);
            array_push($categories,  $first['name'] );
        }
        #return $country->cooperation_description;
        return view('countries.show')->with([
				'country' => $country ,
				'categories' =>  json_encode( $categories ),
				'attributes' => json_encode( $series )
				 ]);
    }
}
