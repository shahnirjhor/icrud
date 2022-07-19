<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class UserController
 * @package App\Http\Controllers
 * @category Controller
 */
class UserController extends Controller
{
    /**
     * Show the form for creating a new resource
     *
     * @access public
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('page.signup');
    }

    /**
     * Store a newly created resource in storage
     *
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:password_confirmation'
        ]);
        $input = array();
        $input['name'] = $request->name;
        $input['email'] = $request->email;
        $input['password'] = bcrypt($request->password);
        $input['status'] = "1";
        $user = User::create($input);
        $user->assignRole("1");
        return redirect()->route('login')->with('success', trans('User Created Successfully'));
    }
}
