<?php

namespace App\Http\Controllers;

use App\Task;
use App\Category;
use App\Priority;
use App\Stage;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use DateTime;
use App\Session;
use DB;

class TaskController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $tasks;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(TaskRepository $tasks)
    {
        $this->middleware('auth');

        $this->tasks = $tasks;
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
    	$categories = Category::All(['id', 'name']);
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);

    	
    	//handle stages filter
    	if ($request->stage_id)
    		if ($request->stage_id > 0) {
    			$user->stage_id = $request->stage_id;
	    		$user->stage = Stage::find($request->stage_id)->name;
	    		$user->save();
    		}
    		else {
    			$user->stage_id = False;
    			$user->stage = "--all Stages--";
    			$user->save();
    		}
    	
    	$ses_stage_id = $user->stage_id;
    	
    	//handle categories filter
    	if ($request->category_id)
    		if ($request->category_id > 0) {
	    		$user->category_id = $request->category_id;
	    		$user->category = Category::find($request->category_id)->name;
	    		$user->save();
	    	}
	    	else {
	    		$user->category_id = False;
	    		$user->category = "--all Categories--";
	    		$user->save();
    	}
    	$ses_category_id = $user->category_id;
    	
    	//base query
    	$tasks = DB::table('tasks')
    		->leftjoin('categories', 'tasks.category_id', '=', 'categories.id')
    		->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
    		->join('stages', 'tasks.stage_id', '=', 'stages.id')
    		->select(
    				'tasks.name', 
    				'tasks.deadline', 
    				'tasks.id',
    				'tasks.created_at',
    				'tasks.updated_at', 
    				'categories.name as cname', 
    				'categories.css_class', 
    				'priorities.name as pname', 
    				'stages.name as sname'
    				)
    		->where('user_id', '=', $request->user()->id);
    	
    	//handle stages
    	if ($ses_stage_id)
    		$tasks->where('stage_id', '=', $ses_stage_id);
    	
    	//handle categories
    	if ($ses_category_id)
    		$tasks->where('category_id', '=', $ses_category_id);
    	
    	//handle search
    	if ($request->btn_search == "s") {
    		if ($request->search) 
    			$request->session()->put('search', $request->search);
    		else
    			$request->session()->forget('search');
    	}
    	$search = $request->session()->get('search');
    	if (strlen($search) > 0)
    		$tasks->where('tasks.name', 'like', "%" . $search . "%");
    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('order', $request->order);
    	$order = $request->session()->get('order');
    	if (!$order)
    		$order = 'priority_id';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('dir', $request->dir);
    	$dir = $request->session()->get('dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('page', $request->page);
    	$page = $request->session()->get('page');
    	
    	$tasks = $tasks->orderBy($order, $dir)->orderBy('deadline', 'ASC')->paginate(50);
    	
        return view('tasks.index', [
        	'categories' => $categories,
        	'priorities' => $priorities,
        	'stages' => $stages,
            'tasks' => $tasks,
        	'order' => $order,
        	'dir' => $dir,
        	'stage' => $user->stage,
        	'category' => $user->category,
        	'search' => $search,
        	'page' => $page,
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
    	$categories = Category::All(['id', 'name']);
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);
    	
    	return view('tasks.update', [
    			'categories' => $categories,
    			'priorities' => $priorities,
    			'stages' => $stages,
    			'category_id' => $user->category_id,
    			])->withTask(new Task());
    	 
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function update(Request $request, Task $task) {
    
    	$categories = Category::All(['id', 'name']);
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);
    
    	return view('tasks.update', [
    			'categories' => $categories,
    			'priorities' => $priorities,
    			'stages' => $stages,
    			'task' => $task,
    			'category_id' => False,
    			])->withTask($task);
    }
    

    /**
     * Validate AND Save/Crate a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $deadline = False;
        if ($request->deadline) {
        	$deadline = DateTime::createFromFormat('d.m.Y', $request->deadline);
        	$deadline = $deadline->format('Y-m-d');
        }
        
        $input = array(
	            'name' => $request->name,
	        	'deadline' => $deadline,
	        	'description' => $request->description,
	        	'category_id' => $request->category,
	        	'priority_id' => $request->priority,
	        	'stage_id' => $request->stage,
	        );
        
        if ($request->task_id) {
        	
        	$task = Task::find($request->task_id);
        	$task->fill($input)->save();
        	$request->session()->flash('alert-success', 'Task was successful updated!');
        	
        	}
        else {
        	
	        $request->user()->tasks()->create($input);
	        $request->session()->flash('alert-success', 'Task was successful added!');
	        
	        }

	    $page = $request->session()->get('page');
        	
        return redirect('/tasks?page=' . $page);
    }
    
    

    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('destroy', $task);

        $task->delete();
        
        $request->session()->flash('alert-success', 'Task was successful deleted!');

        return redirect('/tasks');
    }
    
    
    /**
     * Set the given task to state 'done'.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function done(Request $request, Task $task)
    {
    	    
    	$task->stage_id = 3;
    	$task->save();
    	
    	$request->session()->flash('alert-success', 'Task was successful changed to stage done!');
    
    	return redirect('/tasks');
    }
    
}
