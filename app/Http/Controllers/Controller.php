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
    		$a = array();
    		$b = array();

    		foreach ($user as $dimension) {
    			$a[] = $dimension[0];
    			$b[] = $dimension[1];
    		}

    		// var_dump($a); echo "<br>";
    		// var_dump($b); echo "<br>";
    		$cosineDistance = $this->dot($a, $b) / (sqrt($this->dot($a, $a)) * sqrt($this->dot($b, $b)));

			// echo $cosineDistance . " " . $key . "<br>";
    		$condition1 = $cosineDistance >= $nearestCosine && count($a) >= $biggestRate;
    		$condition2 = count($a) > $biggestRate && ($nearestCosine - $cosineDistance) <= 0.1;
    		if($condition1 || $condition2) {
    			$nearestCosine = $cosineDistance;
    			$nearestUser = $key;
    			$biggestRate = count($a);
    			// echo "updated : " . $nearestCosine . " ". $nearestUser . "<br>";
    		}
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

    	$recommendedMovieArray = array();
    	$cnt = 0;
    	foreach ($recommendedMovieObject as $movie) {
    		$recommendedMovieArray[$cnt]['id'] = $movie->id;
    		$recommendedMovieArray[$cnt]['name'] = $movie->name;
    		$recommendedMovieArray[$cnt]['description'] = $movie->description;
    		$recommendedMovieArray[$cnt]['image'] = $movie->image;
    		$recommendedMovieArray[$cnt]['production_company'] = $movie->production_company;
    		$cnt++;
    	}

		$recommendedMovieArray['cnt'] = (count($recommendedMovieArray) - 3 > 0 ? count($recommendedMovieObject) - 3 : 1);

    	// echo "<pre>";
    	// var_dump($recommendedMovieArray);
    	// echo "</pre>";


    	return view('welcome', ['recommendedMovieArray' => $recommendedMovieArray]);
    }

    public function details($movieId)
    {
    	$userId = $this->getUser();
    	$movieObject = DB::table('movie')->find($movieId);
    	$allMovies = DB::table('user_rate')->where('id_movie', $movieId)->get();

    	$avgRate = 0;
    	foreach ($allMovies as $movie) {
    		$avgRate += $movie->rate;
    	}
    	$avgRate /= count($allMovies);

    	$contentBasedObject = DB::table('movie')
    							->where('production_company', $movieObject->production_company)
    							->where('id', '!=', $movieId)
    							->get();

    	$minRate = ($avgRate - 1 < 0) ? 0 : $avgRate - 1;
    	$maxRate = ($avgRate + 1 > 5) ? 5 : $avgRate + 1;

    	$itemBasedCFObject = DB::table('user_rate')
    							->whereBetween('rate', [$minRate, $maxRate])
    							->groupBy('id_movie')
    							->having('id_movie', '!=', $movieId)
    							->get();

    	$itemBasedCFArray = array();
    	foreach ($itemBasedCFObject as $userRate) {
    		$itemBasedCFArray[] = $userRate->id_movie;
    	}

    	$recommendedMovieObject = DB::table('movie')->whereIn('id', $itemBasedCFArray)->get();

		return view('details', [
			'movieObject' => $movieObject,
			'recommendedMovieObject' => $recommendedMovieObject, 
			'contentBasedObject' => $contentBasedObject,
			'avgRate' => $avgRate
		]);
    }
}
