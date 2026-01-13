<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of messages/conversations.
     */
    public function index()
    {
        // TODO: Fetch real conversations from database
        $conversations = [
            [
                'id' => 1,
                'name' => 'Pak Budi',
                'last_message' => 'Untuk tugas besok, jangan lupa kerjakan latihan soal halaman 45',
                'last_message_time' => '10 menit yang lalu',
                'unread_count' => 2,
                'avatar' => null,
                'online' => true,
            ],
            [
                'id' => 2,
                'name' => 'Bu Sarah',
                'last_message' => 'Terima kasih atas partisipasinya di kelas hari ini',
                'last_message_time' => '1 jam yang lalu',
                'unread_count' => 0,
                'avatar' => null,
                'online' => false,
            ],
            [
                'id' => 3,
                'name' => 'Grup Kelas XII IPA 1',
                'last_message' => 'Ahmad: Ada yang bisa bantu soal nomor 5?',
                'last_message_time' => '2 jam yang lalu',
                'unread_count' => 5,
                'avatar' => null,
                'online' => null,
                'is_group' => true,
            ],
            [
                'id' => 4,
                'name' => 'Pak Ahmad',
                'last_message' => 'Selamat! Nilai quiz kamu sangat bagus',
                'last_message_time' => '1 hari yang lalu',
                'unread_count' => 0,
                'avatar' => null,
                'online' => false,
            ],
        ];

        return view('messages.index', compact('conversations'));
    }

    /**
     * Display the specified conversation.
     */
    public function show($id)
    {
        // TODO: Fetch real conversation from database
        $conversation = [
            'id' => $id,
            'name' => 'Pak Budi',
            'avatar' => null,
            'online' => true,
        ];

        $messages = [
            [
                'id' => 1,
                'sender' => 'other',
                'message' => 'Halo, bagaimana kabar belajarnya?',
                'time' => '09:30',
                'read' => true,
            ],
            [
                'id' => 2,
                'sender' => 'me',
                'message' => 'Baik Pak, saya sudah menyelesaikan materi fungsi kuadrat',
                'time' => '09:32',
                'read' => true,
            ],
            [
                'id' => 3,
                'sender' => 'other',
                'message' => 'Bagus sekali! Untuk tugas besok, jangan lupa kerjakan latihan soal halaman 45',
                'time' => '09:35',
                'read' => true,
            ],
        ];

        return view('messages.show', compact('conversation', 'messages'));
    }
}
