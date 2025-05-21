@extends('layouts.app')

@section('title', 'My Borrowed Books')

@section('content')
<div class="p-4 md:p-6">
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
            <h2 class="text-xl font-bold text-gray-800">My Borrowed Books</h2>
            {{-- ... (link Browse More Books) ... --}}
        </div>

        @if(isset($borrowings) && $borrowings->count() > 0)
        <div class="table-responsive rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cover</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Borrowed</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Due Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Fine</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($borrowings as $borrowing)
                    <tr class="hover:bg-gray-50 transition-colors {{ $borrowing->isOverdue() && $borrowing->status !== 'returned' ? 'bg-red-50 hover:bg-red-100' : '' }}">
                        {{-- ... (kolom Cover, Book Title, Borrowed, Due Date) ... --}}
                        <td class="px-4 py-3">
                            <div class="h-16 w-12 flex-shrink-0 rounded-md bg-gray-100 flex items-center justify-center overflow-hidden shadow-sm">
                                @if($borrowing->book->cover_image_path && Storage::disk('public')->exists($borrowing->book->cover_image_path))
                                    <img src="{{ asset('storage/' . $borrowing->book->cover_image_path) }}" alt="{{ $borrowing->book->title }}" class="h-full w-full object-cover">
                                @else
                                    <i class="ri-image-line text-gray-400 text-2xl"></i>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                <a href="{{ route('books.show', $borrowing->book->id) }}">{{ Str::limit($borrowing->book->title, 35) }}</a>
                            </div>
                            <div class="text-xs text-gray-500">{{ $borrowing->book->category?->name ?? 'Uncategorized' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">
                            {{ $borrowing->borrowed_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">
                            <span class="{{ $borrowing->isOverdue() && $borrowing->status !== 'returned' ? 'text-red-600 font-semibold' : '' }}">
                                {{ $borrowing->due_date->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($borrowing->status === 'overdue')
                                    bg-red-100 text-red-800
                                @elseif($borrowing->status === 'borrowed' && $borrowing->isOverdue())
                                    bg-red-100 text-red-800  {{-- Tetap merah jika borrowed tapi sudah overdue --}}
                                @elseif($borrowing->status === 'borrowed')
                                    bg-yellow-100 text-yellow-800
                                @elseif($borrowing->status === 'returned')
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($borrowing->status) }}
                                @if($borrowing->isOverdue() && $borrowing->status !== 'returned')
                                    (Overdue)
                                @endif
                            </span>
                            @if($borrowing->status === 'returned' && $borrowing->returned_at)
                                <p class="text-xs text-gray-500 mt-1">On: {{ $borrowing->returned_at->format('M d, Y') }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 hidden sm:table-cell">
                            @if($borrowing->fine_amount > 0)
                                <span class="text-red-600 font-semibold">Rp {{ number_format($borrowing->fine_amount, 0, ',', '.') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            @if($borrowing->status === 'borrowed' || $borrowing->status === 'overdue')
                            <form id="returnForm-{{ $borrowing->id }}" action="{{ route('user.borrowings.return', $borrowing->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        onclick="confirmAction(event, 'returnForm-{{ $borrowing->id }}', 'Confirm Return', 'Are you sure you want to return this book: {{ addslashes($borrowing->book->title) }}?', 'Yes, return it!')"
                                        class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    Return Book
                                </button>
                            </form>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ... (Pagination) ... --}}
        @else
            {{-- ... (Pesan "You haven't borrowed any books yet.") ... --}}
        @endif
    </div>
</div>
@endsection