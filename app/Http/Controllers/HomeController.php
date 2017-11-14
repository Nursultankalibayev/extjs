<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function getTrainings(Request $request)
    {
        return view('trainings');
    }

    public function getTrainingsAjax(Request $request)
    {
        $action = $request['action'];
        switch ($action){
            case $action== 'get_results':

                $query = DB::table('trainings')
                    ->select(['id','title','description','trainer_id','city_id']);

                $sort = json_decode($request['sort']);
                $filter['column'] = $request['filter'][0]['field'];
                $filter['value'] = $request['filter'][0]['data']['value'];
                foreach ($sort  as $item) {
                    $query->orderBy($item->property,$item->direction);
                }

                if (sizeof($filter['column']) && sizeof($filter['value'])){
                    $query->where($filter["column"],'like',$filter["value"].'%');
                }

                $results = $query->paginate(10);
                return response()->json($results);
            break;
        }
    }
}
