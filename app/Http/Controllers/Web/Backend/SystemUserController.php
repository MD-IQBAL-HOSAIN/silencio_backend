<?php
namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SystemUserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('can:user_create')->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $users = User::latest()->get();
        if ($request->ajax()) {
            return DataTables::of($users)
                ->addIndexColumn()

                 ->addColumn('avatar', function ($data) {
                    $avatar = $data->avatar ? asset($data->avatar) : asset('frontend/default-avatar-profile.jpg');
                    return '<img src="' . $avatar . '" width="60" alt=" User Image"/>';
                })

                ->addColumn('name', function ($user) {
                    return $user->name;
                })
                ->addColumn('email', function ($user) {
                    return $user->email;
                })
                ->addColumn('status', function ($data) {
                    $backgroundColor  = $data->status ? '#4CAF50' : '#ccc';
                    $sliderTranslateX = $data->status ? '26px' : '2px';

                    return getStatusHTML($data, $backgroundColor, $sliderTranslateX);
                })
                ->addColumn('action', function ($data) {
                    return '
                <button onclick="edit(' . $data->id . ')" type="button" class="btn btn-info btn-sm">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <button type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger btn-sm del">
                    <i class="mdi mdi-delete"></i>
                </button>
            ';
                })
                ->rawColumns(['avatar', 'status', 'action'])
                ->make(true);
        }
        return view('backend.layout.users.system_users.index');
    }
    public function create()
    {
        return view('backend.layout.users.system_users.form');
    }
    public function store(UserRequest $request)
    {
        // dd($request->all());
        $data = $request->validated();
        // dd($data);
        $user           = new User;
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->password = bcrypt($data['password']);

        if ($request->hasFile('avatar')) {
            $user->avatar = fileUpload(
                $request->file('avatar'),
                'avatars',
                'user-avatar-' . time()
            );
        }

        $user->save();
        return redirect()->route('system-user.index')->with('t-success', 'System User Successfully created');
    }

    public function edit(User $system_user)
    {

        return view('backend.layout.users.system_users.form', compact('system_user'));
    }

    public function update(Request $request, User $system_user)
    {

        // dd($request->all());
        $request->validate([
            'name'     => 'required',
            // 'email'=> 'required|email',
            'password' => [['nullable', new PasswordRule]],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,svg,webp,ico,bmp,tiff,avif,jfif,heic', 'max:2048'],
        ]);
        try {
            if (! is_null($request['password'])) {
                $system_user->password = bcrypt($request['password']);
                $system_user->update();
            }

            if ($request->hasFile('avatar')) {
                if (! empty($system_user->avatar)) {
                    fileDelete(public_path($system_user->avatar));
                }

                $system_user->avatar = fileUpload(
                    $request->file('avatar'),
                    'avatars',
                    'user-avatar-' . $system_user->id . '-' . time()
                );
            }

            $data = $request->only(['name', 'email']);
            $system_user->update($data);

        } catch (\Exception $e) {
            return redirect()->route('system-user.index')->with('error', 'System User Failed to Update,,,' . $e->getMessage());
        }
        return redirect()->route('system-user.index')->with('success', 'System User Successfully created');
    }

    public function status($id)
    {
        try {
            $system_user         = User::find($id);
            $system_user->status = ! $system_user->status;
            $system_user->update();

            return response()->json(['status' => 't-success', 'message', 'Status Changed Successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 't-error', 'message', 'Status Change Failed ...' . $e->getMessage()]);
        }
    }
    public function destroy(User $system_user)
    {
        try {
            if ($system_user->id == Auth::user()->id) {
                return response()->json(['status' => 'error', 'message', 'Can\'t delete own id ...']);
            }
            $system_user->delete();
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message', 'User delete Failed ...' . $e->getMessage()]);
        }
        return response()->json(['status' => 'success', 'message', 'User deleted Successfully']);
    }
}
