<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getUser()
    {
    	return 1;
    }

    public function dot($a, $b)
    {
    	$result = 0.0;
    	for ($i=0; $i<count($a); $i++) {
    		$result += ($a[$i] * $b[$i]);
    	}

    	return $result;
    }

    public function cosineSimilarity($node)
    {
   		$nearestCosine = 0.0;
 		$nearestUser = null;
 		$biggestRate = 0;
    	foreach ($node as $key => $user) {
    		foreach ($user as $dimension) {
    			$a[] = $dimension[0];
    			$b[] = $dimension[1];
    		}

    		// var_dump($a); echo "<br>";
    		// var_dump($b); echo "<br>";
    		$cosineDistance = $this->dot($a, $b) / (sqrt($this->dot($a, $a)) * sqrt($this->dot($b, $b)));

			// echo $cosineDistance . " " . $key . "<br>";
    		if($cosineDistance >= $nearestCosine && count($a) >= $biggestRate || (count($a) > $biggestRate && ($nearestCosine - $cosineDistance) <= 0.1)) {
    			$nearestCosine = $cosineDistance;
    			$nearestUser = $key;
    			$biggestRate = count($a);
    			// echo "updated : " . $nearestCosine . " ". $nearestUser . "<br>";
    		}
    		
    		$a = array();
    		$b = array();
    	}

    	return $nearestUser;
    }

    public function firstPage()
    {
    	// K-nearest neighbour algorithm
    	$movie = DB::table('movie')->where('name', '!=', 'Asc')->get();

    	$userRateObject = DB::table('user_rate')->where('id_user', $this->getUser())->get();
    	$ratedMovies = array();

    	$node = array();
    	foreach ($userRateObject as $userRate) {
    		$movieRateObject = DB::table('user_rate')->where('id_movie', $userRate->id_movie)->where('id_user', '!=', $userRate->id_user)->get();

    		foreach ($movieRateObject as $movieRate) {
    			$node[$movieRate->id_user][] = array($userRate->rate, $movieRate->rate);
    		}
    		$ratedMovies[] = $userRate->id_movie;
    	}

    	$nearestUser = $this->cosineSimilarity($node);
    	$nearestUserRatedMovie = DB::table('user_rate')->where('id_user', $nearestUser)->get();

    	$recommendedMovieId = [];
    	foreach ($nearestUserRatedMovie as $movie) {
    		if (!in_array($movie->id_movie, $ratedMovies)) {
    			$recommendedMovieId[] =  $movie->id_movie;
    		}
    	}

    	$recommendedMovieObject = DB::table('movie')->whereIn('id', $recommendedMovieId)->get();

    	echo "<pre>";
    	var_dump($recommendedMovieObject);
    	echo "</pre>";
    	
    	return view('welcome', ['recommendedMovieObject' => $recommendedMovieObject]);
    }

    public function details($movieId, $userId)
    {
    	$movieObject = DB::table('movie')->find($movieId);
    	$movieRate = DB::table('user_rate')->where('id_movie', $movieId)->where('id_user', $userId)->first()->rate;

    	$contentBasedObject = DB::table('movie')
    							->where('production_company', $movieObject->production_company)
    							->where('id', '!=', $movieId)
    							->get();

    	$minRate = ($movieRate - 1 < 0) ? 0 : $movieRate - 1;
    	$maxRate = ($movieRate + 1 > 5) ? 5 : $movieRate + 1;

    	$itemBasedCFObject = DB::table('user_rate')
    							->where('id_movie', $movieId)
    							->whereBetween('rate', [$minRate, $maxRate])
    							->groupBy('id_movie')
    							->value('id_movie');

    	$recommendedMovieObject = DB::table('movie')->whereIn('id', $recommendedMovieId)->get();

		return view('details', ['recommendedMovieObject' => $recommendedMovieObject, 'contentBasedObject' => $contentBasedObject]);
    }
}
