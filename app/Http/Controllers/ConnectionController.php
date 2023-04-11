<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ConnectionController extends Controller
{
    use apiResponseTrait;
    public function index()
    {
        // Retrieve all connections for the authenticated user
        $authUserId = auth()->user()->id;

        $connections = Connection::where(function ($query) use ($authUserId) {
            $query->where('user_id', $authUserId)
                ->orWhere('connection_id', $authUserId);
        })
            ->get();

        return $this->apiResponse('connections', [], ($connections), [], 201);
        /* return response()->json([
            'connections' => $connections,
        ]); */
    }

    public function sendRequest(Request $request, $user_uuid)
    {
        // Validate that the recipient exists and is not already connected with the authenticated user
        $recipient = User::where('uuid', $user_uuid)->first();
        $authenticatedUser = $request->user();

        // Prevent the user from sending a connection request to themselves
        if ($recipient->id === $authenticatedUser->id) {
            return response()->json(['error' => 'Cannot send connection request to yourself'], 400);
        }

        // Check if a connection request has already been sent from the authenticated user to the recipient
        if (Connection::where('user_id', $authenticatedUser->id)->where('connection_id', $recipient->id)->exists()) {
            $connection = Connection::where('user_id', $authenticatedUser->id)->where('connection_id', $recipient->id)->first();

            if ($connection->status === 'requested') {
                return response()->json(['error' => 'Connection request already sent to this user'], 400);
            } elseif ($connection->status === 'accepted') {
                return response()->json(['error' => 'You are already friends!'], 400);
            } elseif ($connection->status === 'rejected') {
                $connection->update([
                    'status' => Connection::STATUS_REQUESTED,
                ]);
                return $this->apiResponse('Send Request', [], ($connection), [], 201);
            }
        }

        // Check if a connection request has already been sent from the recipient to the authenticated user
        if (Connection::where('user_id', $recipient->id)->where('connection_id', $authenticatedUser->id)->exists()) {
            $connection = Connection::where('user_id', $recipient->id)->where('connection_id', $authenticatedUser->id)->first();

            if ($connection->status === 'requested') {
                return response()->json(['error' => 'You have already received a connection request from this user'], 400);
            } elseif ($connection->status === 'accepted') {
                return response()->json(['error' => 'You are already friends!'], 400);
            } elseif ($connection->status === 'rejected') {
                $connection->update([
                    'status' => Connection::STATUS_REQUESTED,
                ]);
                return $this->apiResponse('Send Request', [], ($connection), [], 201);
            }
        }

        $connection = Connection::create([
            'user_id' => $authenticatedUser->id,
            'connection_id' => $recipient->id,
        ]);

        return $this->apiResponse('Send Request', [], ($connection), [], 201);
    }

    public function acceptRequest(Request $request, $connection_uuid)
    {
        // Validate that the connection exists and the authenticated user is the recipient
        $connection = Connection::where('uuid', $connection_uuid)->first();
        $recipient = $request->user();

        if ($connection->connection_id !== $recipient->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->update([
            'status' => Connection::STATUS_ACCEPTED,
        ]);

        return $this->apiResponse('Accepted Request', [], ($connection), [], 201);
        /* return response()->json([
            'connection' => $connection,
        ]); */
    }

    public function rejectRequest(Request $request, $connection_uuid)
    {
        // Validate that the connection exists and the authenticated user is the recipient
        $connection = Connection::where('uuid', $connection_uuid)->first();
        $recipient = $request->user();

        if ($connection->connection_id !== $recipient->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->update([
            'status' => Connection::STATUS_REJECTED,
        ]);

        return $this->apiResponse('Rejected Request', [], ($connection), [], 201);
        /* return response()->json([
            'connection' => $connection,
        ]); */
    }

    public function blockConnection(Request $request, $connection_uuid)
    {
        // Validate that the connection exists and the authenticated user is the recipient
        $connection = Connection::where('uuid', $connection_uuid)->first();
        $recipient = $request->user();

        if ($connection->connection_id !== $recipient->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->update([
            'status' => Connection::STATUS_BLOCKED,
        ]);

        return $this->apiResponse('Blocked Request', [], ($connection), [], 201);
        /* return response()->json([
            'connection' => $connection,
        ]); */
    }

    public function myFriends()
    {
        $authUserId = auth()->user()->id;

        $friends = Connection::where(function ($query) use ($authUserId) {
            $query->where('user_id', $authUserId)
                ->orWhere('connection_id', $authUserId);
        })
            ->where('status', 'accepted')
            ->get();

        return $this->apiResponse('My friends', [], ($friends), [], 201);
        /* return response()->json([
            'friends' => $friends,
        ]); */
    }

    public function destroy(Request $request, $connection_uuid)
    {
        // Validate that the connection exists and the authenticated user is part of the connection
        $connection = Connection::where('uuid', $connection_uuid)->first();
        $authenticatedUser = $request->user();

        if ($connection->user_id !== $authenticatedUser->id && $connection->connection_id !== $authenticatedUser->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $connection->delete();

        return $this->apiResponse('Request deleted successfuly.', [], ($connection), [], 201);
        /* return response(null, 204); */
    }
}
