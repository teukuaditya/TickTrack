<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TicketStoreRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Ticket::query();
            $query->orderBy('created_at', 'desc');

                if ($request->search) {
                    $query->where('code', 'like', '%'.$request->search.'%')
                        ->orWhere('title', 'like', '%'.$request->search.'%');
                }
                if ($request->status) {
                    $query->where('status', $request->status);
                }
                if ($request->priority) {
                    $query->where('priority', $request->priority);
                }
                if (auth()->user()->role == 'user') {
                    $query->where('user_id', auth()->user()->id);
                }


                $tickets = $query->get();

            return response()->json([
                'message' => 'Tickets retrieved successfully',
                'data' => TicketResource::collection($tickets)
            ], 200);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve tickets',
                'data' => null,
            ], 500);
        }
    }


    public function store(TicketStoreRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $ticket = new Ticket();
            $ticket->user_id = auth()->id();
            $ticket->code = 'TIC-'.rand(1000, 9999);
            $ticket->title = $data['title'];
            $ticket->description = $data['description'];
            $ticket->priority = $data['priority'];
            $ticket->save();
            DB::commit();

            return response()->json([
                'message' => 'Ticket created successfully', 
                'data' => new TicketResource($ticket)
            ], 201);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create ticket', 'data' => null], 500);
        }
    }
}
