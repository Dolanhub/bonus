<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
     //index
     public function index(Request $request)
     {
         $users = DB::table('users')
         ->when($request->input('search'), function ($query, $search) {
            return $query->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhere('role', 'like', '%' . $search . '%');
            });
        })
             ->orderBy('id', 'desc')
             ->paginate(10);
         return view('pages.users.index', compact('users'));
     }

     //create
     public function create()
     {
         return view('pages.users.create');
     }

     //store
     public function store(Request $request)
     {
         $request->validate([
             'name' => 'required',
             'email' => 'required|email',
             'role' => 'required',
             'password' => 'required',
         ]);

         $user = new User();
         $user->name = $request->name;
         $user->email = $request->email;
         $user->role = $request->role;
         $user->password = Hash::make($request->password);
         $user->save();

         return redirect()->route('users.index')->with('success', 'User created successfully.');
     }

     //show
    //  public function show($id)
    //  {
    //      $user = User::all();
    //     //  return view('pages.users.index', compact('user'));
    //     return redirect()->route('users.index')->with('success', 'Download successfully.');
    //  }

     //edit
     public function edit($id)
     {
         $user = User::find($id);
         return view('pages.users.edit', compact('user'));
     }

     //update
     public function update(Request $request, $id)
     {
         $request->validate([
             'name' => 'required',
             'email' => 'required|email',
             'role' => 'required',
         ]);

         $user = User::find($id);
         $user->name = $request->name;
         $user->email = $request->email;
         $user->role = $request->role;
         if ($request->password) {
             $user->password = Hash::make($request->password);
         }
         $user->save();

         return redirect()->route('users.index')->with('success', 'User updated successfully.');
     }

     //destroy
     public function destroy($id)
     {
         $user = User::find($id);
         $user->delete();

         return redirect()->route('users.index')->with('success', 'User deleted successfully.');
     }



    public function export(Request $request)
    {

        return Excel::download(new UsersExport($request), 'Users.csv', \Maatwebsite\Excel\Excel::CSV);

    }

}
