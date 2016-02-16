<?php

namespace App\Http\Controllers;

use App\Counter;
use App\Countercategory;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CounterRepository;
use DateTime;
use App\Session;
use DB;

class CounterController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $counters;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(CounterRepository $counters)
    {
        $this->middleware('auth');

        $this->counters = $counters;
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
    	
    	//get basic objects 
    	$user = User::find($request->user()->id);
    	$categories = Countercategory::All(['id', 'name']);
    	
    	//handle categories filter
    	if ($request->category_id)
    		if ($request->category_id > 0) {
    		$user->counter_category_id = $request->category_id;
    		$user->counter_category = Countercategory::find($request->category_id)->name;
    		$user->save();
    	}
    	else {
    		$user->counter_category_id = False;
    		$user->counter_category = "All Categories";
    		$user->save();
    	}
    	$ses_category_id = $user->counter_category_id;
    	
    	//base query
    	$counters = DB::table('counters')
    	->leftjoin('countercategories', 'counters.counter_category_id', '=', 'countercategories.id')
    	//->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
    	->select(
    			'counters.id',
    			'counters.date',
    			'counters.calories',
    			'counters.distance',
    			'counters.counter_category_id',
    			'counters.created_at',
    			'counters.updated_at',
    			'countercategories.name as cname',
    			'countercategories.css_class'
    	);
    	
    	//handle categories
    	if ($ses_category_id)
    		$counters->where('counter_category_id', '=', $ses_category_id);
    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('counter_order', $request->order);
    	$order = $request->session()->get('counter_order');
    	if (!$order)
    		$order = 'date';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('counter_dir', $request->dir);
    	$dir = $request->session()->get('counter_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('counter_page', $request->page);
    	$page = $request->session()->get('counter_page');
    	
    	$counters = $counters->orderBy($order, $dir)->paginate(50);
    	
        return view('counters.index', [
        	'counters' => $counters,
        	'categories' => $categories,
        	'order' => $order,
        	'dir' => $dir,
        	'page' => $page,
        	'category' => $user->counter_category,
        ]);
        
    }
    
    
    /**
     * Create a new task: load date and forward to view
     *
     * @param  Request  $request
     * @return view
     */
    public function create(Request $request) {
    	 
    	$user = User::find($request->user()->id);
    	$countercategories = Countercategory::All(['id', 'name']);
    	
    	return view('counters.update', [
    			'countercategories' => $countercategories,
    			'counter_category_id' => $user->counter_category_id,
    			])->withCounter(new Counter());
    
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function update(Request $request, Counter $counter) {
    	
    	$countercategories = Countercategory::All(['id', 'name']);

    	return view('counters.update', [
    			'countercategories' => $countercategories,
				'counter' => $counter,
    			'counter_category_id' => False,
    			])->withCounter($counter);
    }
    
    
    /**
     * Validate AND Save/Crate a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
    	
    	$date = False;
    	if ($request->date) {
    		$date = DateTime::createFromFormat('d.m.Y', $request->date);
    		$date = $date->format('Y-m-d');
    	}
    
    	$input = array(
    			'date' => $date,
    			'counter_category_id' => $request->category,
    	);
    
    	if ($request->counter_id) {
    		 
    		$counter = Counter::find($request->counter_id);
    		$counter->fill($input)->save();
    		$request->session()->flash('alert-success', 'Counter was successful updated!');
    		 
    	}
    	else {
    		 
    		$counter = new Counter();
    		$counter->create($input);
    		$request->session()->flash('alert-success', 'Counter was successful added!');
    		 
    	}
    
    	$page = $request->session()->get('counter_page');
    	 
    	if ($request->save_edit)
    		return redirect('/counter/' . $counter->id . '/update');
    	else
    		return redirect('/counters?page=' . $page);
    }

    
    
    
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Counter $counter)
    {

    	$counter->delete();
    
    	$request->session()->flash('alert-success', 'Counter was successful deleted!');
    
    	return redirect('/counters');
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function stats(Request $request) {

    	$countercategories = Countercategory::All(['id', 'name']);
    	
    	$months = DB::table('counters')
    	-> select(DB::raw('distinct CONCAT(YEAR(date), "-", MONTH(date)) AS date'))
    	->orderby('date')->get();
    	
    	$counters = DB::table('counters')
    	->leftjoin('countercategories', 'counters.counter_category_id', '=', 'countercategories.id')
    	//->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
    	->select(
    			'counters.id as cid',
    			'countercategories.name as cname',
    			'countercategories.id as ccid',
    			//DB::raw('CONCAT( YEAR( counters.date ) , '-', MONTH( counters.date ) ) AS dateg')
    			DB::raw('count(counters.id) as items'),
    			DB::raw('CONCAT(YEAR(date), "-", MONTH(date)) AS condate')
    	)
    	//->groupBy('condate')
    	->groupBy('counters.counter_category_id')->get();
    	
    	//select 
//CONCAT( YEAR( date ) , '-', MONTH( date ) ) AS thedate

//, counter_category_id, count(id) from counters group by thedate, counter_category_id
    	
    	//select 
//distinct CONCAT( YEAR( date ) , '-', MONTH( date ) ) AS thedate from counters
    	
    	
    	//month: 2016-01, cat1: 10, cat2: 20
    	//month: 2016-02, cat1: 10, cat2: 20
    	
    	/*print "<pre>";
    	$m2 = array();
    	foreach ($months as $month) {
    		$m['date'] = $month->date;
    		foreach ($countercategories as $cat) {
    			$items = 0;
    			foreach ($counters as $c)
    				{
    					
    		
    				if ($c->condate == $month->date AND $c->ccid == $cat->id) {
    					$items = $c->items;
    					break;
    					}
    				}
    			$m[$cat->id] = $items;
    		}
    		array_push($m2, $m);
    	}
    	
    	foreach ($m2 as $m0) {
    		print_r($m0);
    	}*/

    	   
    	return view('counters.stats', [
    			'countercategories' => $countercategories,
    			'cats' => $counters
    			]);
    }
    
    
}
