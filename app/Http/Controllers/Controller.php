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
		
		
		$topRatedMovie = DB::table('user_rate as ur')
		->selectRaw('m.id,m.name,round(avg(ur.rate),2) as average_rate,m.image,m.imageL')	
		->join('movie as m', 'ur.id_movie', '=', 'm.id')
		->groupBy('m.id')
		->orderByRaw('avg(ur.rate) desc')
		->limit(5)
		->get();


		$mostRatedMovie = DB::table('user_rate as ur')
		->selectRaw('m.id,m.name,count(ur.id) as rate_count,m.image')	
		->join('movie as m', 'ur.id_movie', '=', 'm.id')
		->groupBy('m.id')
		->orderByRaw('count(ur.id) desc')
		->limit(1)
		->get();

		// echo "<pre>";
		// var_dump($mostRatedMovie);
    	// echo "<pre>";
    	// var_dump($recommendedMovieArray);
    	// echo "</pre>";


		return view('welcome', [
			'recommendedMovieArray' => $recommendedMovieArray,
			'topRatedMovie' => $topRatedMovie,
			'mostRatedMovie' => $mostRatedMovie,
		]);
    }
	public function itembase($movieId)
	{
		$userId=$this->getUser();
		// $movieIRate=DB::table('movie as m')
		// 			->selectRaw('m.id')
		// 			->join('user_rate as ur','m.id','=','ur.id_movie')
		// 			->where('ur.id_user', $userId)
		// 			->get();
		$movieIRateArray=array();
		// foreach($movieIRate as $tmp)
		// {
		// 	$movieIRateArray[]=$tmp->id;
		// }
		$movieIRateArray[]=$movieId;
		$userRateSame=DB::table('movie as m')
				->selectRaw('ur.id_user')
				->join('user_rate as ur','m.id','=','ur.id_movie')
				->wherein('m.id', $movieIRateArray)
				->groupBy('ur.id_user')
				->get();
		$userRateSameArray=array();
		foreach($userRateSame as $tmp)
		{
			$userRateSameArray[]=$tmp->id_user;
		}

		//echo "<pre>";
		// var_dump($userRateSameArray);
		// echo "<br>";
		// var_dump($movieIRateArray);
		$movieRecommenedCount=DB::table('movie as m')
				->selectRaw('m.id,m.name,m.image,m.description,m.production_company,count(m.id) cnt')
				->join('user_rate as ur','m.id','=','ur.id_movie')
				->whereIn('ur.id_user', $userRateSameArray)
				->whereNotIn('m.id',$movieIRateArray)
				->groupBy('m.id')
				->orderByRaw('count(m.id),m.name desc')
				->get();
		

		//var_dump($movieRecommenedCount);
		$movieRecommenedArray=array();
		$idx=0;
		foreach($movieRecommenedCount as $tmp)
		{
			$movieRecommenedArray[$idx]['id']=$tmp->id;
			$movieRecommenedArray[$idx]['name']=$tmp->name;
			$movieRecommenedArray[$idx]['image']=$tmp->image;
			$movieRecommenedArray[$idx]['description']=$tmp->description;
			$movieRecommenedArray[$idx]['production_company']=$tmp->production_company;
			$movieRecommenedArray[$idx]['cnt']=$tmp->cnt;//for how many people rate that film
			$idx++;
		}

		// echo '<pre>';
		// var_dump($movieRecommenedArray);
		// die();
		return $movieRecommenedArray;
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
		$recommendedMovieArray=array();
		$idx=0;
		foreach ($recommendedMovieObject as $movie)
		{
			$recommendedMovieArray[$idx]['id'] = $movie->id;
			
			$recommendedMovieArray[$idx]['name'] = $movie->name;
			$recommendedMovieArray[$idx]['description'] = $movie->description;
			$recommendedMovieArray[$idx]['image'] = $movie->image;
			$recommendedMovieArray[$idx]['production_company'] = $movie->production_company;
			$idx++;
		} 
		$recommendedMovieArray['cnt'] = count($recommendedMovieObject)<4? 1:count($recommendedMovieObject)-3;
		// echo "<pre>";
		// var_dump($recommendedMovieArray);die();

		// return view('details', [
		// 	'movieObject' => $movieObject,
		// 	'recommendedMovieArray' => $recommendedMovieArray, 
		// 	'contentBasedObject' => $contentBasedObject,
		// 	'avgRate' => $avgRate
		// ]);
		$felixItemBase=$this->itembase($movieId);
		$felixItemBase['cnt'] = count($felixItemBase)<4? 1:count($felixItemBase)-3;
		return view('details', [
			'movieObject' => $movieObject,
			'recommendedMovieArray' => $felixItemBase, 
			'contentBasedObject' => $contentBasedObject,
			'avgRate' => $avgRate
		]);
	}
	
}
