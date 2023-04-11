<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    use apiResponseTrait;
    /**--------------------------------------------------------------------------------------------------------|
     * getUserList: Retrieves a list of all users, regardless of whether they are soft-deleted or not.         |
     * createUser: Creates a new user and returns the user model.                                              |
     * getUserById: Retrieves a user by their ID and returns the user model.                                   |
     * uploadProfileImage: Uploads a profile image for the given user.                                         |
     * updateUser: Updates an existing user with the data from the request.                                    |
     * searchUsers: Searches for users with a given query and returns a collection of users.                   |
     * getUsersByStatus: Retrieves users based on their online status and returns a collection of users.
     * index: Retrieves a list of all active users.
     * store: Creates a new user based on the provided request data.
     * show: Retrieves a specific user based on their ID.
     * update: Updates a specific user based on their ID and the provided request data.
     * search: Searches for users based on a provided query string.
     * destroy: Soft-deletes a specific user based on their ID.
     */


    /**
     * Retrieve a list of users for a given page and page size
     *
     * @param int $page The current page number
     * @param int $perPage The number of users to display per page
     * @return Illuminate\Support\Collection The list of users for the current page
     */
    private function getUserList($page, $perPage)
    {
        // Calculate the offset based on the current page and page size
        $offset = ($page - 1) * $perPage;

        // Check if the user data is cached, and retrieve it if available
        if (Cache::has('users')) {
            $data = Cache::get('users');
        } else { // Otherwise, retrieve the user data from the database and cache it
            $data = DB::table('users')
                ->select('id', 'uuid', 'name', 'email', 'profile_img', 'is_online', 'created_at', 'deleted_at')
                ->whereNull('deleted_at')
                ->get();
            Cache::put('users', $data);
        }

        // Retrieve the users for the current page using the offset and page size
        $users = $data->slice($offset, $perPage);

        // Return the list of users for the current page
        return $users;
    }

    // This function creates a new user and returns the user model
    private function createUser(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return $user;
    }

    // This function retrieves a user by their ID and returns the user model
    private function getUserById($uuid)
    {
        return User::where('uuid', $uuid)
            ->whereNull('deleted_at')
            ->firstOrFail(['id', 'uuid', 'name', 'email', 'profile_img', 'created_at', 'updated_at', 'deleted_at']);
    }

    // This function uploads a profile image for the given user
    private function uploadProfileImage(User $user, $file)
    {
        $filename = $user->id . '_' . $file->getClientOriginalName();
        $file->storeAs('public/profile_images', $filename);
        $user->profile_img = $filename;
    }

    // This function updates an existing user with the data from the request
    private function updateUser(Request $request, User $user)
    {
        // Handle profile image upload
        if ($request->hasFile('profile_img')) {
            $this->uploadProfileImage($user, $request->file('profile_img'));
        }

        $user->update([
            'name' => $request->name,
            'profile_img' => $user->profile_img,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    // This function searches for users with a given query and returns a collection of users
    private function searchUsers($query)
    {
        return User::where('name', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->get(['id', 'uuid', 'name', 'email', 'profile_img', 'created_at', 'updated_at', 'deleted_at', 'is_online']);
    }

    // This function retrieves users based on their online status and returns a collection of users
    private function getUsersByStatus($isActive)
    {
        return User::where('is_online', $isActive)
            ->get(['id', 'uuid', 'name', 'email', 'profile_img', 'created_at', 'updated_at', 'deleted_at', 'is_online']);
    }

    /**
     * Retrieves a list of all active users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        $users = $this->getUserList($page, $perPage);

        return $this->apiResponse('records', [], ($users), [], 200);
        /* return response()->json($users); */
    }

    /**
     * Creates a new user based on the provided request data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $this->createUser($request);
        Cache::forget('users');

        return $this->apiResponse('Record created', [], ($user), [], 200);
        /* return response()->json(['Hello ' . $user->name . ' in your home!'], 201); */
    }

    /**
     * Retrieves a specific user based on their ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($uuid)
    {
        $user = $this->getUserById($uuid);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return $this->apiResponse('Record', [], ($user), [], 200);
        /* return response()->json($user); */
    }

    /**
     * Updates a specific user based on their ID and the provided request data.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $uuid)
    {
        $user = $this->getUserById($uuid);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $this->updateUser($request, $user);
        Cache::forget('users');

        return $this->apiResponse('Record updated', [], ($user), [], 201);
        /* return response()->json($user); */
    }

    /**
     * Searches for users based on a provided query string.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $users = $this->searchUsers($query);

        return $this->apiResponse('Search by name & email', [], ($users), [], 201);
        /* return response()->json($users); */
    }

    /**
     * Soft-deletes a specific user based on their ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($uuid)
    {
        $user = $this->getUserById($uuid);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        Cache::forget('users');

        return $this->apiResponse('Record soft deleted successfully', [], [], 201);
        /* return response()->json(['message' => 'User soft deleted successfully']); */
    }

    /**
     * Retrieves a list of all soft-deleted users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSoftDeletedUsers()
    {
        $users = User::onlyTrashed()
            ->get(['id', 'name', 'email', 'profile_img', 'is_online', 'created_at', 'deleted_at']);

        return $this->apiResponse('Recycle Bin', [], ($users), [], 201);
        /* return response()->json($users); */
    }

    /**
     * This function retrieves a list of users based on their active status,
     * where the default status is active.
     * It takes a request object as input, which contains the active status parameter,
     * and returns a JSON response with the list of users.
     */
    public function getUsersByActiveStatus(Request $request)
    {
        $isActive = $request->input('active', true);
        $users = $this->getUsersByStatus($isActive);

        return $this->apiResponse('Active Users', [], ($users), [], 201);
        /* return response()->json($users); */
    }
}
