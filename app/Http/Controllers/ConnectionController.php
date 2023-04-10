<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ConnectionController extends Controller
{
    public function index()
    {
        // Retrieve all connections for the authenticated user
        $connections = Auth::user()->connections;

        return response()->json([
            'connections' => $connections,
        ]);
    }

    public function sendRequest(Request $request, $id)
    {
        // Validate that the recipient exists and is not already connected with the authenticated user
        $recipient = User::findOrFail($id);
        $authenticatedUser = $request->user();

        // Prevent the user from sending a connection request to themselves
        if ($recipient->id === $authenticatedUser->id) {
            return response()->json(['error' => 'Cannot send connection request to yourself'], 400);
        }

        $connection = Connection::create([
            'user_id' => $authenticatedUser->id,
            'connection_id' => $recipient->id,
        ]);

        return response()->json([
            'connection' => $connection,
        ]);
    }

    public function acceptRequest(Request $request, $id)
    {
        // Validate that the connection exists and the authenticated user is the recipient
        $connection = Connection::findOrFail($id);
        $recipient = $request->user();

        if ($connection->connection_id !== $recipient->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->update([
            'status' => Connection::STATUS_ACCEPTED,
        ]);

        return response()->json([
            'connection' => $connection,
        ]);
    }

    public function rejectRequest(Request $request, $id)
    {
        // Validate that the connection exists and the authenticated user is the recipient
        $connection = Connection::findOrFail($id);
        $recipient = $request->user();

        if ($connection->connection_id !== $recipient->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->update([
            'status' => Connection::STATUS_REJECTED,
        ]);

        return response()->json([
            'connection' => $connection,
        ]);
    }

    public function blockConnection(Request $request, $id)
    {
        // Validate that the connection exists and the authenticated user is the recipient
        $connection = Connection::findOrFail($id);
        $recipient = $request->user();

        if ($connection->connection_id !== $recipient->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->update([
            'status' => Connection::STATUS_BLOCKED,
        ]);

        return response()->json([
            'connection' => $connection,
        ]);
    }

    public function myFriends(Request $request)
    {
        $authUserId = auth()->user()->id;

        $friends = Connection::where(function ($query) use ($authUserId) {
            $query->where('user_id', $authUserId)
                ->orWhere('connection_id', $authUserId);
        })
            ->where('status', 'accepted')
            ->get();

        return response()->json([
            'friends' => $friends,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // Validate that the connection exists and the authenticated user is part of the connection
        $connection = Connection::findOrFail($id);
        $authenticatedUser = $request->user();

        if ($connection->user_id !== $authenticatedUser->id && $connection->recipient_id !== $authenticatedUser->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->delete();

        return response(null, 204);
    }
}
